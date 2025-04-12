<?php
require_once '../../php/databaseConnection.php';
include '../../php/tokenDecoding.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['error' => 'Method not allowed']);
  exit;
}

// Get the event ID from POST data
$eventId = isset($_POST['eventId']) ? $_POST['eventId'] : null;
$merchantId = $decoded->merchantId;

if (!$eventId) {
  http_response_code(400);
  echo json_encode(['error' => 'Event ID is required']);
  exit;
}

// Verify that the event belongs to the merchant and check event date
$checkSql = "SELECT id, date FROM product WHERE id = ? AND merchantID = ?";
$stmt = $conn->prepare($checkSql);
$stmt->bind_param('is', $eventId, $merchantId);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if ($result->num_rows === 0) {
  http_response_code(403);
  echo json_encode(['error' => 'Unauthorized to delete this event']);
  exit;
}

// Check if event date is more than 7 days away
$eventDate = strtotime($event['date']);
$sevenDaysFromNow = strtotime('+7 days');

if ($eventDate <= $sevenDaysFromNow) {
  http_response_code(403);
  echo json_encode(['error' => 'Events can only be deleted if they are more than 7 days away']);
  exit;
}

// Delete the event
$deleteSql = "DELETE FROM product WHERE id = ? AND merchantID = ?";
$stmt = $conn->prepare($deleteSql);
$stmt->bind_param('ss', $eventId, $merchantId);

if ($stmt->execute()) {
  echo json_encode(['success' => true, 'message' => 'Event deleted successfully']);
} else {
  http_response_code(500);
  echo json_encode(['error' => 'Failed to delete event']);
}

$stmt->close();
$conn->close();
