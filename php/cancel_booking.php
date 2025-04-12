<?php
include './databaseConnection.php';
include './tokenDecoding.php'; // if you need to ensure user ownership
require_once '../vendor/autoload.php';
require_once './secrets.php';

\Stripe\Stripe::setApiKey($stripeSecretKey);

if (!isset($_GET['id'])) {
  die("Invalid booking ID.");
}

$bookingId = $_GET['id'];

// Fetch booking and event date
$sql = "
  SELECT b.id, b.status, p.date, b.userId, b.paymentIntentId, b.promoCode
  FROM bookings b
  JOIN product p ON b.productId = p.id
  WHERE b.id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $bookingId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  die("Booking not found.");
}

$booking = $result->fetch_assoc();

// Optional: Check if the booking belongs to the logged-in user
if ($booking['userId'] !== $decoded->userId) {
  die("Unauthorized access.");
}

// Check if cancellation is allowed
$eventDate = new DateTime($booking['date']);
$today = new DateTime();
$interval = $today->diff($eventDate);
$daysUntilEvent = (int)$interval->format('%r%a');

if ($daysUntilEvent < 7) {
  die("Cancellation not allowed. Bookings can only be canceled at least 7 days before the event.");
}

// If promo code was used, decrement its usage count
if (!empty($booking['promoCode'])) {
  $promoUpdateSql = "UPDATE promo_codes SET current_usage = current_usage - 1 WHERE code = ? AND current_usage > 0";
  $promoUpdateStmt = $conn->prepare($promoUpdateSql);
  $promoUpdateStmt->bind_param("s", $booking['promoCode']);
  $promoUpdateStmt->execute();
}

// Perform cancellation (change status)
$updateSql = "UPDATE bookings SET status = 'CANCELLED' WHERE id = ?";
$updateStmt = $conn->prepare($updateSql);
$updateStmt->bind_param("s", $bookingId);

// Find and release all booked seats
$seatSql = "SELECT seatId FROM booking_seats WHERE bookingId = ?";
$seatStmt = $conn->prepare($seatSql);
$seatStmt->bind_param("s", $bookingId);
$seatStmt->execute();
$seatResult = $seatStmt->get_result();

while ($seat = $seatResult->fetch_assoc()) {
  $seatId = $seat['seatId'];
  $conn->query("UPDATE seats SET isBooked = 0 WHERE id = '$seatId'");
}

// Get the product ID for waiting list notification
$productSql = "SELECT productId FROM bookings WHERE id = ?";
$productStmt = $conn->prepare($productSql);
$productStmt->bind_param("s", $bookingId);
$productStmt->execute();
$productResult = $productStmt->get_result();
$productData = $productResult->fetch_assoc();

// Notify users on waiting list about available seats
if ($productData) {
  include_once './check_and_notify.php';
  checkAvailabilityAndNotify($productData['productId']);
}

if ($updateStmt->execute()) {
  // Stripe refund
  try {
    $refund = \Stripe\Refund::create([
      'payment_intent' => $booking['paymentIntentId'],
    ]);
    echo "<script>alert('Booking successfully canceled and refund initiated.'); window.location.href = '../purchase_history.php';</script>";
  } catch (\Stripe\Exception\ApiErrorException $e) {
    echo "<script>alert('Booking canceled, but refund failed: " . $e->getMessage() . "'); window.location.href = '../purchase_history.php';</script>";
  }
} else {
  echo "Failed to cancel booking.";
}

$conn->close();
