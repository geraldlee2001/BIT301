<?php
require_once '../../php/databaseConnection.php';
require_once './promo_code_management.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit(json_encode(['success' => false, 'message' => 'Method not allowed']));
}

// Get and validate input
$promoId = $_POST['promoId'] ?? '';
$merchantId = $_POST['merchantId'] ?? '';
$productId = $_POST['productId'] ?? '';
$code = $_POST['code'] ?? '';
$discountAmount = $_POST['discountAmount'] ?? '';
$discountType = $_POST['discountType'] ?? '';
$startDate = $_POST['startDate'] ?? '';
$expiryDate = $_POST['expiryDate'] ?? '';
$usageLimit = $_POST['usageLimit'] ?? '';

// Basic validation
if (!$promoId || !$merchantId || !$productId || !$code || !$discountAmount || !$discountType || !$startDate || !$expiryDate || !$usageLimit) {
  exit(json_encode(['success' => false, 'message' => 'All fields are required']));
}

// Validate dates
if (strtotime($startDate) > strtotime($expiryDate)) {
  exit(json_encode(['success' => false, 'message' => 'Start date must be before expiry date']));
}

// Check if the promo code exists and belongs to the merchant
$stmt = $conn->prepare("SELECT current_usage FROM promo_codes WHERE id = ? AND merchantId = ?");
$stmt->bind_param("ii", $promoId, $merchantId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  exit(json_encode(['success' => false, 'message' => 'Promo code not found or unauthorized']));
}

$promoData = $result->fetch_assoc();
$currentUsage = $promoData['current_usage'];

// Ensure new usage limit is not less than current usage
if ($usageLimit < $currentUsage) {
  exit(json_encode(['success' => false, 'message' => 'New usage limit cannot be less than current usage']));
}

// Update the promo code
$stmt = $conn->prepare("UPDATE promo_codes SET productId = ?, code = ?, discount_amount = ?, discount_type = ?, start_date = ?, expiry_date = ?, usage_limit = ? WHERE id = ? AND merchantId = ?");
$stmt->bind_param("isdsssiii", $productId, $code, $discountAmount, $discountType, $startDate, $expiryDate, $usageLimit, $promoId, $merchantId);

if ($stmt->execute()) {
  echo json_encode(['success' => true, 'message' => 'Promo code updated successfully']);
} else {
  echo json_encode(['success' => false, 'message' => 'Error updating promo code: ' . $stmt->error]);
}
