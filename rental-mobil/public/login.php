<?php 
session_start();
require_once "navbar.php";

// Jika sudah login, redirect ke home
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

// Ambil error message dari session jika ada
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);
?>

<div class="container-lg py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="booking-card">
                <div class="text-center mb-4">
                    <div style="font-size: 3rem; color: var(--primary); margin-bottom: 1rem;">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <h3 style="font-weight: 700;">Login</h3>
                    <p style="color: var(--text-light);">Masuk ke akun Anda untuk melakukan booking</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger mb-4">
                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form action="../app/controllers/AuthController.php?action=login" method="POST" novalidate>
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
                               placeholder="Masukkan password Anda" 
                               required>
                    </div>

                    <button type="submit" class="btn btn-detail w-100" style="padding: 12px 20px; font-size: 1rem; margin-bottom: 1rem;">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>

                <div style="text-align: center; border-top: 1px solid var(--border); padding-top: 1rem;">
                    <p style="color: var(--text-light); margin-bottom: 0;">
                        Belum punya akun? 
                        <a href="register.php" style="font-weight: 600; color: var(--primary);">
                            Daftar di sini
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>