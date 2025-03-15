<?php
include './databaseConnection.php';
include './tokenDecoding.php'; // if you need to ensure user ownership

if (!isset($_GET['id'])) {
  die("Invalid booking ID.");
}

$bookingId = $_GET['id'];

// Fetch booking and event date
$sql = "
  SELECT b.id, b.status, p.date, b.userId
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

// Perform cancellation (change status)
$updateSql = "UPDATE bookings SET status = 'CANCELLED' WHERE id = ?";
$updateStmt = $conn->prepare($updateSql);
$updateStmt->bind_param("s", $bookingId);

if ($updateStmt->execute()) {
  echo "<script>alert('Booking successfully canceled.'); window.location.href = '../purchase_history.php';</script>";
} else {
  echo "Failed to cancel booking.";
}

$conn->close();
