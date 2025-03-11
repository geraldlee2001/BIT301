<?php
include "./tokenDecoding.php";
include './databaseConnection.php';
require_once '../vendor/autoload.php'; // Assuming ramsey/uuid is used

use Ramsey\Uuid\Uuid;

header('Content-Type: application/json');

// Parse JSON input
$input = json_decode(file_get_contents("php://input"), true);
$productID = $input['productId'];
$seats = $input['seats']; // array of seats
$cartId = $decoded->cartId; // from tokenDecoding

$errors = [];
$success = [];

foreach ($seats as $seat) {
  $cartItemId = Uuid::uuid4()->toString();

  // Check if seat already exists in this cart
  $checkQuery = "SELECT * FROM cartitem 
    WHERE productId = \"$productID\" AND seat = \"$seat\" 
    AND id IN (SELECT cart_item_id FROM cartcartItem WHERE cart_id = \"$cartId\")";

  $checkResult = $conn->query($checkQuery);

  if ($checkResult->num_rows > 0) {
    $errors[] = "Seat $seat already in cart.";
    continue;
  }

  // Insert into cartitem (seat stored)
  $insertCartItem = "INSERT INTO cartitem (id, productId, seat) VALUES (\"$cartItemId\", \"$productID\", \"$seat\")";
  $conn->query($insertCartItem);

  // Link to cart
  $insertLink = "INSERT INTO cartcartItem (cart_id, cart_item_id) VALUES (\"$cartId\", \"$cartItemId\")";
  $conn->query($insertLink);

  $success[] = $seat;
}

echo json_encode([
  "added" => $success,
  "errors" => $errors
]);
