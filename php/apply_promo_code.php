<?php
require_once './databaseConnection.php';
require_once '../admin/php/promo_code_management.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $missingParams = [];
  if (!isset($_POST['code'])) {
    $missingParams[] = 'code';
  }
  if (!isset($_POST['productId'])) {
    $missingParams[] = 'productId';
  }
  if (!isset($_POST['price'])) {
    $missingParams[] = 'price';
  }

  if (!empty($missingParams)) {
    echo json_encode([
      'success' => false,
      'message' => 'Missing required parameters: ' . implode(', ', $missingParams)
    ]);
    exit;
  }

  $code = strtoupper($_POST['code']);
  $productId = intval($_POST['productId']);
  $price = floatval($_POST['price']);

  // Apply the promo code
  $result = applyPromoCode($code, $productId, $price);

  if ($result['success']) {
    // Format the prices for display
    $result['original_price_formatted'] = number_format($result['original_price'], 2);
    $result['discount_value_formatted'] = number_format($result['discount_value'], 2);
    $result['final_price_formatted'] = number_format($result['final_price'], 2);
  }

  echo json_encode($result);
} else {
  echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
