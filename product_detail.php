<?php
include "./php/databaseConnection.php";
$eventId = $_GET['id'];
$event = $conn->query("SELECT * FROM product WHERE id='$eventId'")->fetch_assoc();

// Fetch seats with their ticket types
$seatsSql = "SELECT s.seatRow, s.seatNumber, tt.name as ticketType, tt.price 
FROM seats s 
JOIN ticket_types tt ON s.ticketTypeId = tt.id 
WHERE s.eventId = ?";
$stmt = $conn->prepare($seatsSql);
$stmt->bind_param("s", $eventId);
$stmt->execute();
$result = $stmt->get_result();
$seatTickets = [];
while ($row = $result->fetch_assoc()) {
  $seatId = $row['seatRow'] . '-' . $row['seatNumber'];
  $seatTickets[$seatId] = [
    'type' => $row['ticketType'],
    'price' => $row['price']
  ];
}

// Seat map configuration
$seatRanges = [
  'A' => [[1, 8], [15, 33], [36, 43]],
  'B' => [[1, 10], [15, 34], [36, 44]],
  'C' => [[1, 11], [15, 33], [36, 46]],
  'D' => [[1, 12], [15, 34], [36, 47]],
  'E' => [[1, 12], [15, 31], [36, 47]],
  'F' => [[1, 12], [15, 32], [36, 47]],
  'G' => [[1, 12], [15, 31], [36, 47]],
  'H' => [[1, 11], [15, 32], [36, 46]],
  'J' => [[1, 10], [15, 32], [36, 45]],
  'K' => [[1, 8], [15, 29], [36, 43]],
  'L' => [[1, 5], [15, 30], [36, 40]],
  'AA' => [[1, 13], [15, 36], [37, 50]],
  'BB' => [[1, 13], [15, 36], [37, 50]],
  'CC' => [[1, 13], [15, 36], [37, 50]],
  'DD' => [[1, 13], [15, 36], [37, 49]],
  'EE' => [[1, 12], [15, 35], [37, 48]]
];

// Fetch booked seats
$bookedSeatsQuery = "SELECT seatRow, seatNumber FROM seats WHERE eventId = ? AND isBooked = 1";
$stmt = $conn->prepare($bookedSeatsQuery);
$stmt->bind_param("s", $eventId);
$stmt->execute();
$result = $stmt->get_result();
$bookedSeats = [];
while ($row = $result->fetch_assoc()) {
  $bookedSeats[$row['seatRow'] . '-' . $row['seatNumber']] = true;
}
?>
<!DOCTYPE html>
<html>

<head>
  <title><?= $event['name'] ?></title>
  <link rel="stylesheet" href="css/styles.css" />
  <link rel="stylesheet" href="css/product-detail.css" />
  <style>
    .seat-map {
      display: flex;
      flex-direction: column;
      gap: 5px;
      padding: 20px;
      margin: 0 auto;
      max-width: 1200px;
    }

    .seat-row {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 10px;
    }

    .row-label {
      width: 30px;
      text-align: center;
      font-weight: bold;
      margin-right: 10px;
    }

    .seat {
      width: 45px;
      height: 45px;
      border: 1px solid #ccc;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      position: relative;
      transition: all 0.3s ease;
      margin: 2px;
    }

    .seat:hover {
      transform: scale(1.1);
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }

    .booked {
      background-color: #dc3545;
      color: white;
      pointer-events: none;
    }

    .selected {
      background-color: #28a745;
      color: white;
    }

    .seat-legend {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin: 20px 0;
    }

    .legend-item {
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .legend-box {
      width: 20px;
      height: 20px;
      border: 1px solid #ccc;
    }

    .legend-box.available {
      background-color: white;
    }

    .legend-box.booked {
      background-color: #dc3545;
    }

    .legend-box.selected {
      background-color: #28a745;
    }
  </style>
</head>

<body>
  <h1><?= $event['name'] ?></h1>

  <form action="/review.php" method="POST">
    <input type="hidden" name="eventId" value="<?= $eventId ?>">
    <div class="seat-legend">
      <div class="legend-item">
        <div class="legend-box available"></div>
        <span>Available</span>
      </div>
      <div class="legend-item">
        <div class="legend-box booked"></div>
        <span>Booked</span>
      </div>
      <div class="legend-item">
        <div class="legend-box selected"></div>
        <span>Selected</span>
      </div>
    </div>

    <div id="seat-map" class="seat-map">
      <?php foreach ($seatRanges as $row => $ranges): ?>
        <div class="seat-row">
          <div class="row-label"><?= $row ?></div>
          <?php
          foreach ($ranges as $range) {
            for ($i = $range[0]; $i <= $range[1]; $i++) {
              $seatId = $row . '-' . $i;
              $isBooked = isset($bookedSeats[$seatId]);
          ?>
              <?php
              $seatId = $row . '-' . $i;
              $ticketInfo = isset($seatTickets[$seatId]) ? $seatTickets[$seatId] : null;
              ?>
              <div class="seat <?= $isBooked ? 'booked' : '' ?>"
                data-seat="<?= $seatId ?>"
                data-row="<?= $row ?>"
                data-number="<?= $i ?>"
                data-price="<?= $ticketInfo ? $ticketInfo['price'] : 0 ?>"
                data-type="<?= $ticketInfo ? $ticketInfo['type'] : '' ?>"
                title="<?= $ticketInfo ? $ticketInfo['type'] . ' - RM' . number_format($ticketInfo['price'], 2) : 'Unavailable' ?>">
                <?= $i ?>
              </div>
          <?php
            }
          }
          ?>
        </div>
      <?php endforeach; ?>
    </div>

    <p>Selected: <span id="selected-seats">None</span></p>
    <input type="hidden" name="selectedSeats" id="selectedSeatsInput">

    <div class="price-info">
      <p>Total Price: <span id="totalPrice">RM0.00</span></p>
    </div>

    <div class="promo-code-section">
      <label>Promo Code:</label>
      <input type="text" id="promoCode" name="promoCode" />
      <button type="button" id="applyPromoCode">Apply</button>
      <p id="promoMessage"></p>
    </div>

    <div class="final-price-section" style="display: none;">
      <p>Original Price: <span id="originalPrice">RM0.00</span></p>
      <p>Discount: <span id="discountAmount">-RM0.00</span></p>
      <p>Final Price: <span id="finalPrice">RM0.00</span></p>
    </div>

    <input type="hidden" name="appliedDiscount" id="appliedDiscount">
    <button type="submit" id="bookButton">Book</button>
    <button type="button" id="waitingListButton" style="display: none;" onclick="showContactForm('<?= $eventId ?>')">Join Waiting List</button>
  </form>

  <script src="js/product-detail.js"></script>
  <script src="js/waiting-list.js"></script>
  <script>
    // Check event availability when page loads
    document.addEventListener('DOMContentLoaded', function() {
      checkEventAvailability('<?= $eventId ?>');
    });
  </script>

</body>

</html>