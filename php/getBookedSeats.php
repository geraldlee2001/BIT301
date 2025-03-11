<?php
header('Content-Type: application/json');
include './databaseConnection.php';

$productId = $_GET['id'];

$sql = "SELECT seat FROM cartitem WHERE productId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $productId);
$stmt->execute();
$result = $stmt->get_result();

$bookedSeats = [];
while ($row = $result->fetch_assoc()) {
  $bookedSeats[] = $row['seat'];
}

echo json_encode(['bookedSeats' => $bookedSeats]);
