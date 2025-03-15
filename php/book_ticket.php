<?php
require_once '../vendor/autoload.php';
include "./databaseConnection.php";
include "./tokenDecoding.php";

use Ramsey\Uuid\Uuid;

// POST data
$userId = $decoded->userId;
$eventId = $_POST['eventId'];
$promoCode = $_POST['promoCode'] ?? '';
$selectedSeats = isset($_POST['selectedSeats']) ? explode(",", $_POST['selectedSeats']) : [];

$bookingId = Uuid::uuid4()->toString();
$seatPrice = 50;
$totalPrice = $seatPrice * count($selectedSeats);

// Apply promo
if (!empty($promoCode)) {
  $promoResult = $conn->query("SELECT * FROM promo_codes WHERE code='$promoCode' AND validUntil >= CURDATE()");
  if ($promo = $promoResult->fetch_assoc()) {
    $discount = $promo['discountPercent'];
    $totalPrice = $totalPrice - ($totalPrice * $discount / 100);
  }
}

// Insert booking
$insertBookingSQL = "INSERT INTO bookings (id, userId, productId, totalPrice, promoCode, status)
                     VALUES ('$bookingId', '$userId', '$eventId', '$totalPrice', '$promoCode', 'PENDING')";
if (!$conn->query($insertBookingSQL)) {
  die("Failed to create booking: " . $conn->error);
}

// Assign seats
foreach ($selectedSeats as $seatLabel) {
  $seatLabel = trim($seatLabel);
  if (!preg_match('/^([A-Z]+)-?(\d+)$/', $seatLabel, $matches)) {
    die("Invalid seat format: $seatLabel");
  }

  $seatRow = $matches[1];
  $seatNumber = (int) $matches[2];

  $seatResult = $conn->query("SELECT id FROM seats WHERE eventId = '$eventId' AND seatRow = '$seatRow' AND seatNumber = '$seatNumber'");
  if ($seatRowData = $seatResult->fetch_assoc()) {
    $seatId = $seatRowData['id'];

    $conn->query("UPDATE seats SET isBooked = 1 WHERE id = '$seatId'");

    $insertBookingSeatSQL = "INSERT INTO booking_seats (bookingId, seatId) VALUES ('$bookingId', '$seatId')";
    if (!$conn->query($insertBookingSeatSQL)) {
      die("Error assigning seat: " . $conn->error);
    }
  } else {
    die("Seat not found or already booked: $seatLabel");
  }
}

// Redirect to checkout
header("Location: /checkout.php?totalPrice=$totalPrice&bookingId=$bookingId");
exit();
