<?php
header('Content-Type: application/json');
include './databaseConnection.php';

$eventId = $_GET['id'];

$sql = "SELECT seatRow, seatNumber, tt.name as ticketType, tt.price
        FROM seats s
        JOIN ticket_types tt ON s.ticketTypeId = tt.id
        WHERE s.eventId = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $eventId);
$stmt->execute();
$result = $stmt->get_result();

$assignedSeats = [];
while ($row = $result->fetch_assoc()) {
  $seatLabel = $row['seatRow'] . '-' . $row['seatNumber'];
  $assignedSeats[$seatLabel] = [
    'type' => $row['ticketType'],
    'price' => floatval($row['price'])
  ];
}

echo json_encode(['assignedSeats' => $assignedSeats]);
