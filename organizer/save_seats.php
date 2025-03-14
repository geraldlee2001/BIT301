<?php
require_once '../php/databaseConnection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_id = $_POST['event_id'] ?? '';
    $ticket_type = $_POST['ticket_type'] ?? '';
    $price = $_POST['price'] ?? '';
    $selected_seats = $_POST['selected_seats'] ?? '';

    if (empty($event_id) || empty($ticket_type) || empty($price) || empty($selected_seats)) {
        die("所有字段都必须填写！");
    }

    $seat_ids = explode(',', $selected_seats);
    
    foreach ($seat_ids as $seat_id) {
        $seat_id = intval($seat_id); 
        
        $checkSeat = $conn->query("SELECT * FROM seats WHERE id = $seat_id AND event_id = $event_id AND status = 'available'");
        
        if ($checkSeat->num_rows > 0) {
            $updateSeat = $conn->query("UPDATE seats SET status = 'booked', ticket_type = '$ticket_type', price = $price WHERE id = $seat_id AND event_id = $event_id");
            
            if (!$updateSeat) {
                die("座位更新失败: " . $conn->error);
            }
        } else {
            echo "座位 $seat_id 不可用或已被预订！<br>";
        }
    }
    
    echo "座位预订成功！<a href='select_seats.php?event_id=$event_id'>返回选择座位</a>";
}
?>
