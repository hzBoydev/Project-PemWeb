<?php
session_start();
require_once "navbar.php";
require_once "../app/config/db.php";

if (!isset($_GET['id'])) {
    header("Location: cars.php");
    exit;
}

$id = intval($_GET['id']);

// Query mobil dengan nama kolom yang benar
$q = "SELECT * FROM cars WHERE car_id = $id LIMIT 1";
$r = mysqli_query($conn, $q);

if (!$r) {
    die("Query Error: " . mysqli_error($conn));
}

$car = mysqli_fetch_assoc($r);
if (!$car) {
    header("Location: cars.php");
    exit;
}

// Get images dengan nama tabel yang benar
$q2 = "SELECT * FROM car_images WHERE car_id = $id";
$r2 = mysqli_query($conn, $q2);

if (!$r2) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<div class="container-lg py-5">
    <div class="row gap-4">
        <!-- Left: Images Carousel -->
        <div class="col-lg-7">
            <div id="carouselImages" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner rounded-3" style="box-shadow: var(--shadow-lg);">
                    <?php 
                    $active = 'active'; 
                    $has_images = false;
                    while($img = mysqli_fetch_assoc($r2)): 
                        $has_images = true;
                    ?>
                        <div class="carousel-item <?= $active ?>">
                            <img src="uploads/cars/<?= htmlspecialchars($img['image_path']) ?>" 
                                 class="d-block w-100" 
                                 style="height:420px; object-fit:cover;">
                        </div>
                    <?php $active = ''; endwhile; ?>
                    
                    <?php if (!$has_images): ?>
                        <div class="carousel-item active">
                            <img src="assets/img/no-image.png" 
                                 class="d-block w-100" 
                                 style="height:420px; object-fit:cover;">
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($has_images): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselImages" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselImages" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right: Car Details -->
        <div class="col-lg-5">
            <div class="booking-card">
                <h2 style="font-weight: 700; margin-bottom: 0.5rem;">
                    <?= htmlspecialchars($car['car_name']) ?>
                </h2>

                <p style="color: var(--text-light); margin-bottom: 1rem;">
                    <i class="fas fa-tag"></i> <?= htmlspecialchars($car['brand']) ?> 
                    &mdash; 
                    <i class="fas fa-car"></i> <?= htmlspecialchars($car['plate_number']) ?>
                </p>

                <hr style="margin: 1.5rem 0;">

                <div style="margin-bottom: 1.5rem;">
                    <h4 style="color: var(--primary); font-weight: 700;">
                        Rp <?= number_format($car['price_per_day'], 0, ',', '.') ?>
                    </h4>
                    <p style="color: var(--text-light); font-size: 0.95rem;">
                        <i class="fas fa-calendar-days"></i> per hari
                    </p>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <p style="font-weight: 600; color: var(--text-dark); margin-bottom: 0.5rem;">
                        <i class="fas fa-info-circle"></i> Status Mobil
                    </p>
                    <span class="badge <?= $car['status'] == 'available' ? 'badge-available' : 'badge-unavailable' ?>">
                        <i class="fas fa-check-circle"></i> <?= ucfirst($car['status']) ?>
                    </span>
                </div>

                <div style="margin-bottom: 2rem; padding: 1rem; background: var(--bg-light); border-radius: 8px;">
                    <p style="font-weight: 600; color: var(--text-dark); margin-bottom: 0.5rem;">
                        <i class="fas fa-list"></i> Detail Mobil
                    </p>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between;">
                            <span style="color: var(--text-light);">Brand</span>
                            <strong><?= htmlspecialchars($car['brand']) ?></strong>
                        </li>
                        <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between;">
                            <span style="color: var(--text-light);">Plat Nomor</span>
                            <strong><?= htmlspecialchars($car['plate_number']) ?></strong>
                        </li>
                        <li style="padding: 0.5rem 0; display: flex; justify-content: space-between;">
                            <span style="color: var(--text-light);">Status</span>
                            <strong style="color: var(--success);"><?= ucfirst($car['status']) ?></strong>
                        </li>
                    </ul>
                </div>

                <?php if (isset($_SESSION['user'])): ?>
                    <a href="booking.php?car_id=<?= $car['car_id'] ?>" 
                       class="btn btn-detail" 
                       style="padding: 12px 20px; font-size: 1rem; margin-bottom: 1rem;">
                        <i class="fas fa-calendar-check"></i> Booking Sekarang
                    </a>
                <?php else: ?>
                    <a href="login.php" 
                       class="btn btn-detail" 
                       style="padding: 12px 20px; font-size: 1rem; margin-bottom: 1rem;">
                        <i class="fas fa-sign-in-alt"></i> Login untuk Booking
                    </a>
                <?php endif; ?>

                <a href="cars.php" class="btn btn-outline-secondary" style="padding: 12px 20px; font-size: 1rem;">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>