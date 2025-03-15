<?php
include "./php/databaseConnection.php";
include "./php/tokenDecoding.php";

use Firebase\JWT\JWT;
use Ramsey\Uuid\Uuid;

$key = 'bit210';
$newCartId = Uuid::uuid4();
$code = bin2hex(random_bytes(10));

$bookingId = $_GET['bookingId'] ?? '';
$status = $_GET['status'] ?? 'failed';
$price = $_GET['price'] ?? 0;

echo $bookingId;

// Get user info
$userId = $decoded->userId;
$customerId = $decoded->customerId;

// Check booking
$bookingQuery = "SELECT * FROM bookings WHERE id = '$bookingId'";
$bookingResult = $conn->query($bookingQuery);
$booking = $bookingResult->fetch_assoc();

if (!$booking) {
  die("Invalid booking ID.");
}

if ($status === 'success') {
  // 1. Update booking status to CONFIRMED
  $updateBookingQuery = "UPDATE bookings SET status = 'CONFIRMED' WHERE id = '$bookingId'";
  $conn->query($updateBookingQuery);

  // 2. (Optional) Insert into booking_history or logs if needed

  // 3. (Optional) Clear any temporary user data

  // 4. Refresh token (if needed)
  $payload = array(
    "customerId" => $decoded->customerId,
    "cartId" => $newCartId, // still used if cart logic exists elsewhere
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
      <strong><?php echo htmlspecialchars($bookingId); ?></strong>
      <p>Your event booking has been confirmed!</p>
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