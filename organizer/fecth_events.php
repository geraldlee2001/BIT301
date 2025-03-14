<?php
require_once '../php/databaseConnection.php';

$events = [];
$query = "SELECT name, event_date FROM events";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $events[] = [
        'title' => $row['name'],
        'start' => $row['event_date']
    ];
}

echo json_encode($events);
?>
