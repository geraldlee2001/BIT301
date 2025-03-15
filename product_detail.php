<?php
include "./php/databaseConnection.php";
$eventId = $_GET['id'];
$event = $conn->query("SELECT * FROM product WHERE id='$eventId'")->fetch_assoc();
$seats = $conn->query("SELECT * FROM seats WHERE eventId='$eventId'");
?>
<!DOCTYPE html>
<html>

<head>
  <title><?= $event['name'] ?></title>
  <link rel="stylesheet" href="css/styles.css" />
  <link rel="stylesheet" href="css/product-detail.css" />
  <style>
    .seat {
      width: 30px;
      height: 30px;
      border: 1px solid #999;
      margin: 3px;
      display: inline-block;
      text-align: center;
      cursor: pointer;
    }

    .booked {
      background-color: gray;
      pointer-events: none;
    }

    .selected {
      background-color: green;
    }
  </style>
</head>

<body>
  <h1><?= $event['name'] ?></h1>

  <form action="/review.php" method="POST">
    <input type="hidden" name="eventId" value="<?= $eventId ?>">
    <div id="seat-map">
      <?php while ($seat = $seats->fetch_assoc()) : ?>
        <div class="seat <?= $seat['isBooked'] ? 'booked' : '' ?>"
          data-id="<?= $seat['id'] ?>"
          data-row="<?= $seat['seatRow'] ?>"
          data-number="<?= $seat['seatNumber'] ?>">
          <?= $seat['seatNumber'] ?>
        </div>
      <?php endwhile; ?>
    </div>

    <p>Selected: <span id="selected-seats">None</span></p>
    <input type="hidden" name="selectedSeats" id="selectedSeatsInput">

    <label>Promo Code: <input type="text" name="promoCode" /></label>
    <button type="submit">Book</button>
  </form>

  <script src="js/product-detail.js"></script>

</body>

</html>