<?php
require_once '../php/databaseConnection.php';
include '../component/organizer_header.php';

// **获取所有活动**
$sql = "SELECT id, event_name, description, event_date, event_time, image FROM events ORDER BY event_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Browse Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .event-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Browse Events</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Poster</th>
                <th>Event Name</th>
                <th>Description</th>
                <th>Date</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td>
                        <?php 
                            if (!empty($row['image']) && file_exists($row['image'])) { 
                                echo '<img src="' . htmlspecialchars($row['image']) . '" class="event-img" alt="Event Poster">';
                            } else {
                                echo '<img src="../uploads/default.jpg" class="event-img" alt="No Image">';
                            }
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['event_time']); ?></td>
                    <td><a href="edit_events.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a></td>

                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php include '../component/organizer_footer.php'; ?>
</body>
</html>
