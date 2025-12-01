<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/db.php";

class AdminController {
    
    public static function login() {
        global $conn;
        
        try {
            // Validasi input
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';

            // Validasi field tidak kosong
            if (empty($email) || empty($password)) {
                throw new Exception("Email dan password harus diisi");
            }

            // Query admin berdasarkan email
            $email_escaped = mysqli_real_escape_string($conn, $email);
            $query = "SELECT * FROM users WHERE email = '$email_escaped' AND role = 'admin' LIMIT 1";
            $res = mysqli_query($conn, $query);

            if (!$res) {
                throw new Exception("Database error: " . mysqli_error($conn));
            }

            if (mysqli_num_rows($res) === 0) {
                throw new Exception("Email atau password salah");
            }

            $admin = mysqli_fetch_assoc($res);

            // Verifikasi password
            if (!password_verify($password, $admin['password'])) {
                throw new Exception("Email atau password salah");
            }

            // Set session admin
            $_SESSION['user'] = [
                'user_id' => $admin['user_id'],
                'name' => $admin['name'],
                'email' => $admin['email'],
                'role' => $admin['role']
            ];

            $_SESSION['success'] = "Login berhasil! Selamat datang, " . $admin['name'] . "!";
            header("Location: ../../public/admin/dashboard.php");
            exit;

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: ../../public/admin/login.php");
            exit;
        }
    }

    public static function logout() {
        // Destroy session
        session_destroy();
        
        // Redirect ke login admin
        header("Location: ../../public/admin/login.php");
        exit;
    }

    // Method untuk menambah mobil (untuk modal di dashboard)
    public static function addCar() {
        global $conn;
        
        try {
            // Validasi admin
            if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
                throw new Exception("Akses ditolak.");
            }

            // Validasi input
            $car_name = isset($_POST['car_name']) ? trim($_POST['car_name']) : '';
            $brand = isset($_POST['brand']) ? trim($_POST['brand']) : '';
            $plate_number = isset($_POST['plate_number']) ? trim($_POST['plate_number']) : '';
            $price_per_day = isset($_POST['price_per_day']) ? intval($_POST['price_per_day']) : 0;

            if (empty($car_name) || empty($brand) || empty($plate_number) || $price_per_day <= 0) {
                throw new Exception("Data mobil tidak lengkap");
            }

            // Cek plat nomor sudah ada
            $plate_escaped = mysqli_real_escape_string($conn, $plate_number);
            $check_query = "SELECT car_id FROM cars WHERE plate_number = '$plate_escaped'";
            $check_res = mysqli_query($conn, $check_query);

            if (mysqli_num_rows($check_res) > 0) {
                throw new Exception("Plat nomor sudah terdaftar");
            }

            // Insert mobil baru
            $car_name_escaped = mysqli_real_escape_string($conn, $car_name);
            $brand_escaped = mysqli_real_escape_string($conn, $brand);

            $insert_query = "INSERT INTO cars (car_name, brand, plate_number, price_per_day, status, created_at)
                            VALUES ('$car_name_escaped', '$brand_escaped', '$plate_escaped', $price_per_day, 'available', NOW())";
            
            $insert_res = mysqli_query($conn, $insert_query);

            if (!$insert_res) {
                throw new Exception("Gagal menambah mobil: " . mysqli_error($conn));
            }

            $car_id = mysqli_insert_id($conn);

            // Handle upload gambar
            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                $upload_dir = __DIR__ . '/../../public/uploads/cars/';
                
                // Cek folder upload
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                        $file_name = uniqid() . '_' . time() . '_' . basename($_FILES['images']['name'][$key]);
                        $file_path = $upload_dir . $file_name;

                        if (move_uploaded_file($tmp_name, $file_path)) {
                            // Simpan path gambar ke database
                            $file_name_escaped = mysqli_real_escape_string($conn, $file_name);
                            $img_query = "INSERT INTO car_images (car_id, image_path, created_at) 
                                        VALUES ($car_id, '$file_name_escaped', NOW())";
                            mysqli_query($conn, $img_query);
                        }
                    }
                }
            }

            $_SESSION['success'] = "Mobil berhasil ditambahkan!";
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

if ($action === 'login') {
    AdminController::login();
} elseif ($action === 'logout') {
    AdminController::logout();
} elseif ($action === 'add_car') {
    AdminController::addCar();
} else {
    $_SESSION['error'] = "Action tidak dikenali: " . $action;
    header("Location: ../../public/admin/login.php");
    exit;
}
?>