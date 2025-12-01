<?php
// File untuk membuat admin user (HAPUS SETELAH DIPAKAI)
require_once "../app/config/db.php";

try {
    $email = "admin@gmail.com";
    $password = "admin123";
    $name = "Administrator";
    
    // Hash password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    
    // Cek apakah admin sudah ada
    $check_query = "SELECT user_id FROM users WHERE email = '$email'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        echo "<div style='padding: 20px; background: #d4edda; color: #155724; border-radius: 5px;'>
                <h3>✅ Admin sudah ada</h3>
                <p>Akun admin dengan email <strong>admin@gmail.com</strong> sudah terdaftar.</p>
                <p><strong>Login dengan:</strong></p>
                <ul>
                    <li>Email: admin@gmail.com</li>
                    <li>Password: admin123</li>
                </ul>
                <a href='admin/login.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Login ke Admin</a>
              </div>";
    } else {
        // Insert admin user
        $insert_query = "INSERT INTO users (name, email, password, role, created_at)
                        VALUES ('$name', '$email', '$password_hash', 'admin', NOW())";
        
        if (mysqli_query($conn, $insert_query)) {
            echo "<div style='padding: 20px; background: #d4edda; color: #155724; border-radius: 5px;'>
                    <h3>✅ Admin berhasil dibuat!</h3>
                    <p><strong>Login dengan:</strong></p>
                    <ul>
                        <li>Email: admin@gmail.com</li>
                        <li>Password: admin123</li>
                    </ul>
                    <a href='admin/login.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Login ke Admin</a>
                  </div>";
        } else {
            echo "<div style='padding: 20px; background: #f8d7da; color: #721c24; border-radius: 5px;'>
                    <h3>❌ Gagal membuat admin</h3>
                    <p>Error: " . mysqli_error($conn) . "</p>
                  </div>";
        }
    }
} catch (Exception $e) {
    echo "<div style='padding: 20px; background: #f8d7da; color: #721c24; border-radius: 5px;'>
            <h3>❌ Error</h3>
            <p>" . $e->getMessage() . "</p>
          </div>";
}
?>