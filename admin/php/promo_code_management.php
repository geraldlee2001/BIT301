<?php
include __DIR__ . '/../../php/databaseConnection.php';


use Ramsey\Uuid\Uuid;


function createPromoCode($merchantId, $productId, $code, $discountAmount, $discountType, $startDate, $expiryDate, $usageLimit)
{
  global $conn;
  $id = Uuid::uuid4()->toString();
  $stmt = $conn->prepare("INSERT INTO promo_codes (id, merchantId, productId, code, discount_amount, discount_type, start_date, expiry_date, usage_limit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

  if (!$stmt) {
    return ['success' => false, 'message' => 'Prepare failed: ' . $conn->error];
  }

  $stmt->bind_param("ssssdsssi", $id, $merchantId, $productId, $code, $discountAmount, $discountType, $startDate, $expiryDate, $usageLimit);

  if ($stmt->execute()) {
    return ['success' => true, 'message' => 'Promo code created successfully'];
  } else {
    return ['success' => false, 'message' => 'Error creating promo code: ' . $stmt->error];
  }
}

function validatePromoCode($code, $productId)
{
  global $conn;

  $stmt = $conn->prepare("SELECT * FROM promo_codes WHERE code = ? AND productId = ? AND start_date <= CURDATE() AND expiry_date >= CURDATE() AND current_usage < usage_limit");
  $stmt->bind_param("si", $code, $productId);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $promoCode = $result->fetch_assoc();
    return ['success' => true, 'data' => $promoCode];
  } else {
    return ['success' => false, 'message' => 'Invalid or expired promo code'];
  }
}

function applyPromoCode($code, $productId, $originalPrice)
{
  $validation = validatePromoCode($code, $productId);

  if (!$validation['success']) {
    return $validation;
  }

  $promoCode = $validation['data'];
  $discountAmount = $promoCode['discount_amount'];
  $discountType = $promoCode['discount_type'];

  if ($discountType === 'percentage') {
    $discountValue = $originalPrice * ($discountAmount / 100);
  } else {
    $discountValue = $discountAmount;
  }

  $finalPrice = $originalPrice - $discountValue;

  // Ensure final price is not negative
  $finalPrice = max(0, $finalPrice);

  return [
    'success' => true,
    'original_price' => $originalPrice,
    'discount_value' => $discountValue,
    'final_price' => $finalPrice
  ];
}

function incrementPromoCodeUsage($code)
{
  global $conn;

  $stmt = $conn->prepare("UPDATE promo_codes SET current_usage = current_usage + 1 WHERE code = ?");
  $stmt->bind_param("s", $code);

  return $stmt->execute();
}

function getPromoCodesByMerchant($merchantId)
{
  global $conn;

  $stmt = $conn->prepare("SELECT pc.*, p.name as product_name FROM promo_codes pc JOIN product p ON pc.productId = p.id WHERE pc.merchantId = ?");
  $stmt->bind_param("i", $merchantId);
  $stmt->execute();
  $result = $stmt->get_result();

  $promoCodes = [];
  while ($row = $result->fetch_assoc()) {
    $promoCodes[] = $row;
  }

  return $promoCodes;
}
