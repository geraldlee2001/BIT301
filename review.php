<?php
require_once './vendor/autoload.php';
include "./php/databaseConnection.php";
include "./php/tokenDecoding.php";

$eventId = $_POST['eventId'];
$promoCode = $_POST['promoCode'] ?? '';
$selectedSeats = isset($_POST['selectedSeats']) ? explode(",", $_POST['selectedSeats']) : [];

// Get event info
$event = $conn->query("SELECT * FROM product WHERE id='$eventId'")->fetch_assoc();
$seatPrice = 50;
$totalPrice = $seatPrice * count($selectedSeats);

// Optional: Apply promo discount preview
if (!empty($promoCode)) {
  $promoResult = $conn->query("SELECT * FROM promo_codes WHERE code='$promoCode' AND validUntil >= CURDATE()");
  if ($promo = $promoResult->fetch_assoc()) {
    $discount = $promo['discountPercent'];
    $totalPrice = $totalPrice - ($totalPrice * $discount / 100);
  }
}
?>
<!DOCTYPE html>
<html>

<head>
  <title><?= htmlspecialchars($event['name']) ?> - Review</title>
  <link rel="stylesheet" href="css/styles.css" />
  <style>
    .seat {
      display: inline-block;
      padding: 6px 10px;
      margin: 4px;
      background: grey;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    /* General */
    body {
      font-family: Arial, sans-serif !important;
      background-color: #000 !important;
      color: white !important;
      text-align: center !important;
      margin: 0 !important;
      padding: 20px !important;
    }

    /* Stage / Screen */
    .screen {
      background-color: #fff !important;
      color: #000 !important;
      padding: 10px !important;
      margin: 20px auto !important;
      width: fit-content !important;
      font-weight: bold !important;
      border-radius: 8px !important;
    }
  </style>
</head>

<body>
  <h1><?= htmlspecialchars($event['name']) ?></h1>

  <form action="./php/book_ticket.php" method="POST">
    <input type="hidden" name="eventId" value="<?= htmlspecialchars($eventId) ?>">
    <input type="hidden" name="promoCode" value="<?= htmlspecialchars($promoCode) ?>">
    <input type="hidden" name="selectedSeats" value="<?= htmlspecialchars(implode(",", $selectedSeats)) ?>">

    <p>Selected Seats:</p>
    <div>
      <?php foreach ($selectedSeats as $seat): ?>
        <span class="seat"><?= htmlspecialchars($seat) ?></span>
      <?php endforeach; ?>
    </div>

    <p>Total Price: RM <?= number_format($totalPrice, 2) ?></p>

    <button type="submit">Confirm Booking</button>
  </form>
</body>

</html>