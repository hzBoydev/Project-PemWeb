<?php
session_start();

// Jika sudah login sebagai admin, redirect ke dashboard
if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Ambil error message dari session jika ada
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Sukses Lancar Rejeki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh;">
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center">
        <div class="row w-100 justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div style="font-size: 3rem; color: var(--primary); margin-bottom: 1rem;">
                                <i class="fas fa-lock"></i>
                            </div>
                            <h3 class="fw-bold">Admin Login</h3>
                            <p class="text-muted">Masuk ke dashboard administrator <strong>Sukses Lancar Rejeki</strong></p>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger mb-4">
                                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <form action="../../app/controllers/AdminController.php?action=login" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-envelope text-muted"></i>
                                    </span>
                                    <input type="email" 
                                        name="email" 
                                        class="form-control border-start-0" 
                                        placeholder="admin@gmail.com" 
                                        required
                                        value="admin@gmail.com">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password" 
                                        name="password" 
                                        class="form-control border-start-0" 
                                        placeholder="Masukkan password" 
                                        required
                                        value="admin123">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                <i class="fas fa-sign-in-alt me-2"></i>Login Admin
                            </button>
                        </form>

                        <div class="text-center mt-4 pt-3 border-top">
                            <a href="../index.php" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>Kembali ke Website
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>