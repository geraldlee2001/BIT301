<?php
header('Content-Type: application/json');

include '../../php/databaseConnection.php';
include '../../php/tokenDecoding.php';
require_once './promo_code_management.php';

$merchantId = $decoded->merchantId;

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    throw new Exception('Invalid request method');
  }


  $productId = isset($_POST['productId']) ? $_POST['productId'] : null;
  $code = isset($_POST['code']) ? strtoupper($_POST['code']) : null;
  $discountAmount = isset($_POST['discountAmount']) ? floatval($_POST['discountAmount']) : 0;
  $discountType = isset($_POST['discountType']) ? $_POST['discountType'] : null;
  $startDate = isset($_POST['startDate']) ? $_POST['startDate'] : null;
  $expiryDate = isset($_POST['expiryDate']) ? $_POST['expiryDate'] : null;
  $usageLimit = isset($_POST['usageLimit']) ? intval($_POST['usageLimit']) : 0;

  // Basic validation
  if (empty($code) || empty($productId) || $discountAmount <= 0 || $usageLimit <= 0) {
    throw new Exception('Please fill all required fields with valid values');
  }

  // Validate dates
  $today = new DateTime();
  $start = new DateTime($startDate);
  $expiry = new DateTime($expiryDate);

  if ($expiry <= $start) {
    throw new Exception('Expiry date must be after start date');
  }

  // Validate discount amount
  if ($discountType === 'percentage' && $discountAmount > 100) {
    throw new Exception('Percentage discount cannot exceed 100%');
  }

  // Create the promo code
  $result = createPromoCode($merchantId, $productId, $code, $discountAmount, $discountType, $startDate, $expiryDate, $usageLimit);

  echo "<script> 
  alert('Create Successfully');
</script>";
  header('Location: /admin/promo_codes.php');
} catch (Exception $e) {
  http_response_code(400);
  header('Location: /admin/create_promo.php');
  echo json_encode([
    'success' => false,
    'message' => $e->getMessage()
  ]);
}
