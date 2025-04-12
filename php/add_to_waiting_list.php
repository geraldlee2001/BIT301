<?php
header('Content-Type: application/json');
include './databaseConnection.php';
include './tokenDecoding.php';

use Ramsey\Uuid\Uuid;

// Get and sanitize the request parameters
$productId = $_POST['productId'];
$userId = $decoded->userId ?? null;
$phone = $_POST['phone'];
$email = $_POST['email'];
$preferredContact = $_POST['preferredContact'] ?? 'PHONE';


// Validate contact details
if (empty($phone)) {
  http_response_code(400);
  echo json_encode(['error' => 'Phone number is required']);
  exit;
}


// Check if the product exists
$productCheckSql = "SELECT id FROM product WHERE id = ?";
$productCheckStmt = $conn->prepare($productCheckSql);
$productCheckStmt->bind_param("s", $productId);
$productCheckStmt->execute();
if ($productCheckStmt->get_result()->num_rows === 0) {
  http_response_code(404);
  echo json_encode(['error' => 'Product not found']);
  exit;
}

// Validate preferred contact method and email if required
if ($preferredContact === 'EMAIL' || $preferredContact === 'BOTH') {
  if (empty($email)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email is required for the selected contact preference']);
    exit;
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    exit;
  }
}


// Validate user authentication
if (!$userId) {
  http_response_code(401);
  echo json_encode(['error' => 'User must be logged in to join waiting list']);
  exit;
}

if (empty($productId)) {
  http_response_code(400);
  echo json_encode(['error' => 'Product ID is required']);
  exit;
}

// Check if the waiting list is full (limit to 50 people per event)
$capacityCheckSql = "SELECT COUNT(*) as count FROM waiting_list WHERE productId = ? AND status = 'WAITING'";
$capacityStmt = $conn->prepare($capacityCheckSql);
$capacityStmt->bind_param("s", $productId);
$capacityStmt->execute();
$capacityResult = $capacityStmt->get_result()->fetch_assoc();

if ($capacityResult['count'] >= 50) {
  http_response_code(400);
  echo json_encode(['error' => 'The waiting list for this event is full']);
  exit;
}

// Check if user is already in waiting list for this product
$checkSql = "SELECT id, status FROM waiting_list WHERE userId = ? AND productId = ? AND status = 'WAITING'";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("ss", $userId, $productId);
$checkStmt->execute();
$existingEntry = $checkStmt->get_result()->fetch_assoc();

if ($existingEntry) {
  http_response_code(400);
  echo json_encode(['error' => 'You are already on the waiting list for this event']);
  exit;
}

// Generate a new UUID for the waiting list entry
$waitingListId = Uuid::uuid4()->toString();

// Add user to waiting list with contact details
$sql = "INSERT INTO waiting_list (id, userId, productId, phone, email, preferredContact, status) VALUES (?, ?, ?, ?, ?, ?, 'WAITING')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $waitingListId, $userId, $productId, $phone, $email, $preferredContact);

if ($stmt->execute()) {
  echo json_encode([
    'success' => true,
    'message' => 'You have been added to the waiting list. We will notify you when seats become available.'
  ]);
} else {
  http_response_code(500);
  echo json_encode(['error' => 'Failed to add to waiting list']);
}

$conn->close();
