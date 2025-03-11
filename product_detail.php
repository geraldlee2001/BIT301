<?php
$productId = $_GET['id']; // get product ID from URL
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Event Seat Booking</title>
  <link rel="stylesheet" href="css/styles.css" />
  <link rel="stylesheet" href="css/product-detail.css" />
</head>

<body>
  <?php include "./component/header.php"; ?>
  <div class="container-fluid h-100">
    <div class="container">
      <div class="row d-flex justify-content-center align-items-center">
        <div class="col-10 mt-10">
          <h1>🎟️ Event Seat Booking</h1>

          <div class="screen">STAGE / SCREEN</div>

          <div class="legend">
            <div><span class="seat available"></span> Available</div>
            <div><span class="seat selected"></span> Selected</div>
            <div><span class="seat booked"></span> Booked</div>
          </div>

          <div class="seat-container-wrapper">
            <div class="seat-container" id="seat-map"></div>
          </div>

          <div class="summary">
            <p>Selected Seats: <span id="selected-seats">None</span></p>
          </div>

          <button onclick="addToCart()">Add to Cart</button>
        </div>
      </div>
    </div>


    <script src="js/product-detail.js"></script>
    <script src="js/addToCart.js"></script>
</body>

</html>