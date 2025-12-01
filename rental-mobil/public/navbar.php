<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">

<style>
    :root {
        --primary: #2563eb;
        --primary-dark: #1e40af;
        --secondary: #f97316;
        --text-dark: #1f2937;
        --text-light: #6b7280;
        --border: #e5e7eb;
    }

    .navbar {
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-bottom: 2px solid var(--border);
        padding: 1rem 0;
    }

    .navbar-brand {
        font-weight: 700;
        font-size: 1.5rem;
        color: var(--primary) !important;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .navbar-brand i {
        font-size: 1.75rem;
    }

    .nav-link {
        color: var(--text-dark) !important;
        font-weight: 500;
        margin: 0 0.5rem;
        transition: all 0.3s;
        position: relative;
    }

    .nav-link:hover,
    .nav-link.active {
        color: var(--primary) !important;
    }

    .nav-link i {
        margin-right: 0.5rem;
    }

    .nav-link.text-danger:hover {
        color: #ef4444 !important;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <div class="container-lg">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-car"></i> Sukses Lancar Rejeki
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cars.php">
                        <i class="fas fa-list"></i> Daftar Mobil
                    </a>
                </li>

                <?php if (!isset($_SESSION['user'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="booking_history.php">
                            <i class="fas fa-history"></i> Booking Saya
                        </a>
                    </li>
                    <li class="nav-item">
                        <span class="nav-link" style="cursor: default; color: var(--text-light);">
                            <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['user']['name']) ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="../app/controllers/AuthController.php?action=logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>