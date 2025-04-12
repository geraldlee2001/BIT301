<?php
require_once './vendor/autoload.php';
include "./php/databaseConnection.php";
include "./php/tokenDecoding.php";

$eventId = $_POST['eventId'];
$promoCode = $_POST['promoCode'] ?? '';
$selectedSeats = isset($_POST['selectedSeats']) ? explode(",", $_POST['selectedSeats']) : [];

// Get event info and calculate total price
$event = $conn->query("SELECT * FROM product WHERE id='$eventId'")->fetch_assoc();

// Fetch seat prices from ticket types
$totalPrice = 0;
foreach ($selectedSeats as $seat) {
  list($row, $number) = explode('-', $seat);
  $seatQuery = $conn->prepare("SELECT tt.price FROM seats s JOIN ticket_types tt ON s.ticketTypeId = tt.id WHERE s.eventId = ? AND s.seatRow = ? AND s.seatNumber = ?");
  $seatQuery->bind_param('ssi', $eventId, $row, $number);
  $seatQuery->execute();
  $result = $seatQuery->get_result();
  if ($price = $result->fetch_assoc()) {
    $totalPrice += $price['price'];
  }
}

// Optional: Apply promo discount preview
if (!empty($promoCode)) {
  $promoResult = $conn->query("SELECT * FROM promo_codes WHERE code='$promoCode' AND expiry_date >= CURDATE() AND current_usage < usage_limit");
  if ($promo = $promoResult->fetch_assoc()) {
    $discount = ($promo['discount_type'] === 'percentage') ? $promo['discount_amount'] : ($promo['discount_amount'] / $totalPrice * 100);
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