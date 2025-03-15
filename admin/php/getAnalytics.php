<?php
include '../../php/databaseConnection.php';
include '../../php/tokenDecoding.php';
header('Content-Type: application/json');

$role = $decoded->role ?? 'merchant';
$period = $_GET['period'] ?? 'daily';
$eventId = isset($_GET['eventId']) ? intval($_GET['eventId']) : null;

switch ($period) {
  case 'weekly':
    $groupByPeriod = "YEARWEEK(b.createdAt)";
    break;
  case 'monthly':
    $groupByPeriod = "DATE_FORMAT(b.createdAt, '%Y-%m')";
    break;
  default:
    $groupByPeriod = "DATE(b.createdAt)";
    break;
}

$data = [];



if (strtolower($role) === 'merchant') {
  if ($eventId === null) {
    return die("Current merchant does not have any events");
  }
  // Organizer Analytics - Prevent revenue duplication
  $sql = "
    SELECT
      summary.period,
      summary.productId,
      summary.total_bookings,
      summary.revenue,
      IFNULL(seat_data.total_tickets, 0) AS total_tickets
    FROM (
      SELECT 
        $groupByPeriod AS period,
        b.productId,
        COUNT(DISTINCT b.id) AS total_bookings,
        SUM(b.totalPrice) AS revenue
      FROM bookings b
      WHERE b.status = 'CONFIRMED'
      " . ($eventId ? "AND b.productId = $eventId" : "") . "
      GROUP BY period, b.productId
    ) AS summary
    LEFT JOIN (
      SELECT 
        $groupByPeriod AS period,
        b.productId,
        COUNT(bs.seatId) AS total_tickets
      FROM bookings b
      JOIN booking_seats bs ON b.id = bs.bookingId
      WHERE b.status = 'CONFIRMED'
      " . ($eventId ? "AND b.productId = $eventId" : "") . "
      GROUP BY period, b.productId
    ) AS seat_data
    ON summary.period = seat_data.period AND summary.productId = seat_data.productId
    ORDER BY summary.period ASC, summary.productId ASC
  ";

  $result = $conn->query($sql);

  if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $data[] = [
        'period' => $row['period'],
        'productId' => $row['productId'],
        'total_bookings' => (int) $row['total_bookings'],
        'revenue' => (float) $row['revenue'],
        'total_tickets' => (int) $row['total_tickets']
      ];
    }
  }
} else {
  // Admin analytics: total booked seats + total events
  $sql = "
  SELECT
    $groupByPeriod AS period,
    COUNT(DISTINCT b.productId) AS total_events,
    COUNT(bs.seatId) AS total_booked_seats
  FROM bookings b
  JOIN booking_seats bs ON b.id = bs.bookingId
  WHERE b.status IN ('CONFIRMED', 'COMPLETED') -- <-- safer condition
  GROUP BY period
  ORDER BY period ASC
";

  $result = $conn->query($sql);
  if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $data[] = [
        'period' => $row['period'],
        'total_events' => (int) $row['total_events'],
        'total_booked_seats' => (int) $row['total_booked_seats']
      ];
    }
  }
}

echo json_encode($data);
