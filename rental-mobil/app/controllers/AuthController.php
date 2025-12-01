<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Path yang benar ke db.php
require_once __DIR__ . "/../config/db.php";

class AuthController {
    
    public static function register() {
        global $conn;
        
        try {
            // Validasi input
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

            // Validasi field tidak kosong
            if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
                throw new Exception("Semua field harus diisi");
            }

            // Validasi panjang nama
            if (strlen($name) < 3) {
                throw new Exception("Nama minimal 3 karakter");
            }

            // Validasi format email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Format email tidak valid");
            }

            // Validasi panjang password
            if (strlen($password) < 6) {
                throw new Exception("Password minimal 6 karakter");
            }

            // Validasi password match
            if ($password !== $confirm_password) {
                throw new Exception("Password tidak cocok");
            }

            // Cek email sudah terdaftar
            $email_escaped = mysqli_real_escape_string($conn, $email);
            $check_query = "SELECT user_id FROM users WHERE email = '$email_escaped' LIMIT 1";
            $check_res = mysqli_query($conn, $check_query);

            if (!$check_res) {
                throw new Exception("Database error: " . mysqli_error($conn));
            }

            if (mysqli_num_rows($check_res) > 0) {
                throw new Exception("Email sudah terdaftar");
            }

            // Hash password
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            // Escape string untuk keamanan
            $name_escaped = mysqli_real_escape_string($conn, $name);
            $password_escaped = mysqli_real_escape_string($conn, $password_hash);

            // Insert user baru
            $insert_query = "INSERT INTO users (name, email, password, role, created_at)
                            VALUES ('$name_escaped', '$email_escaped', '$password_escaped', 'customer', NOW())";

            $insert_res = mysqli_query($conn, $insert_query);

            if (!$insert_res) {
                throw new Exception("Gagal membuat akun: " . mysqli_error($conn));
            }

            $_SESSION['success'] = "Akun berhasil dibuat! Silakan login.";
            header("Location: ../../public/login.php");
            exit;

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: ../../public/register.php");
            exit;
        }
    }

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

            // Query user berdasarkan email
            $email_escaped = mysqli_real_escape_string($conn, $email);
            $query = "SELECT * FROM users WHERE email = '$email_escaped' LIMIT 1";
            $res = mysqli_query($conn, $query);

            if (!$res) {
                throw new Exception("Database error: " . mysqli_error($conn));
            }

            if (mysqli_num_rows($res) === 0) {
                throw new Exception("Email atau password salah");
            }

            $user = mysqli_fetch_assoc($res);

            // Verifikasi password
            if (!password_verify($password, $user['password'])) {
                throw new Exception("Email atau password salah");
            }

            // Set session
            $_SESSION['user'] = [
                'user_id' => $user['user_id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ];

            $_SESSION['success'] = "Login berhasil! Selamat datang, " . $user['name'] . "!";
            header("Location: ../../public/index.php");
            exit;

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: ../../public/login.php");
            exit;
        }
    }

    public static function logout() {
        // Destroy session
        session_destroy();
        
        // Redirect ke home
        header("Location: ../../public/index.php");
        exit;
    }
}

// Handle action
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'register') {
    AuthController::register();
} elseif ($action === 'login') {
    AuthController::login();
} elseif ($action === 'logout') {
    AuthController::logout();
} else {
    $_SESSION['error'] = "Action tidak dikenali";
    header("Location: ../../public/login.php");
    exit;
}