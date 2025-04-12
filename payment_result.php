<?php
include "./php/databaseConnection.php";
include "./php/tokenDecoding.php";
require_once './vendor/autoload.php';
require_once './php/secrets.php';

use Firebase\JWT\JWT;
use Ramsey\Uuid\Uuid;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

$key = 'bit210';
$newCartId = Uuid::uuid4();

// Get query parameters
$status = $_GET['status'] ?? 'failed';
$bookingId = $_GET['bookingId'] ?? '';
$price = $_GET['price'] ?? 0;
$paymentIntentId = $_GET['paymentIntentId'] ?? '';
$stripeSessionId = $_GET['session_id'] ?? '';

// Get user info from token
$userId = $decoded->userId;
$customerId = $decoded->customerId;

// Validate booking
$bookingStmt = $conn->prepare("SELECT * FROM bookings WHERE id = ? AND userId = ?");
$bookingStmt->bind_param("ss", $bookingId, $userId);
$bookingStmt->execute();
$bookingResult = $bookingStmt->get_result();
$booking = $bookingResult->fetch_assoc();

if (!$booking) {
  die("Invalid or unauthorized booking.");
}

// If success, confirm booking and store paymentIntentId
if ($status === 'success') {
  // If paymentIntentId not passed, get from Stripe session
  if (!$paymentIntentId && $stripeSessionId) {
    Stripe::setApiKey($stripeSecretKey);
    try {
      $session = StripeSession::retrieve($stripeSessionId);
      $paymentIntentId = $session->payment_intent;
    } catch (Exception $e) {
      die("Unable to fetch payment details from Stripe.");
    }
  }

  // Update booking
  $updateStmt = $conn->prepare("UPDATE bookings SET status = 'CONFIRMED', paymentIntentId = ? WHERE id = ?");
  $updateStmt->bind_param("ss", $paymentIntentId, $bookingId);
  $updateStmt->execute();

  // Refresh token
  $payload = array(
    "customerId" => $customerId,
    "cartId" => $newCartId,
    "userId" => $userId,
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
  <title>Payment <?php echo $status === 'success' ? 'Successful' : 'Failed'; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
  <link href="css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body>
  <?php include "./component/header.php"; ?>

  <div class="container-fluid d-flex flex-column align-items-center justify-content-center mt-10 payment-success">
    <?php if ($status === 'success'): ?>
      <h1 class="mt-4">Payment Successful</h1>
      <i class="fas fa-check-circle fa-5x text-success"></i>
      <strong>Booking ID: <?php echo htmlspecialchars($bookingId); ?></strong>
      <p class="mt-2">Your event booking has been confirmed!</p>
    <?php else: ?>
      <h1 class="mt-4">Payment Failed</h1>
      <i class="fas fa-times-circle fa-5x text-danger"></i>
      <p class="mt-2">Something went wrong. Please try again.</p>
    <?php endif; ?>
  </div>

  <style>
    body {
      margin: 50 auto;
      font-family: Arial, sans-serif;
    }

    .payment-success {
      width: 60%;
      text-align: center;
    }

    strong {
      font-size: 28px;
      display: block;
      margin-top: 10px;
    }

    .text-success {
      color: green;
    }

    .text-danger {
      color: red;
    }
  </style>
</body>

</html>