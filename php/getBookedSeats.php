<?php
header('Content-Type: application/json');
include './databaseConnection.php';

$eventId = $_GET['id']; // productId is the eventId in bookings

$sql = "
  SELECT s.seatRow, s.seatNumber
  FROM booking_seats bs
  JOIN bookings b ON bs.bookingId = b.id
  JOIN seats s ON bs.seatId = s.id
  WHERE b.productId = ? AND b.status = 'CONFIRMED'
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $eventId);
$stmt->execute();
$result = $stmt->get_result();

$bookedSeats = [];
while ($row = $result->fetch_assoc()) {
  $seatLabel = $row['seatRow'] . '-' . $row['seatNumber'];
  $bookedSeats[] = $seatLabel;
}

echo json_encode(['bookedSeats' => $bookedSeats]);
