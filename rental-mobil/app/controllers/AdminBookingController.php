<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/db.php";

class AdminBookingController {
    
    public static function confirmBooking() {
        global $conn;
        
        try {
            // Validasi admin
            if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
                throw new Exception("Akses ditolak. Hanya admin yang bisa konfirmasi booking.");
            }

            $booking_id = intval($_POST['booking_id'] ?? 0);

            if (!$booking_id) {
                throw new Exception("Booking ID tidak valid");
            }

            // Update status booking menjadi confirmed
            $update_query = "UPDATE bookings SET status = 'confirmed' WHERE booking_id = $booking_id";
            
            $update_res = mysqli_query($conn, $update_query);

            if (!$update_res) {
                throw new Exception("Gagal konfirmasi booking: " . mysqli_error($conn));
            }

            $_SESSION['success'] = "Booking #$booking_id berhasil dikonfirmasi!";
            header("Location: ../../public/admin/bookings.php");
            exit;

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: ../../public/admin/bookings.php");
            exit;
        }
    }

    public static function cancelBooking() {
        global $conn;
        
        try {
            // Validasi admin
            if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
                throw new Exception("Akses ditolak.");
            }

            $booking_id = intval($_POST['booking_id'] ?? 0);
            $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';

            if (!$booking_id) {
                throw new Exception("Booking ID tidak valid");
            }

            // Get car_id before cancelling to update car status
            $car_query = "SELECT car_id FROM bookings WHERE booking_id = $booking_id";
            $car_res = mysqli_query($conn, $car_query);
            $booking = mysqli_fetch_assoc($car_res);
            $car_id = $booking['car_id'];

            // Update status booking menjadi cancelled
            $update_query = "UPDATE bookings SET status = 'cancelled' WHERE booking_id = $booking_id";
            
            $update_res = mysqli_query($conn, $update_query);

            if (!$update_res) {
                throw new Exception("Gagal membatalkan booking: " . mysqli_error($conn));
            }

            // Update mobil kembali menjadi available
            $update_car_query = "UPDATE cars SET status = 'available' WHERE car_id = $car_id";
            mysqli_query($conn, $update_car_query);

            $_SESSION['success'] = "Booking #$booking_id berhasil dibatalkan!";
            header("Location: ../../public/admin/bookings.php");
            exit;

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: ../../public/admin/bookings.php");
            exit;
        }
    }

    public static function markFinished() {
        global $conn;
        
        try {
            // Validasi admin
            if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
                throw new Exception("Akses ditolak.");
            }

            $booking_id = intval($_POST['booking_id'] ?? 0);

            if (!$booking_id) {
                throw new Exception("Booking ID tidak valid");
            }

            // Get car_id before marking as finished
            $car_query = "SELECT car_id FROM bookings WHERE booking_id = $booking_id";
            $car_res = mysqli_query($conn, $car_query);
            $booking = mysqli_fetch_assoc($car_res);
            $car_id = $booking['car_id'];

            // Update status booking menjadi finished
            $update_query = "UPDATE bookings SET status = 'finished' WHERE booking_id = $booking_id";
            $update_res = mysqli_query($conn, $update_query);

            if (!$update_res) {
                throw new Exception("Gagal menandai booking selesai: " . mysqli_error($conn));
            }

            // Update mobil kembali menjadi available
            $update_car_query = "UPDATE cars SET status = 'available' WHERE car_id = $car_id";
            mysqli_query($conn, $update_car_query);

            $_SESSION['success'] = "Booking #$booking_id telah selesai! Mobil kembali tersedia.";
            header("Location: ../../public/admin/bookings.php");
            exit;

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: ../../public/admin/bookings.php");
            exit;
        }
    }

    public static function updateCarStatus() {
        global $conn;
        
        try {
            // Validasi admin
            if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
                throw new Exception("Akses ditolak. Hanya admin yang bisa update status mobil.");
            }

            $car_id = intval($_POST['car_id'] ?? 0);
            $status = isset($_POST['status']) ? $_POST['status'] : '';

            if (!$car_id) {
                throw new Exception("Car ID tidak valid");
            }

            if (!in_array($status, ['available', 'rented'])) {
                throw new Exception("Status tidak valid");
            }

            // Update status mobil
            $update_query = "UPDATE cars SET status = '$status' WHERE car_id = $car_id";
            
            $update_res = mysqli_query($conn, $update_query);

            if (!$update_res) {
                throw new Exception("Gagal update status mobil: " . mysqli_error($conn));
            }

            $_SESSION['success'] = "Status mobil berhasil diupdate!";
            header("Location: ../../public/admin/dashboard.php");
            exit;

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: ../../public/admin/dashboard.php");
            exit;
        }
    }
}

// Handle action
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'confirm_booking') {
    AdminBookingController::confirmBooking();
} elseif ($action === 'cancel_booking') {
    AdminBookingController::cancelBooking();
} elseif ($action === 'mark_finished') {
    AdminBookingController::markFinished();
} elseif ($action === 'update_car_status') {
    AdminBookingController::updateCarStatus();
} else {
    $_SESSION['error'] = "Action tidak dikenali: " . $action;
    header("Location: ../../public/admin/bookings.php");
    exit;
}
?>