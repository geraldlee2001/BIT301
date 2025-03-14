<?php
session_start();
if (!isset($_SESSION["organizer_id"])) {
    header("Location: login.php");
    exit();
}
include '../php/databaseConnection.php';

// 确保用户已登录

$organizer_id = $_SESSION['organizer_id'];

// 获取当前组织者创建的活动
$sql = "SELECT * FROM events WHERE organizer_id = '$organizer_id'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<body>
    <div class="container mt-5">
        <!DOCTYPE html>
        <html lang="en">

        <body>
            <?php include "../component/organizer_header.php"; ?>
            <h3 class="mt-4">Your Events</h3>
            <a href="create_events.php" class="btn btn-primary">Create Event</a>
            <a href="browse_events.php" class="btn btn-secondary">Browse Events</a>
            <a href="view_report.php" class="btn btn-info">View Reports</a>
            <a href="waiting_list.php" class="btn btn-warning">View Waiting List</a>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Location</th>
                        <th>Settings</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc())
                    { ?>
                        <tr>
                            <td><?php echo $row['event_name']; ?></td>
                            <td><?php echo $row['event_date']; ?></td>
                            <td><?php echo $row['event_time']; ?></td>
                            <td><?php echo $row['event_location']; ?></td>
                            <td><a href="event_settings.php?event_id=<?php echo $row['id']; ?>"
                                    class="btn btn-secondary">Manage</a></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
    </div>
    <?php include "../component/organizer_footer.php"; ?>
</body>

</html>