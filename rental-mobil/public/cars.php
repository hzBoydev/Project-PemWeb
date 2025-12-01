<?php
session_start();
require_once "navbar.php";
require_once "../app/config/db.php";

// Filter dan search
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';

// Build WHERE clause
$where = "WHERE c.status = 'available'";
if ($search) {
    $where .= " AND (c.car_name LIKE '%$search%' OR c.brand LIKE '%$search%')";
}

// Build ORDER BY clause
$order_by = ($sort === 'price_low') ? 'c.price_per_day ASC' : 
            (($sort === 'price_high') ? 'c.price_per_day DESC' : 'c.created_at DESC');

// Query - menggunakan nama kolom yang sesuai dengan database
$query = "SELECT c.car_id, c.car_name, c.brand, c.plate_number, c.price_per_day, c.status,
          (SELECT image_path FROM car_images WHERE car_id = c.car_id LIMIT 1) AS thumb
          FROM cars c
          $where
          ORDER BY $order_by";

$res = mysqli_query($conn, $query);
if (!$res) {
    die("Query Error: " . mysqli_error($conn));
}

$total_cars = mysqli_num_rows($res);
?>

<div class="container-lg py-5">
    <!-- Header -->
    <div class="mb-5">
        <h2 class="section-title">
            <div class="divider"></div>
            Daftar Mobil Tersedia
        </h2>
    </div>

    <!-- Search & Filter -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" 
                       placeholder="Cari brand atau nama mobil..." 
                       value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-detail" style="width: auto; padding: 0 20px;">
                    <i class="fas fa-search"></i> Cari
                </button>
            </form>
        </div>
        <div class="col-lg-4">
            <form method="GET" class="d-flex gap-2">
                <?php if ($search): ?>
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                <?php endif; ?>
                <select name="sort" class="form-select" onchange="this.form.submit()">
                    <option value="created_at" <?= $sort === 'created_at' ? 'selected' : '' ?>>
                        Terbaru
                    </option>
                    <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>
                        Harga Terendah
                    </option>
                    <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>
                        Harga Tertinggi
                    </option>
                </select>
            </form>
        </div>
    </div>

    <!-- Status Info -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle"></i>
        Ditemukan <strong><?= $total_cars ?></strong> mobil yang tersedia
        <?php if ($search): ?>
            untuk pencarian "<strong><?= htmlspecialchars($search) ?></strong>"
        <?php endif; ?>
    </div>

    <!-- Cars Grid -->
    <div class="row g-4">
        <?php if ($total_cars > 0): ?>
            <?php while($car = mysqli_fetch_assoc($res)): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="car-card">
                        <!-- Image -->
                        <div class="car-card-image">
                            <img src="<?php echo ($car['thumb']) ? 
                                "uploads/cars/".$car['thumb'] : 
                                'assets/img/no-image.png'; ?>"
                                 alt="<?= htmlspecialchars($car['car_name']) ?>">
                            <div class="car-badge">TERSEDIA</div>
                        </div>

                        <!-- Content -->
                        <div class="car-card-body">
                            <h5 class="car-title"><?= htmlspecialchars($car['car_name']) ?></h5>
                            
                            <p class="car-meta">
                                <i class="fas fa-tag"></i> 
                                <?= htmlspecialchars($car['brand']) ?>
                            </p>
                            
                            <p class="car-meta">
                                <i class="fas fa-car"></i>
                                <?= htmlspecialchars($car['plate_number']) ?>
                            </p>
                            
                            <div class="car-price">
                                Rp <?= number_format($car['price_per_day'], 0, ',', '.') ?>
                                <span>/ hari</span>
                            </div>

                            <a href="car_detail.php?id=<?= $car['car_id'] ?>" 
                               class="btn btn-detail">
                                <i class="fas fa-info-circle"></i> Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle"></i>
                    Tidak ada mobil yang sesuai dengan pencarian Anda
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>