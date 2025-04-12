<?php
header('Content-Type: application/json');
include './databaseConnection.php';

$eventId = $_GET['id'];

// Count total seats for the event
$totalSeatsQuery = "SELECT COUNT(*) as total FROM seats WHERE eventId = ?";
$stmt = $conn->prepare($totalSeatsQuery);
$stmt->bind_param("s", $eventId);
$stmt->execute();
$totalResult = $stmt->get_result()->fetch_assoc();
$totalSeats = $totalResult['total'];

// Count booked seats for the event
$bookedSeatsQuery = "SELECT COUNT(*) as booked FROM seats WHERE eventId = ? AND isBooked = 1";
$stmt = $conn->prepare($bookedSeatsQuery);
$stmt->bind_param("s", $eventId);
$stmt->execute();
$bookedResult = $stmt->get_result()->fetch_assoc();
$bookedSeats = $bookedResult['booked'];

// Check if all seats are booked
$available = $bookedSeats < $totalSeats;

echo json_encode(['available' => $available]);
