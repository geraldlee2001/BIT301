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

// Calculate total price based on actual ticket prices
$totalPrice = 0;
foreach ($selectedSeats as $seat) {
  list($row, $number) = explode('-', $seat);
  $seatQuery = $conn->prepare("SELECT tt.price FROM seats s JOIN ticket_types tt ON s.ticketTypeId = tt.id WHERE s.eventId = ? AND s.seatRow = ? AND s.seatNumber = ?");
  $seatQuery->bind_param('ssi', $eventId, $row, $number);
  $seatQuery->execute();
  $result = $seatQuery->get_result();
  if ($price = $result->fetch_assoc()) {
    $totalPrice += $price['price'];
  }
}

// Apply promo
if (!empty($promoCode)) {
  $promoStmt = $conn->prepare("SELECT * FROM promo_codes WHERE code = ? AND expiry_date >= CURDATE() AND current_usage < usage_limit");
  $promoStmt->bind_param('s', $promoCode);
  $promoStmt->execute();
  $promoResult = $promoStmt->get_result();

  if ($promo = $promoResult->fetch_assoc()) {
    // Calculate discount based on type
    if ($promo['discount_type'] === 'percentage') {
      $totalPrice = $totalPrice - ($totalPrice * $promo['discount_amount'] / 100);
    } else {
      $totalPrice = $totalPrice - $promo['discount_amount'];
    }

    // Increment usage count
    $updateStmt = $conn->prepare("UPDATE promo_codes SET current_usage = current_usage + 1 WHERE code = ?");
    $updateStmt->bind_param('s', $promoCode);
    $updateStmt->execute();
  }
}

// Insert booking
$insertBookingSQL = "INSERT INTO bookings (id, userId, productId, totalPrice, promoCode, status)
                     VALUES ('$bookingId', '$userId', '$eventId', '$totalPrice', '$promoCode', 'PENDING')";
if (!$conn->query($insertBookingSQL)) {
  die("Failed to create booking: " . $conn->error);
}

// Validate all seats first
$invalidSeats = [];
$bookedSeats = [];
$nonexistentSeats = [];
$validSeats = [];

foreach ($selectedSeats as $seatLabel) {
  $seatLabel = trim($seatLabel);
  if (!preg_match('/^([A-Z]+)-?(\d+)$/', $seatLabel, $matches)) {
    $invalidSeats[] = $seatLabel;
    continue;
  }

  $seatRow = $matches[1];
  $seatNumber = (int) $matches[2];

  // Use prepared statements to prevent SQL injection
  $seatStmt = $conn->prepare("SELECT id, isBooked FROM seats WHERE eventId = ? AND seatRow = ? AND seatNumber = ?");
  $seatStmt->bind_param('ssi', $eventId, $seatRow, $seatNumber);
  $seatStmt->execute();
  $seatResult = $seatStmt->get_result();

  if ($seatData = $seatResult->fetch_assoc()) {
    if ($seatData['isBooked'] == 1) {
      $bookedSeats[] = $seatLabel;
    } else {
      $validSeats[] = ['label' => $seatLabel, 'id' => $seatData['id']];
    }
  } else {
    $nonexistentSeats[] = $seatLabel;
  }
}

// Check for any validation errors
if (!empty($invalidSeats) || !empty($bookedSeats) || !empty($nonexistentSeats)) {
  $conn->query("DELETE FROM bookings WHERE id = '$bookingId'");
  $errors = [];

  if (!empty($invalidSeats)) {
    $errors[] = "Invalid seat format: " . implode(", ", $invalidSeats);
  }
  if (!empty($bookedSeats)) {
    $errors[] = "Already booked seats: " . implode(", ", $bookedSeats);
  }
  if (!empty($nonexistentSeats)) {
    $errors[] = "Non-existent seats: " . implode(", ", $nonexistentSeats);
  }

  die(json_encode(['error' => true, 'messages' => $errors]));
}

// Begin transaction for seat updates and booking
$conn->begin_transaction();

try {
  foreach ($validSeats as $seat) {
    // Update seat status
    $updateSeatStmt = $conn->prepare("UPDATE seats SET isBooked = 1 WHERE id = ? AND isBooked = 0");
    $updateSeatStmt->bind_param('s', $seat['id']);
    if (!$updateSeatStmt->execute()) {
      throw new Exception("Failed to update seat status for seat: " . $seat['label']);
    }

    // Insert booking seat record
    $insertBookingSeatStmt = $conn->prepare("INSERT INTO booking_seats (bookingId, seatId) VALUES (?, ?)");
    $insertBookingSeatStmt->bind_param('ss', $bookingId, $seat['id']);
    if (!$insertBookingSeatStmt->execute()) {
      throw new Exception("Failed to create booking seat record for seat: " . $seat['label']);
    }
  }

  $conn->commit();

  // Redirect to checkout on success
  header("Location:/php/checkout.php?totalPrice=$totalPrice&bookingId=$bookingId");
  exit();
} catch (Exception $e) {
  // Rollback transaction on error
  $conn->rollback();

  // Delete the booking record
  $conn->query("DELETE FROM bookings WHERE id = '$bookingId'");

  // Return error response
  die(json_encode([
    'error' => true,
    'messages' => [$e->getMessage()]
  ]));
}
