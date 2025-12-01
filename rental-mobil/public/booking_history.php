<?php
session_start();
require_once "navbar.php";
require_once "../app/config/db.php";

// Validasi login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['user_id'];

// DEBUG: Show user info
echo "<!-- Debug: User ID = $user_id -->";

// Query booking berdasarkan user
$query = "SELECT b.booking_id, b.start_date, b.end_date, b.total_price, b.status, b.created_at,
          c.car_id, c.car_name, c.brand, c.price_per_day,
          p.image_path AS proof_image
          FROM bookings b
          JOIN cars c ON b.car_id = c.car_id
          LEFT JOIN payment_proof p ON b.booking_id = p.booking_id
          WHERE b.user_id = $user_id
          ORDER BY b.created_at DESC";

echo "<!-- Debug Query: $query -->";

$res = mysqli_query($conn, $query);

if (!$res) {
    die("Query Error: " . mysqli_error($conn));
}

$total_bookings = mysqli_num_rows($res);

// DEBUG: Show booking count
echo "<!-- Debug: Total bookings found = $total_bookings -->";
?>

<div class="container-lg py-5">
    <!-- Header -->
    <div class="mb-5">
        <h2 class="section-title">
            <div class="divider"></div>
            Booking Saya
        </h2>
    </div>

    <!-- Status Info -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle"></i>
        Anda memiliki <strong><?= $total_bookings ?></strong> riwayat booking
    </div>

    <!-- Bookings List -->
    <?php if ($total_bookings > 0): ?>
        <div class="row g-4">
            <?php while($booking = mysqli_fetch_assoc($res)): 
                $start = new DateTime($booking['start_date']);
                $end = new DateTime($booking['end_date']);
                $durasi = $end->diff($start)->days;
                // Gunakan total_price dari database jika ada, jika tidak hitung manual
                $total = $booking['total_price'] ? $booking['total_price'] : ($durasi * $booking['price_per_day']);
            ?>
                <div class="col-lg-6">
                    <div class="booking-card">
                        <!-- Top Section -->
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid var(--border);">
                            <div>
                                <h5 style="font-weight: 700; margin-bottom: 0.25rem;">
                                    <?= htmlspecialchars($booking['car_name']) ?>
                                </h5>
                                <p style="color: var(--text-light); font-size: 0.9rem; margin-bottom: 0;">
                                    <i class="fas fa-tag"></i> <?= htmlspecialchars($booking['brand']) ?>
                                </p>
                            </div>
                            <span class="badge <?= $booking['status'] == 'confirmed' ? 'badge-available' : 
                                                   ($booking['status'] == 'pending' ? 'badge badge-warning' : 'badge badge-danger') ?>"
                                  style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                                <i class="fas <?= $booking['status'] == 'confirmed' ? 'fa-check-circle' : 
                                                  ($booking['status'] == 'pending' ? 'fa-clock' : 'fa-times-circle') ?>"></i>
                                <?= ucfirst($booking['status']) ?>
                            </span>
                        </div>

                        <!-- Booking Details -->
                        <div style="margin-bottom: 1.5rem;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                <div>
                                    <p style="color: var(--text-light); font-size: 0.85rem; margin-bottom: 0.25rem;">
                                        <i class="fas fa-calendar-alt"></i> Tanggal Mulai
                                    </p>
                                    <p style="font-weight: 700; margin-bottom: 0;">
                                        <?= date('d/m/Y', strtotime($booking['start_date'])) ?>
                                    </p>
                                </div>
                                <div>
                                    <p style="color: var(--text-light); font-size: 0.85rem; margin-bottom: 0.25rem;">
                                        <i class="fas fa-calendar-alt"></i> Tanggal Akhir
                                    </p>
                                    <p style="font-weight: 700; margin-bottom: 0;">
                                        <?= date('d/m/Y', strtotime($booking['end_date'])) ?>
                                    </p>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div>
                                    <p style="color: var(--text-light); font-size: 0.85rem; margin-bottom: 0.25rem;">
                                        <i class="fas fa-hourglass-half"></i> Durasi
                                    </p>
                                    <p style="font-weight: 700; margin-bottom: 0;">
                                        <?= $durasi ?> hari
                                    </p>
                                </div>
                                <div>
                                    <p style="color: var(--text-light); font-size: 0.85rem; margin-bottom: 0.25rem;">
                                        <i class="fas fa-money-bill"></i> Total Harga
                                    </p>
                                    <p style="font-weight: 700; color: var(--primary); margin-bottom: 0;">
                                        Rp <?= number_format($total, 0, ',', '.') ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Proof Image -->
                        <div style="margin-bottom: 1rem; padding: 1rem; background: var(--bg-light); border-radius: 8px;">
                            <p style="color: var(--text-light); font-size: 0.85rem; margin-bottom: 0.5rem;">
                                <i class="fas fa-receipt"></i> Bukti Pembayaran
                            </p>
                            <?php if ($booking['proof_image']): ?>
                                <img src="uploads/proofs/<?= htmlspecialchars($booking['proof_image']) ?>" 
                                     class="img-thumbnail" 
                                     style="max-width: 100%; height: auto; cursor: pointer;" 
                                     alt="Bukti Pembayaran"
                                     onclick="window.open(this.src)">
                            <?php else: ?>
                                <p style="color: var(--text-light); font-size: 0.9rem; margin-bottom: 0;">
                                    <i class="fas fa-times"></i> Tidak ada bukti
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- Footer -->
                        <div style="padding-top: 1rem; border-top: 1px solid var(--border);">
                            <p style="color: var(--text-light); font-size: 0.85rem; margin-bottom: 0;">
                                <i class="fas fa-clock"></i> Dibuat: <?= date('d/m/Y H:i', strtotime($booking['created_at'])) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            <i class="fas fa-inbox"></i>
            Anda belum memiliki riwayat booking. 
            <a href="cars.php" style="font-weight: 600;">Mulai booking sekarang!</a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>