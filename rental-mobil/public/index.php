<?php 
session_start();
require_once "navbar.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Mobil - Sukses Lancar Rejeki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Hero Section -->
    <section class="bg-primary bg-gradient text-white py-5">
        <div class="container py-5 text-center">
            <h1 class="display-4 fw-bold mb-3">Sewa Mobil Impian Anda</h1>
            <p class="lead mb-4">
                Layanan rental mobil terpercaya dari <strong>Sukses Lancar Rejeki</strong> 
                dengan harga terbaik dan armada terlengkap
            </p>
            <a href="cars.php" class="btn btn-light btn-lg rounded-pill px-4 py-2 fw-bold text-primary shadow">
                <i class="fas fa-car me-2"></i>Jelajahi Sekarang
            </a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 80px; height: 80px;">
                        <i class="fas fa-car fa-2x text-primary"></i>
                    </div>
                    <h5 class="fw-bold">Armada Lengkap</h5>
                    <p class="text-muted">Pilihan mobil beragam dari hatchback hingga premium SUV</p>
                </div>
                
                <div class="col-md-4 text-center">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 80px; height: 80px;">
                        <i class="fas fa-tag fa-2x text-primary"></i>
                    </div>
                    <h5 class="fw-bold">Harga Terjangkau</h5>
                    <p class="text-muted">Tarif kompetitif dengan berbagai paket rental yang fleksibel</p>
                </div>
                
                <div class="col-md-4 text-center">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 80px; height: 80px;">
                        <i class="fas fa-shield-alt fa-2x text-primary"></i>
                    </div>
                    <h5 class="fw-bold">Terpercaya</h5>
                    <p class="text-muted">Proses booking mudah dengan sistem pembayaran yang aman</p>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>