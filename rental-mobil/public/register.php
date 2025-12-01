<?php 
session_start();
require_once "navbar.php";

// Jika sudah login, redirect ke home
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

// Ambil message dari session jika ada
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success']);
unset($_SESSION['error']);
?>

<div class="container-lg py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="booking-card">
                <div class="text-center mb-4">
                    <div style="font-size: 3rem; color: var(--secondary); margin-bottom: 1rem;">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3 style="font-weight: 700;">Daftar Akun</h3>
                    <p style="color: var(--text-light);">Buat akun baru untuk mulai rental mobil</p>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success mb-4">
                        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger mb-4">
                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form action="../app/controllers/AuthController.php?action=register" method="POST" novalidate>
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-user"></i> Nama Lengkap
                        </label>
                        <input type="text" 
                               name="name" 
                               class="form-control" 
                               placeholder="Masukkan nama lengkap Anda"
                               minlength="3"
                               maxlength="100"
                               required>
                        <small class="text-muted" style="display: block; margin-top: 0.25rem;">
                            Minimal 3 karakter
                        </small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-envelope"></i> Email
                        </label>
                        <input type="email" 
                               name="email" 
                               class="form-control" 
                               placeholder="Masukkan email Anda"
                               required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <input type="password" 
                               name="password" 
                               class="form-control" 
                               placeholder="Masukkan password (minimal 6 karakter)"
                               minlength="6"
                               required
                               id="password">
                        <small class="text-muted" style="display: block; margin-top: 0.25rem;">
                            Minimal 6 karakter
                        </small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-check-circle"></i> Konfirmasi Password
                        </label>
                        <input type="password" 
                               name="confirm_password" 
                               class="form-control" 
                               placeholder="Ulangi password Anda"
                               required
                               id="confirm_password">
                    </div>

                    <button type="submit" class="btn btn-detail w-100" style="padding: 12px 20px; font-size: 1rem; margin-bottom: 1rem;">
                        <i class="fas fa-user-plus"></i> Daftar
                    </button>
                </form>

                <div style="text-align: center; border-top: 1px solid var(--border); padding-top: 1rem;">
                    <p style="color: var(--text-light); margin-bottom: 0;">
                        Sudah punya akun? 
                        <a href="login.php" style="font-weight: 600; color: var(--primary);">
                            Login di sini
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Validasi password match
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Password tidak cocok!');
        document.getElementById('confirm_password').focus();
    }
});
</script>