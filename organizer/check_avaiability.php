<?php
require_once '../php/databaseConnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventDate = $_POST['event_date'];
    $eventTime = $_POST['event_time'];

    $query = "SELECT * FROM events WHERE event_date = ? AND event_time = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $eventDate, $eventTime);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "unavailable";
    } else {
        echo "available";
    }
}
?>
