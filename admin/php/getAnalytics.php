<?php
include '../../php/databaseConnection.php';

header('Content-Type: application/json');

// Get input parameters
$period = $_GET['period'] ?? 'daily';
$eventId = isset($_GET['eventId']) ? intval($_GET['eventId']) : null;

// Define groupBy clause based on period
switch ($period) {
  case 'weekly':
    $groupByPeriod = "YEARWEEK(ph.createdAt)";
    break;
  case 'monthly':
    $groupByPeriod = "DATE_FORMAT(ph.createdAt, '%Y-%m')";
    break;
  default:
    $groupByPeriod = "DATE(ph.createdAt)";
    break;
}

// Build base SQL query
$sql = "
SELECT
  $groupByPeriod AS period,
  ci.productId,
  COUNT(DISTINCT ph.id) AS total_tickets,
  SUM(ph.totalAmount) AS revenue,
  SUM(JSON_LENGTH(ci.seat)) AS seat_occupancy
FROM purchasehistory ph
JOIN cart c ON ph.cartId = c.id
JOIN cartcartitem cci ON c.id = cci.cart_id
JOIN cartitem ci ON cci.cart_item_id = ci.id
WHERE 1=1
";

// Apply event filter if provided
if ($eventId) {
  $sql .= " AND ci.productId = $eventId";
}

$sql .= " GROUP BY period, ci.productId ORDER BY period ASC, ci.productId ASC";

// Execute and prepare response
$data = [];
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $data[] = [
      'period' => $row['period'],
      'productId' => $row['productId'],
      'total_tickets' => $row['total_tickets'],
      'revenue' => $row['revenue'],
      'seat_occupancy' => $row['seat_occupancy']
    ];
  }
}

echo json_encode($data);
