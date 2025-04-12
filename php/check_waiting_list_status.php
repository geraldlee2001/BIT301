<?php
header('Content-Type: application/json');
include './databaseConnection.php';
include './tokenDecoding.php';

$productId = $_GET['productId'] ?? '';
$userId = $decoded->userId ?? null;

if (!$userId) {
  http_response_code(401);
  echo json_encode(['error' => 'Unauthorized']);
  exit;
}

if (empty($productId)) {
  http_response_code(400);
  echo json_encode(['error' => 'Product ID is required']);
  exit;
}

$sql = "SELECT id FROM waiting_list WHERE userId = ? AND productId = ? AND status = 'WAITING'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $userId, $productId);
$stmt->execute();
$result = $stmt->get_result();

$isOnWaitingList = $result->num_rows > 0;

echo json_encode([
  'success' => true,
  'isOnWaitingList' => $isOnWaitingList
]);
