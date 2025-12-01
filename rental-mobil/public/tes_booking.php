<?php
session_start();
require_once "../app/config/db.php";

echo "<h2>🔧 Debug Booking System</h2>";

// Test database connection
if ($conn) {
    echo "✅ Database connected<br>";
} else {
    echo "❌ Database connection failed: " . mysqli_connect_error() . "<br>";
    exit;
}

// Test session
if (isset($_SESSION['user'])) {
    echo "✅ User logged in: " . $_SESSION['user']['name'] . "<br>";
} else {
    echo "❌ User not logged in<br>";
}

// Test tables existence
$tables = ['users', 'cars', 'bookings', 'payment_proof'];
foreach ($tables as $table) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($result) > 0) {
        echo "✅ Table '$table' exists<br>";
    } else {
        echo "❌ Table '$table' missing<br>";
    }
}

// Test if there are cars available
$cars_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM cars WHERE status = 'available'");
$cars_count = mysqli_fetch_assoc($cars_result)['total'];
echo "🚗 Available cars: " . $cars_count . "<br>";

// Show recent bookings
echo "<h3>Recent Bookings:</h3>";
$bookings_result = mysqli_query($conn, "SELECT * FROM bookings ORDER BY created_at DESC LIMIT 5");
if (mysqli_num_rows($bookings_result) > 0) {
    while ($booking = mysqli_fetch_assoc($bookings_result)) {
        echo "📅 Booking ID: " . $booking['booking_id'] . " - User: " . $booking['user_id'] . " - Status: " . $booking['status'] . "<br>";
    }
} else {
    echo "No bookings found<br>";
}

// Test form for manual booking
echo "<h3>Test Booking Form:</h3>";
echo '<form action="../app/controllers/BookingController.php?action=store" method="POST" enctype="multipart/form-data">';
echo '<input type="hidden" name="user_id" value="' . ($_SESSION['user']['user_id'] ?? 1) . '">';
echo '<input type="hidden" name="car_id" value="1">';
echo 'Start Date: <input type="date" name="start_date" value="' . date('Y-m-d') . '"><br>';
echo 'End Date: <input type="date" name="end_date" value="' . date('Y-m-d', strtotime('+2 days')) . '"><br>';
echo 'Proof: <input type="file" name="proof"><br>';
echo '<button type="submit">Test Booking</button>';
echo '</form>';
?>