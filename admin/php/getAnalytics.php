<?php
include '../../php/databaseConnection.php';

header('Content-Type: application/json');

// Get input parameters
$period = $_GET['period'] ?? 'daily';
$eventId = isset($_GET['eventId']) ? intval($_GET['eventId']) : null;

// Define groupBy clause
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

// Base SQL
$sql = "
SELECT
  $groupByPeriod AS period,
  b.productId,
  COUNT(DISTINCT b.id) AS total_bookings,
  SUM(b.totalPrice) AS revenue,
  COUNT(bs.seatId) AS seat_occupancy
FROM bookings b
LEFT JOIN booking_seats bs ON b.id = bs.bookingId
WHERE b.status = 'CONFIRMED'
";

// Optional event filter
if ($eventId) {
  $sql .= " AND b.productId = $eventId";
}

$sql .= " GROUP BY period, b.productId ORDER BY period ASC, b.productId ASC";

// Execute
$data = [];
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $data[] = [
      'period' => $row['period'],
      'productId' => $row['productId'],
      'total_bookings' => (int) $row['total_bookings'],
      'revenue' => (float) $row['revenue'],
      'seat_occupancy' => (int) $row['seat_occupancy']
    ];
  }
}

echo json_encode($data);
