<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../config/db.php";

class BookingController {
    
    public static function store() {
        global $conn;
        
        try {
            // Validasi login
            if (!isset($_SESSION['user'])) {
                throw new Exception("Silakan login terlebih dahulu.");
            }

            // Validasi input
            $user_id = intval($_POST['user_id'] ?? 0);
            $car_id = intval($_POST['car_id'] ?? 0);
            $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
            $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

            // Validasi field tidak kosong
            if (!$user_id || !$car_id || !$start_date || !$end_date) {
                throw new Exception("Data tidak lengkap");
            }

            // Validasi file upload
            if (!isset($_FILES['proof']) || $_FILES['proof']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("File bukti pembayaran harus diupload");
            }

            $file = $_FILES['proof'];

            // Validasi mobil ada dan tersedia
            $query = "SELECT * FROM cars WHERE car_id = $car_id LIMIT 1";
            $res = mysqli_query($conn, $query);
            
            if (!$res) {
                throw new Exception("Error: Mobil tidak ditemukan");
            }

            if (mysqli_num_rows($res) === 0) {
                throw new Exception("Mobil tidak ditemukan");
            }

            $car = mysqli_fetch_assoc($res);

            if ($car['status'] !== 'available') {
                throw new Exception("Mobil tidak tersedia untuk booking");
            }

            // Upload file
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '_' . time() . '.' . $ext;
            $upload_dir = __DIR__ . '/../../public/uploads/proofs/';

            // Cek apakah folder ada
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $filepath = $upload_dir . $filename;

            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                throw new Exception("Gagal upload bukti pembayaran");
            }

            // Hitung total harga
            $start_obj = new DateTime($start_date);
            $end_obj = new DateTime($end_date);
            $durasi = $end_obj->diff($start_obj)->days;
            $total_price = $durasi * $car['price_per_day'];

            // Insert booking
            $start_date_escaped = mysqli_real_escape_string($conn, $start_date);
            $end_date_escaped = mysqli_real_escape_string($conn, $end_date);
            $filename_escaped = mysqli_real_escape_string($conn, $filename);

            // 1. Insert ke tabel bookings
            $insert_query = "INSERT INTO bookings (user_id, car_id, start_date, end_date, total_price, status, created_at)
                            VALUES ($user_id, $car_id, '$start_date_escaped', '$end_date_escaped', $total_price, 'pending', NOW())";
            
            $insert_res = mysqli_query($conn, $insert_query);

            if (!$insert_res) {
                // Hapus file yang sudah diupload jika gagal insert booking
                if (file_exists($filepath)) {
                    unlink($filepath);
                }
                throw new Exception("Gagal menyimpan booking: " . mysqli_error($conn));
            }

            $booking_id = mysqli_insert_id($conn);

            // 2. Insert bukti pembayaran
            $proof_query = "INSERT INTO payment_proof (booking_id, image_path, uploaded_at)
                        VALUES ($booking_id, '$filename_escaped', NOW())";
            
            $proof_res = mysqli_query($conn, $proof_query);

            if (!$proof_res) {
                // Hapus booking dan file jika gagal insert proof
                mysqli_query($conn, "DELETE FROM bookings WHERE booking_id = $booking_id");
                if (file_exists($filepath)) {
                    unlink($filepath);
                }
                throw new Exception("Gagal menyimpan bukti pembayaran: " . mysqli_error($conn));
            }

            // 3. Update status mobil
            $update_car_query = "UPDATE cars SET status = 'rented' WHERE car_id = $car_id";
            $update_car_res = mysqli_query($conn, $update_car_query);

            if (!$update_car_res) {
                throw new Exception("Gagal update status mobil");
            }

            // Set success message
            $_SESSION['success'] = "Booking berhasil dibuat! ID Booking: #" . $booking_id . ". Menunggu konfirmasi admin.";
            
            // Redirect ke booking history
            header("Location: ../../public/booking_history.php");
            exit;

        } catch (Exception $e) {
            // Set error message
            $_SESSION['error'] = $e->getMessage();
            
            // Redirect kembali ke halaman booking
            header("Location: ../../public/booking.php?car_id=" . ($_POST['car_id'] ?? ''));
            exit;
        }
    }
}

// Handle action
$action = isset($_GET['action']) ? $_GET['action'] : 'store';

if ($action === 'store') {
    BookingController::store();
} else {
    $_SESSION['error'] = "Action tidak dikenali";
    header("Location: ../../public/cars.php");
    exit;
}
?>