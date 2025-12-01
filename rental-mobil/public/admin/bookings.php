<?php
session_start();
require_once "../../app/config/db.php";

// Validasi admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Ambil semua data booking
$bookings_query = "SELECT b.*, c.car_name, c.brand, c.car_id, 
                u.name as user_name, u.email as user_email,
                p.image_path as proof_image
                FROM bookings b
                JOIN cars c ON b.car_id = c.car_id
                JOIN users u ON b.user_id = u.user_id
                LEFT JOIN payment_proof p ON b.booking_id = p.booking_id
                ORDER BY b.created_at DESC";
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
    <title>Kelola Booking - Sukses Lancar Rejeki Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
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
        .status-badge {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-4">
                    <h4 class="text-white mb-4">
                        <i class="fas fa-car me-2"></i>Sukses Lancar Rejeki
                    </h4>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#addCarModal" data-bs-toggle="modal">
                                <i class="fas fa-plus-circle"></i>Tambah Mobil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="bookings.php">
                                <i class="fas fa-list"></i>Kelola Booking
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-warning" href="../../app/controllers/AdminController.php?action=logout">
                                <i class="fas fa-sign-out-alt"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold">Kelola Booking</h2>
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

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <?php if (mysqli_num_rows($bookings_res) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Customer</th>
                                            <th>Mobil</th>
                                            <th>Tanggal</th>
                                            <th>Total</th>
                                            <th>Bukti</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($booking = mysqli_fetch_assoc($bookings_res)): 
                                            $start = new DateTime($booking['start_date']);
                                            $end = new DateTime($booking['end_date']);
                                            $durasi = $end->diff($start)->days;
                                        ?>
                                            <tr>
                                                <td><code>#<?= $booking['booking_id'] ?></code></td>
                                                <td>
                                                    <div>
                                                        <strong><?= htmlspecialchars($booking['user_name']) ?></strong><br>
                                                        <small class="text-muted"><?= htmlspecialchars($booking['user_email']) ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong><?= htmlspecialchars($booking['car_name']) ?></strong><br>
                                                    <small class="text-muted"><?= htmlspecialchars($booking['brand']) ?></small>
                                                </td>
                                                <td>
                                                    <small>
                                                        <?= date('d/m/Y', strtotime($booking['start_date'])) ?><br>
                                                        s/d<br>
                                                        <?= date('d/m/Y', strtotime($booking['end_date'])) ?>
                                                    </small>
                                                    <br>
                                                    <small class="text-muted">(<?= $durasi ?> hari)</small>
                                                </td>
                                                <td class="fw-bold text-primary">
                                                    Rp <?= number_format($booking['total_price'], 0, ',', '.') ?>
                                                </td>
                                                <td>
                                                    <?php if ($booking['proof_image']): ?>
                                                        <a href="../uploads/proofs/<?= $booking['proof_image'] ?>" 
                                                           target="_blank" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye me-1"></i>Lihat
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Tidak ada</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $status_badge = [
                                                        'pending' => 'bg-warning',
                                                        'confirmed' => 'bg-success', 
                                                        'cancelled' => 'bg-danger',
                                                        'finished' => 'bg-info'
                                                    ];
                                                    ?>
                                                    <span class="badge status-badge <?= $status_badge[$booking['status']] ?>">
                                                        <?= ucfirst($booking['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <?php if ($booking['status'] == 'pending'): ?>
                                                            <!-- Konfirmasi Booking -->
                                                            <form method="POST" action="../../app/controllers/AdminBookingController.php?action=confirm_booking" class="d-inline">
                                                                <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                                                                <button type="submit" class="btn btn-success" 
                                                                        onclick="return confirm('Konfirmasi booking #<?= $booking['booking_id'] ?>?')">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>

                                                        <?php if ($booking['status'] == 'confirmed'): ?>
                                                            <!-- Tandai Selesai -->
                                                            <form method="POST" action="../../app/controllers/AdminBookingController.php?action=mark_finished" class="d-inline">
                                                                <input type="hidden" name="booking_id" value="<?= $booking['booking_id'] ?>">
                                                                <button type="submit" class="btn btn-info" 
                                                                        onclick="return confirm('Tandai booking #<?= $booking['booking_id'] ?> sebagai selesai? Mobil akan kembali tersedia.')">
                                                                    <i class="fas fa-flag"></i>
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>

                                                        <?php if ($booking['status'] == 'pending'): ?>
                                                            <!-- Batalkan Booking -->
                                                            <button class="btn btn-danger" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#cancelModal"
                                                                    data-booking-id="<?= $booking['booking_id'] ?>">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
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
            </div>
        </div>
    </div>

    <!-- Cancel Booking Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Batalkan Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="../../app/controllers/AdminBookingController.php?action=cancel_booking">
                    <div class="modal-body">
                        <input type="hidden" name="booking_id" id="cancelBookingId">
                        <p>Batalkan booking ini? Status akan berubah menjadi <span class="badge bg-danger">cancelled</span> dan mobil akan kembali tersedia.</p>
                        <div class="mb-3">
                            <label class="form-label">Alasan pembatalan:</label>
                            <textarea name="reason" class="form-control" rows="3" placeholder="Berikan alasan pembatalan..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Batalkan Booking</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        // Set booking ID untuk modal cancel
        document.addEventListener('DOMContentLoaded', function() {
            var cancelModal = document.getElementById('cancelModal');
            cancelModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var bookingId = button.getAttribute('data-booking-id');
                document.getElementById('cancelBookingId').value = bookingId;
            });
        });
    </script>
</body>
</html>