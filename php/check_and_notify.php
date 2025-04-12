<?php
include './databaseConnection.php';

function checkAvailabilityAndNotify($productId)
{
  global $conn;

  // Get total seats for the event
  $seatsSql = "SELECT COUNT(*) as total FROM seats WHERE eventId = ?";
  $stmt = $conn->prepare($seatsSql);
  $stmt->bind_param("s", $productId);
  $stmt->execute();
  $totalSeats = $stmt->get_result()->fetch_assoc()['total'];

  // Get booked seats count
  $bookedSql = "SELECT COUNT(*) as booked FROM seats WHERE eventId = ? AND isBooked = 1";
  $stmt = $conn->prepare($bookedSql);
  $stmt->bind_param("s", $productId);
  $stmt->execute();
  $bookedSeats = $stmt->get_result()->fetch_assoc()['booked'];

  // If there are available seats
  if ($totalSeats > $bookedSeats) {
    // Get users from waiting list
    $waitingSql = "SELECT w.*, u.name, p.name as event_name 
                       FROM waiting_list w 
                       JOIN users u ON w.userId = u.id 
                       JOIN product p ON w.productId = p.id 
                       WHERE w.productId = ? AND w.status = 'WAITING' 
                       AND (w.notificationAttempts < 3 OR w.lastNotificationAttempt < DATE_SUB(NOW(), INTERVAL 24 HOUR))
                       ORDER BY w.requestDate ASC";

    $stmt = $conn->prepare($waitingSql);
    $stmt->bind_param("s", $productId);
    $stmt->execute();
    $waitingList = $stmt->get_result();

    while ($user = $waitingList->fetch_assoc()) {
      $notified = false;
      $subject = "Tickets Available - " . $user['event_name'];
      $message = "Dear {$user['name']},\n\n"
        . "Good news! Tickets are now available for {$user['event_name']}.\n"
        . "Please log in to your account and complete your booking within the next 24 hours.\n\n"
        . "Best regards,\nTicketing Team";

      // Handle notifications based on preferred contact method
      if ($user['preferredContact'] === 'EMAIL' || $user['preferredContact'] === 'BOTH') {
        if (!empty($user['email'])) {
          $notified = sendEmail($user['email'], $subject, $message);
        }
      }

      if ($user['preferredContact'] === 'PHONE' || ($user['preferredContact'] === 'BOTH' && !$notified)) {
        if (!empty($user['phone'])) {
          // Implement SMS notification here
          $notified = sendSMS($user['phone'], $message);
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
      $updateStmt->bind_param("iis", $notified, $notified, $user['id']);
      $updateStmt->execute();
    }

    return true;
  }

  return false;
}
