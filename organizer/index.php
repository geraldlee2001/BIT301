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
            </table>
    </div>
    <?php include "../component/organizer_footer.php"; ?>
</body>

</html>