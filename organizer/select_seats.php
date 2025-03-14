<?php
require_once '../php/databaseConnection.php';
include '../component/organizer_header.php';


$eventResult = $conn->query("SELECT id, event_name FROM events");

$ticket_types = $conn->query("SELECT * FROM ticket_types");

$seats = [];
if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];
    $seatResult = $conn->query("SELECT * FROM seats WHERE event_id = $event_id");

    while ($row = $seatResult->fetch_assoc()) {
        $seats[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>选择座位</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .seat {
            width: 40px;
            height: 40px;
            text-align: center;
            line-height: 40px;
            margin: 5px;
            cursor: pointer;
            border: 1px solid black;
            display: inline-block;
        }
        .available {
            background-color: lightgray;
        }
        .booked {
            background-color: red;
            color: white;
            pointer-events: none;
        }
        .selected {
            background-color: blue;
            color: white;
        }
    </style>
    <script>
        function selectSeat(seatId) {
            let seat = document.getElementById(seatId);
            let input = document.getElementById("selectedSeats");
            let selectedSeats = input.value ? input.value.split(",") : [];

            if (seat.classList.contains("selected")) {
                seat.classList.remove("selected");
                selectedSeats = selectedSeats.filter(s => s !== seatId);
            } else {
                seat.classList.add("selected");
                selectedSeats.push(seatId);
            }

            input.value = selectedSeats.join(",");
        }
    </script>
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-4">Choose Seat</h2>
        <form method="POST" action="select_seats.php">
            <label class="form-label">Choose Event:</label>
            <select name="event_id" class="form-control" id="eventSelect" onchange="this.form.submit()">
                <option value="">Select Events</option>
                <?php while ($event = $eventResult->fetch_assoc()): ?>
                    <option value="<?= $event['id']; ?>"><?= $event['event_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </form>

        <form method="POST" action="save_seats.php">
            <input type="hidden" name="event_id" value="<?= $_GET['event_id'] ?? ''; ?>">
            
            <label class="form-label">Choose Ticket Type:</label>
            <select name="ticket_type" class="form-control">
                <?php while ($type = $ticket_types->fetch_assoc()): ?>
                    <option value="<?= $type['id']; ?>"><?= $type['name']; ?></option>
                <?php endwhile; ?>
            </select>

            <label class="form-label">Prices:</label>
            <input type="number" name="price" class="form-control" required>

            <label class="form-label">Choose Seat:</label>
            <div>
                <?php
                foreach ($seats as $seat) {
                    $status = $seat["status"];
                    $seat_id = $seat["id"];
                    $seat_class = $status === "available" ? "available" : "booked";
                    echo "<div id='$seat_id' class='seat $seat_class' onclick='selectSeat(\"$seat_id\")'>$seat_id</div>";
                }
                ?>
            </div>

            <input type="hidden" name="selected_seats" id="selectedSeats">
            
            <button type="submit" class="btn btn-primary mt-3">保存选择</button>
        </form>
    </body>
    <script>
        function selectSeat(seatId) {
            let seat = document.getElementById(seatId);
            let input = document.getElementById("selectedSeats");
            let selectedSeats = input.value ? input.value.split(",") : [];

            if (seat.classList.contains("selected")) {
                seat.classList.remove("selected");
                selectedSeats = selectedSeats.filter(s => s !== seatId);
            } else {
                seat.classList.add("selected");
                selectedSeats.push(seatId);
            }

            input.value = selectedSeats.join(",");
        }
    </script>
</html>
