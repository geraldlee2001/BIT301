<?php
header('Content-Type: application/json');
include './databaseConnection.php';
include './tokenDecoding.php';

// Get the product ID from the request
$productId = $_POST['productId'] ?? '';
$userId = $decoded->userId ?? null;

// Validate authentication
if (!$userId) {
  http_response_code(401);
  echo json_encode(['error' => 'Unauthorized']);
  exit;
}

if (empty($productId)) {
  http_response_code(400);
  echo json_encode(['error' => 'Product ID is required']);
  exit;
}

// Get users from waiting list with contact details
$sql = "SELECT w.*, u.name FROM waiting_list w 
JOIN users u ON w.userId = u.id 
WHERE w.productId = ? AND w.status = 'WAITING' 
AND (w.notificationAttempts < 3 OR w.lastNotificationAttempt < DATE_SUB(NOW(), INTERVAL 24 HOUR))
ORDER BY w.requestDate ASC LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $productId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
  $notified = false;
  $subject = "Seats Available for " . $row['event_name'];
  $message = "Dear {$row['name']},\n\nSeats are now available for {$row['event_name']}. " .
    "Please log in to your account to complete your booking.\n\n" .
    "This offer is valid for the next 24 hours.";

  // Handle notifications based on preferred contact method
  if ($row['preferredContact'] === 'EMAIL' || $row['preferredContact'] === 'BOTH') {
    if (!empty($row['email'])) {
      $notified = sendEmail($row['email'], $subject, $message);
    }
  }

  if ($row['preferredContact'] === 'PHONE' || ($row['preferredContact'] === 'BOTH' && !$notified)) {
    if (!empty($row['phone'])) {
      // Implement SMS notification here
      $notified = sendSMS($row['phone'], $message);
    }
  }

  // Update notification attempts and status
  $updateSql = "UPDATE waiting_list 
                  SET notificationAttempts = notificationAttempts + 1,
                      lastNotificationAttempt = NOW(),
                      status = CASE WHEN ? = 1 THEN 'NOTIFIED' ELSE status END,
                      notificationDate = CASE WHEN ? = 1 THEN NOW() ELSE notificationDate END
                  WHERE id = ?";
  $updateStmt = $conn->prepare($updateSql);
  $updateStmt->bind_param("iis", $notified, $notified, $row['id']);
  $updateStmt->execute();
}

// Check for approaching event dates and notify users
function notifyEventApproaching()
{
  global $conn;

  // Get waiting list entries for events happening in 7 days
  $sql = "SELECT w.*, u.name, p.name as event_name, p.event_date 
            FROM waiting_list w 
            JOIN users u ON w.userId = u.id 
            JOIN product p ON w.productId = p.id 
            WHERE w.status = 'WAITING' 
            AND p.date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)";

  $result = $conn->query($sql);

  while ($row = $result->fetch_assoc()) {
    $notified = false;
    $subject = "Event Update: " . $row['event_name'];
    $message = "Dear {$row['name']},\n\nThe event {$row['event_name']} is approaching on {$row['event_date']}. " .
      "Unfortunately, no seats have become available yet.\n\n" .
      "We will notify you immediately if any seats become available.";

    // Send notification based on preferred contact method
    if ($row['preferredContact'] === 'EMAIL' || $row['preferredContact'] === 'BOTH') {
      if (!empty($row['email'])) {
        $notified = sendEmail($row['email'], $subject, $message);
      }
    }

    if ($row['preferredContact'] === 'PHONE' || ($row['preferredContact'] === 'BOTH' && !$notified)) {
      if (!empty($row['phone'])) {
        $notified = sendSMS($row['phone'], $message);
      }
    }
  }
}

// Handle the notification type based on request
$notificationType = $_GET['type'] ?? '';

if ($notificationType === 'available_seats') {
  $productId = $_GET['productId'] ?? '';
  if (empty($productId)) {
    http_response_code(400);
    echo json_encode(['error' => 'Product ID is required']);
    exit;
  }
  notifyAvailableSeats($productId);
  echo json_encode(['success' => true, 'message' => 'Notifications sent for available seats']);
} elseif ($notificationType === 'event_approaching') {
  notifyEventApproaching();
  echo json_encode(['success' => true, 'message' => 'Notifications sent for approaching events']);
} else {
  http_response_code(400);
  echo json_encode(['error' => 'Invalid notification type']);
}
