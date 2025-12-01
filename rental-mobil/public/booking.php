<?php
session_start();
require_once "navbar.php";
require_once "../app/config/db.php";

// Validasi login
if (!isset($_SESSION['user'])) {
    echo "<script>
        alert('Silakan login terlebih dahulu.');
        window.location='login.php';
    </script>";
    exit;
}

// Validasi car_id
$car_id = isset($_GET['car_id']) ? intval($_GET['car_id']) : 0;
if (!$car_id) {
    header("Location: cars.php");
    exit;
}

// Ambil data mobil dengan nama kolom yang benar
$q = "SELECT * FROM cars WHERE car_id = $car_id LIMIT 1";
$res = mysqli_query($conn, $q);

if (!$res) {
    die("Query Error: " . mysqli_error($conn));
}

$car = mysqli_fetch_assoc($res);

if (!$car) {
    header("Location: cars.php");
    exit;
}

// Ambil gambar mobil dengan nama tabel yang benar
$q_img = "SELECT * FROM car_images WHERE car_id = $car_id LIMIT 1";
$res_img = mysqli_query($conn, $q_img);

if (!$res_img) {
    die("Query Error: " . mysqli_error($conn));
}

$car_img = mysqli_fetch_assoc($res_img);
?>

<div class="container-lg py-5">
    <!-- Header -->
    <div class="mb-5">
        <h2 class="section-title">
            <div class="divider"></div>
            Booking Mobil
        </h2>
    </div>

    <div class="row gap-4">
        <!-- Left: Car Summary -->
        <div class="col-lg-4">
            <div class="booking-card">
                <h5 class="mb-3">
                    <i class="fas fa-info-circle"></i> Ringkasan Mobil
                </h5>
                
                <!-- Car Image -->
                <div class="mb-3">
                    <img src="<?php echo ($car_img && $car_img['image_path']) ? 
                        "uploads/cars/".htmlspecialchars($car_img['image_path']) : 
                        'assets/img/no-image.png'; ?>"
                         class="img-fluid rounded-2" 
                         alt="<?= htmlspecialchars($car['car_name']) ?>"
                         style="max-height: 250px; object-fit: cover;">
                </div>

                <!-- Car Details -->
                <table class="table table-borderless small" style="margin-bottom: 0;">
                    <tr>
                        <td style="color: var(--text-light);">Nama Mobil</td>
                        <td style="font-weight: 700;"><?= htmlspecialchars($car['car_name']) ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--text-light);">Brand</td>
                        <td style="font-weight: 700;"><?= htmlspecialchars($car['brand']) ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--text-light);">Plat Nomor</td>
                        <td style="font-weight: 700;"><?= htmlspecialchars($car['plate_number']) ?></td>
                    </tr>
                    <tr>
                        <td style="color: var(--text-light);">Harga/Hari</td>
                        <td style="font-weight: 700; color: var(--primary);">
                            Rp <?= number_format($car['price_per_day'], 0, ',', '.') ?>
                        </td>
                    </tr>
                </table>

                <!-- Price Calculator -->
                <div style="background: var(--bg-light); padding: 1rem; border-radius: 8px; margin-top: 1.5rem;">
                    <h6 style="margin-bottom: 1rem; font-weight: 700;">
                        <i class="fas fa-calculator"></i> Estimasi Biaya
                    </h6>
                    <table class="table table-sm table-borderless" style="margin-bottom: 0;">
                        <tr>
                            <td style="color: var(--text-light);">Hari Rental:</td>
                            <td style="text-align: right; font-weight: 600;"><span id="durasi">-</span> hari</td>
                        </tr>
                        <tr>
                            <td style="color: var(--text-light);">Harga/Hari:</td>
                            <td style="text-align: right; font-weight: 600;">Rp <?= number_format($car['price_per_day'], 0, ',', '.') ?></td>
                        </tr>
                        <tr style="border-top: 2px solid var(--border);">
                            <td style="font-weight: 700; color: var(--text-dark);">Total Biaya:</td>
                            <td style="text-align: right; font-weight: 700; color: var(--primary); font-size: 1.1rem;" 
                                id="totalHarga">Rp 0</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Booking Form -->
        <div class="col-lg-8">
            <div class="booking-card">
                <h5 class="mb-4">
                    <i class="fas fa-calendar-check"></i> Form Booking
                </h5>

                <form action="../app/controllers/BookingController.php?action=store" 
                      method="POST" 
                      enctype="multipart/form-data"
                      id="bookingForm"
                      novalidate>

                    <input type="hidden" name="car_id" value="<?= $car_id ?>">
                    <input type="hidden" name="user_id" value="<?= $_SESSION['user']['user_id'] ?>">

                    <!-- Tanggal Mulai -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-calendar-alt"></i> Tanggal Mulai Rental
                        </label>
                        <input type="date" 
                               name="start_date" 
                               id="startDate"
                               class="form-control" 
                               required
                               min="<?= date('Y-m-d') ?>">
                        <small class="text-muted" style="display: block; margin-top: 0.5rem;">
                            Pilih tanggal saat Anda akan mengambil mobil
                        </small>
                    </div>

                    <!-- Tanggal Selesai -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-calendar-alt"></i> Tanggal Akhir Rental
                        </label>
                        <input type="date" 
                               name="end_date" 
                               id="endDate"
                               class="form-control" 
                               required
                               min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                        <small class="text-muted" style="display: block; margin-top: 0.5rem;">
                            Pilih tanggal saat Anda akan mengembalikan mobil
                        </small>
                    </div>

                    <!-- Bukti Pembayaran -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-receipt"></i> Upload Bukti Pembayaran
                        </label>
                        <div class="input-group">
                            <input type="file" 
                                   name="proof" 
                                   id="proofFile"
                                   class="form-control" 
                                   accept="image/*"
                                   required>
                            <span class="input-group-text">
                                <i class="fas fa-image"></i>
                            </span>
                        </div>
                        <small class="text-muted" style="display: block; margin-top: 0.5rem;">
                            Format: JPG, PNG, GIF (Max 2MB)
                        </small>
                        <div id="previewContainer" style="display:none; margin-top: 1rem;">
                            <small class="text-muted" style="display: block; margin-bottom: 0.5rem;">Preview Gambar:</small>
                            <img id="previewImage" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; height: auto;">
                        </div>
                    </div>

                    <!-- Info Penyewa -->
                    <div style="background: var(--bg-light); padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                        <h6 style="margin-bottom: 1rem; font-weight: 700;">
                            <i class="fas fa-user-circle"></i> Data Penyewa
                        </h6>
                        <table class="table table-sm table-borderless" style="margin-bottom: 0;">
                            <tr>
                                <td style="color: var(--text-light); width: 30%;">Nama</td>
                                <td style="font-weight: 700;"><?= htmlspecialchars($_SESSION['user']['name']) ?></td>
                            </tr>
                            <tr>
                                <td style="color: var(--text-light); width: 30%;">Email</td>
                                <td style="font-weight: 700;"><?= htmlspecialchars($_SESSION['user']['email']) ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Alert -->
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>Perhatian:</strong> Pastikan semua data sudah benar sebelum submit. 
                        Bukti pembayaran akan diverifikasi oleh admin.
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-detail flex-grow-1" style="padding: 12px 20px; font-size: 1rem;">
                            <i class="fas fa-check-circle"></i> Kirim Booking
                        </button>
                        <a href="car_detail.php?id=<?= $car_id ?>" class="btn btn-outline-secondary" style="padding: 12px 20px; font-size: 1rem;">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Hitung harga otomatis
document.getElementById('startDate').addEventListener('change', hitungHarga);
document.getElementById('endDate').addEventListener('change', hitungHarga);

function hitungHarga() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        
        if (end <= start) {
            alert('Tanggal akhir harus lebih besar dari tanggal mulai!');
            document.getElementById('endDate').value = '';
            return;
        }
        
        const durasi = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
        const hargaPerHari = <?= $car['price_per_day'] ?>;
        const total = durasi * hargaPerHari;
        
        document.getElementById('durasi').textContent = durasi;
        document.getElementById('totalHarga').textContent = 
            'Rp ' + total.toLocaleString('id-ID');
    }
}

// Preview gambar bukti pembayaran
document.getElementById('proofFile').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validasi ukuran file
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file maksimal 2MB');
            this.value = '';
            return;
        }

        // Validasi tipe file
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('Format file harus JPG, PNG, atau GIF');
            this.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('previewImage').src = event.target.result;
            document.getElementById('previewContainer').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

// Validasi form sebelum submit
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const proof = document.getElementById('proofFile').files.length;
    
    if (!startDate || !endDate || proof === 0) {
        e.preventDefault();
        alert('Harap isi semua field dengan benar!');
    }
});
</script>