<?php
session_start();
require_once "../../app/config/db.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Handle delete car
if (isset($_GET['delete_car'])) {
    $car_id = intval($_GET['delete_car']);
    
    // 1. Hapus gambar mobil dari server
    $images_query = "SELECT image_path FROM car_images WHERE car_id = $car_id";
    $images_result = mysqli_query($conn, $images_query);
    
    while($img = mysqli_fetch_assoc($images_result)) {
        $file_path = __DIR__ . "/../../uploads/cars/" . $img['image_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    // 2. Hapus dari tabel car_images
    mysqli_query($conn, "DELETE FROM car_images WHERE car_id = $car_id");
    
    // 3. Hapus dari tabel cars
    $delete_query = "DELETE FROM cars WHERE car_id = $car_id";
    
    if (mysqli_query($conn, $delete_query)) {
        $_SESSION['success'] = "Mobil berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus mobil: " . mysqli_error($conn);
    }
    
    header("Location: dashboard.php");
    exit;
}

// Ambil data mobil
$cars_query = "SELECT c.*, 
            (SELECT COUNT(*) FROM car_images WHERE car_id = c.car_id) as image_count,
            (SELECT image_path FROM car_images WHERE car_id = c.car_id LIMIT 1) as thumb
            FROM cars c 
            ORDER BY c.created_at DESC";
$cars_res = mysqli_query($conn, $cars_query);

// Ambil data booking
$bookings_query = "SELECT b.*, c.car_name, u.name as user_name 
                FROM bookings b
                JOIN cars c ON b.car_id = c.car_id
                JOIN users u ON b.user_id = u.user_id
                ORDER BY b.created_at DESC
                LIMIT 10";
$bookings_res = mysqli_query($conn, $bookings_query);

// Ambil message dari session
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success']);
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sukses Lancar Rejeki</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            min-height: 100vh;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 4px 0;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
        }
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border: none;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse min-vh-100">
                <div class="position-sticky pt-3">
                    <div class="px-3 mb-4">
                        <h5 class="text-white">
                            <i class="fas fa-car me-2"></i>Sukses Lancar Rejeki
                        </h5>
                        <p class="text-white-50 small mb-0">Admin Panel</p>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white active bg-primary rounded" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50" href="#addCarModal" data-bs-toggle="modal">
                                <i class="fas fa-plus-circle me-2"></i>Tambah Mobil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white-50" href="bookings.php">
                                <i class="fas fa-list me-2"></i>Kelola Booking
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-warning" href="../../app/controllers/AdminController.php?action=logout">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2 fw-bold">Admin Dashboard</h1>
                    <span class="badge bg-primary fs-6">
                        <i class="fas fa-user me-1"></i><?= htmlspecialchars($_SESSION['user']['name']) ?>
                    </span>
                </div>

                <!-- Messages -->
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="text-primary mb-2">
                                    <i class="fas fa-car fa-2x"></i>
                                </div>
                                <h4 class="fw-bold"><?= mysqli_num_rows($cars_res) ?></h4>
                                <p class="text-muted mb-0">Total Mobil</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="text-success mb-2">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                                <h4 class="fw-bold">
                                    <?php 
                                    $available_cars = mysqli_query($conn, "SELECT COUNT(*) as total FROM cars WHERE status = 'available'");
                                    echo mysqli_fetch_assoc($available_cars)['total'];
                                    ?>
                                </h4>
                                <p class="text-muted mb-0">Mobil Tersedia</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="text-warning mb-2">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                                <h4 class="fw-bold">
                                    <?php 
                                    $pending_bookings = mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE status = 'pending'");
                                    echo mysqli_fetch_assoc($pending_bookings)['total'];
                                    ?>
                                </h4>
                                <p class="text-muted mb-0">Booking Pending</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="text-info mb-2">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                                <h4 class="fw-bold">
                                    <?php 
                                    $total_users = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'customer'");
                                    echo mysqli_fetch_assoc($total_users)['total'];
                                    ?>
                                </h4>
                                <p class="text-muted mb-0">Total Customer</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cars Management -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="fas fa-car me-2"></i>Daftar Mobil
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($cars_res) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Gambar</th>
                                            <th>Nama Mobil</th>
                                            <th>Brand</th>
                                            <th>Plat Nomor</th>
                                            <th>Harga/Hari</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        // Reset pointer result
                                        mysqli_data_seek($cars_res, 0);
                                        while($car = mysqli_fetch_assoc($cars_res)): 
                                        ?>
                                            <tr>
                                                <td>
                                                    <?php if ($car['thumb']): ?>
                                                        <img src="../uploads/cars/<?= $car['thumb'] ?>" 
                                                            alt="<?= htmlspecialchars($car['car_name']) ?>" 
                                                            style="width: 60px; height: 40px; object-fit: cover;" 
                                                            class="rounded">
                                                    <?php else: ?>
                                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                            style="width: 60px; height: 40px;">
                                                            <i class="fas fa-car text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="fw-semibold"><?= htmlspecialchars($car['car_name']) ?></td>
                                                <td><?= htmlspecialchars($car['brand']) ?></td>
                                                <td><code><?= htmlspecialchars($car['plate_number']) ?></code></td>
                                                <td class="fw-bold text-primary">Rp <?= number_format($car['price_per_day'], 0, ',', '.') ?></td>
                                                <td>
                                                    <span class="badge <?= $car['status'] == 'available' ? 'bg-success' : 'bg-warning' ?>">
                                                        <?= ucfirst($car['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <!-- UPDATE Status Button -->
                                                        <form method="POST" action="../../app/controllers/AdminBookingController.php?action=update_car_status" class="d-inline">
                                                            <input type="hidden" name="car_id" value="<?= $car['car_id'] ?>">
                                                            <input type="hidden" name="status" value="<?= $car['status'] == 'available' ? 'rented' : 'available' ?>">
                                                            <button type="submit" class="btn btn-sm <?= $car['status'] == 'available' ? 'btn-warning' : 'btn-success' ?>">
                                                                <i class="fas fa-sync-alt me-1"></i>
                                                                <?= $car['status'] == 'available' ? 'Tandai Disewa' : 'Tandai Tersedia' ?>
                                                            </button>
                                                        </form>
                                                        
                                                        <!-- DELETE Button -->
                                                        <a href="?delete_car=<?= $car['car_id'] ?>" 
                                                           class="btn btn-sm btn-danger"
                                                           onclick="return confirmDelete('<?= htmlspecialchars($car['car_name']) ?>')">
                                                            <i class="fas fa-trash me-1"></i>Hapus
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-car fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada mobil yang terdaftar.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Bookings -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="fas fa-history me-2"></i>Booking Terbaru
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($bookings_res) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID Booking</th>
                                            <th>Customer</th>
                                            <th>Mobil</th>
                                            <th>Tanggal</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($booking = mysqli_fetch_assoc($bookings_res)): ?>
                                            <tr>
                                                <td><code>#<?= $booking['booking_id'] ?></code></td>
                                                <td><?= htmlspecialchars($booking['user_name']) ?></td>
                                                <td><?= htmlspecialchars($booking['car_name']) ?></td>
                                                <td>
                                                    <?= date('d/m/Y', strtotime($booking['start_date'])) ?> - 
                                                    <?= date('d/m/Y', strtotime($booking['end_date'])) ?>
                                                </td>
                                                <td class="fw-bold text-primary">Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></td>
                                                <td>
                                                    <span class="badge <?= $booking['status'] == 'confirmed' ? 'bg-success' : 
                                                                        ($booking['status'] == 'pending' ? 'bg-warning' : 'bg-danger') ?>">
                                                        <?= ucfirst($booking['status']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada booking.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Car Modal -->
    <div class="modal fade" id="addCarModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Mobil Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../app/controllers/AdminController.php?action=add_car" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nama Mobil</label>
                                <input type="text" name="car_name" class="form-control" placeholder="Contoh: Toyota Avanza" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Brand</label>
                                <input type="text" name="brand" class="form-control" placeholder="Contoh: Toyota" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Plat Nomor</label>
                                <input type="text" name="plate_number" class="form-control" placeholder="Contoh: B 1234 ABC" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Harga per Hari (Rp)</label>
                                <input type="number" name="price_per_day" class="form-control" placeholder="Contoh: 300000" min="100000" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Gambar Mobil (Max 5 gambar)</label>
                            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                            <div class="form-text">Format: JPG, PNG, GIF (Max 2MB per gambar)</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Simpan Mobil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Confirmation for delete
    function confirmDelete(carName) {
        return confirm(`Hapus mobil "${carName}"?\n\nPERHATIAN: \n• Semua data mobil akan dihapus permanen \n• Semua gambar mobil akan dihapus \n• Tindakan ini tidak dapat dibatalkan!`);
    }
    
    // Confirmation for status change
    document.querySelectorAll('form[action*="update_car_status"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const carId = this.querySelector('input[name="car_id"]').value;
            const newStatus = this.querySelector('input[name="status"]').value;
            const action = newStatus === 'rented' ? 'Tandai Disewa' : 'Tandai Tersedia';
            
            if (!confirm(`${action} untuk mobil ID: ${carId}?`)) {
                e.preventDefault();
            }
        });
    });
    </script>
</body>
</html>