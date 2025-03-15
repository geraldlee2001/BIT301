<?php
include "./php/tokenDecoding.php";
include "./php/databaseConnection.php";

$userId = $decoded->userId;

$sql = "
  SELECT 
    b.id AS bookingId,
    b.totalPrice,
    b.createdAt AS purchasedAt,
    b.status,
    b.promoCode,
    p.id AS productId,
    p.name AS productName,
    p.imageUrl AS productImageUrl,
    p.date,
    p.time,
    s.seatRow,
    s.seatNumber
  FROM bookings b
  JOIN booking_seats bs ON b.id = bs.bookingId
  JOIN seats s ON bs.seatId = s.id
  JOIN product p ON b.productId = p.id
  WHERE b.userId = ? AND b.status IN ('CONFIRMED', 'CANCELLED')
  ORDER BY b.createdAt DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Group by booking
$purchases = [];
while ($row = $result->fetch_assoc()) {
  $bookingId = $row['bookingId'];

  if (!isset($purchases[$bookingId])) {
    $purchases[$bookingId] = [
      'bookingId' => $bookingId,
      'productId' => $row['productId'],
      'productName' => $row['productName'],
      'productImage' => $row['productImageUrl'],
      'eventDate' => $row['date'],
      'purchasedAt' => $row['purchasedAt'],
      'totalPrice' => $row['totalPrice'],
      'promoCode' => $row['promoCode'],
      'status' => $row['status'],
      'seats' => [],
    ];
  }

  $purchases[$bookingId]['seats'][] =  $row['seatRow'] . '-' . $row['seatNumber'];
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Purchased History</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link href="css/styles.css" rel="stylesheet" />
</head>

<body>
  <?php include "./component/header.php"; ?>

  <div class="container-fluid h-100" style="background-color: #eee;">
    <div class="container py-5">
      <div class="row d-flex justify-content-center align-items-center">
        <div class="col-10 mt-6">
          <h3 class="fw-normal mb-4 text-black">Purchased History</h3>

          <?php if (empty($purchases)): ?>
            <p>No purchase history found.</p>
          <?php else: ?>
            <?php foreach ($purchases as $purchase): ?>
              <?php
              $today = new DateTime();
              $eventDate = new DateTime($purchase['eventDate']);
              $interval = $today->diff($eventDate);
              $daysUntilEvent = (int)$interval->format('%r%a');
              $canCancel = $daysUntilEvent >= 7 && $purchase['status'] === 'CONFIRMED';
              ?>
              <div class="card rounded-3 mb-4">
                <div class="card-body p-4">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                      <h5 class="mb-0">
                        <strong>Event:</strong> <?= $purchase['productName'] ?>
                        <?php if ($purchase['status'] === 'CANCELLED'): ?>
                          <span class="badge badge-danger ml-2">CANCELLED</span>
                        <?php endif; ?>
                      </h5>
                      <small class="text-muted">Purchased at: <?= $purchase['purchasedAt'] ?></small><br>
                      <small class="text-muted">Event Date: <?= $purchase['eventDate'] ?></small>
                      <?php if ($purchase['promoCode']): ?>
                        <br><small class="text-success">Promo Applied: <?= $purchase['promoCode'] ?></small>
                      <?php endif; ?>
                    </div>
                    <div>
                      <a class="btn btn-primary" href="/php/generateReceipt.php?id=<?= $purchase['bookingId'] ?>">Generate receipt</a>
                      <?php if ($canCancel): ?>
                        <a class="btn btn-danger" href="/php/cancel_booking.php?id=<?= $purchase['bookingId'] ?>"
                          onclick="return confirm('Are you sure you want to cancel this booking?');">
                          Cancel Booking
                        </a>
                      <?php elseif ($purchase['status'] === 'CONFIRMED'): ?>
                        <button class="btn btn-secondary" disabled title="Cannot cancel within 7 days of the event">
                          Cancel Unavailable
                        </button>
                      <?php endif; ?>
                    </div>
                  </div>

                  <div class="row d-flex justify-content-between align-items-center mb-3">
                    <div class="col-md-2 col-lg-2 col-xl-2">
                      <img src="<?= $purchase['productImage'] ?>" class="img-fluid rounded-3"
                        alt="<?= $purchase['productName'] ?>">
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-6">
                      <p class="fw-bold mb-1"><?= $purchase['productName'] ?></p>
                      <p class="text-muted mb-1"><strong>Seats:</strong> <?= implode(', ', $purchase['seats']) ?></p>
                    </div>
                    <div class="col-md-4 col-lg-4 col-xl-4 text-end">
                      <p class="mb-0"><strong>Total:</strong> RM <?= number_format($purchase['totalPrice'], 2) ?></p>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>

  <?php include "./component/footer.php"; ?>
</body>

</html>