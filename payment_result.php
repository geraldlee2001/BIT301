<?php
include "./php/databaseConnection.php";
include "./php/tokenDecoding.php";

use Firebase\JWT\JWT;
use Ramsey\Uuid\Uuid;

$key = 'bit210';
$id = Uuid::uuid4();
$code = bin2hex(random_bytes(10));

$cartId = $decoded->cartId;
$customerId = $decoded->customerId;
$status = $_GET['status'] ?? 'failed';
$price = $_GET['price'] ?? 0;
$cartCode = $_GET['code'] ?? '';

$cartQuery = "SELECT * FROM cart WHERE id = \"$cartId\" AND code = \"$cartCode\"";
$cartResult = $conn->query($cartQuery);
$cartData = $cartResult->fetch_assoc();

if (!$cartData) {
  die("Invalid cart or code.");
}

if ($status === 'success') {
  // 1. Update cart status to COMPLETED
  $updateCartStatusQuery = "UPDATE cart SET status = 'COMPLETED' WHERE id = \"$cartId\"";
  $conn->query($updateCartStatusQuery);

  // 2. Insert purchase history
  $purchaseHistoryQuery = "INSERT INTO purchasehistory (id, cartId, customerId, totalAmount) VALUES ('$id', '$cartId', '$customerId', '$price')";
  $conn->query($purchaseHistoryQuery);

  // 3. Update stock
  $productQuery = "UPDATE product
    JOIN cartitem ON product.id = cartitem.productId
    JOIN cartcartitem ON cartitem.id = cartcartitem.cart_item_id
    SET product.amount = product.amount - 1
    WHERE cartcartitem.cart_id = \"$cartId\"";
  $conn->query($productQuery);

  // 4. Create new empty cart
  $newCartQuery = "INSERT INTO cart (id, customerId, status, code) VALUES ('$id', '$customerId', 'ADDING', '$code')";
  $conn->query($newCartQuery);

  // 5. Refresh token with new cartId
  $payload = array(
    "customerId" => $decoded->customerId,
    "cartId" => $id,
    "userId" => $decoded->userId,
    "username" => $decoded->username,
    "role" => $decoded->role,
  );
  $token = JWT::encode($payload, $key, 'HS256');
  setcookie("token", $token, time() + 3600 * 60, "/", "localhost");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Payment <?php echo $status === 'success' ? 'Successful' : 'Failed'; ?></title>

  <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700" rel="stylesheet" />
  <link href="css/styles.css" rel="stylesheet" />
</head>

<body>
  <?php include "./component/header.php"; ?>

  <div class="container-fluid d-flex flex-column align-items-center justify-content-center mt-5 payment-success">
    <?php if ($status === 'success'): ?>
      <h1 class="mt-10">Payment Successful</h1>
      <i class="fas fa-check-circle fa-5x" style="color:green"></i>
      <strong><?php echo htmlspecialchars($cartCode); ?></strong>
      <p>Thank you for your purchase!</p>
    <?php else: ?>
      <h1 class="mt-5">Payment Failed</h1>
      <i class="fas fa-times-circle fa-5x" style="color:red"></i>
      <p>Something went wrong. Please try again.</p>
    <?php endif; ?>
  </div>

  <style>
    body {
      margin: 0 auto;
    }

    .payment-success {
      width: 50%;
      text-align: center;
    }

    #mainNav {
      padding-top: 1.5rem;
      padding-bottom: 1.5rem;
      border: none;
      background-color: #212529;
      transition: padding-top 0.3s ease-in-out, padding-bottom 0.3s ease-in-out;
      color: white;
    }

    strong {
      font-size: 40px;
    }
  </style>
</body>

</html>