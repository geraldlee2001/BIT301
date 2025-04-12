<?php
require_once '../php/databaseConnection.php';
include '../php/tokenDecoding.php';

$merchantId = $decoded->merchantId;


// **获取所有活动**
$sql = "SELECT id, name, description, date, time, imageUrl FROM product WHERE merchantID = '$merchantId' ORDER BY date DESC";
$events = $conn->query($sql);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Events </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
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

<body class="sb-nav-fixed">
    <?php include "./component/header.php"; ?>
    <div id="layoutSidenav">
        <?php include "./component/sidebar.php"; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Events</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Events</li>
                    </ol>
                    <!-- Events Table -->
                    <div class="card mb-4">
                        <div class="card-header d-flex flex-row justify-content-between align-items-center">
                            <div><i class="fas fa-box me-1"></i> Events</div>
                            <a class='btn btn-primary' href="/admin/product_create.php"> Create </a>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Poster</th>
                                        <th>Event Name</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $events->fetch_assoc()) { ?>
                                        <tr>
                                            <td>
                                                <?php
                                                if (!empty($row['imageUrl'])) {
                                                    echo '<img id="imagePreview" src="../' . $row['imageUrl'] . '" width=180 height=100 alt="Preview">';
                                                } else {
                                                    echo '<img src="../uploads/default.jpg" class="event-img" alt="No Image">';
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                                            <td><?php echo htmlspecialchars($row['time']); ?></td>
                                            <td>
                                                <a href="product_detail.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
                                                <?php
                                                $eventDate = new DateTime($row['date']);
                                                $currentDate = new DateTime();
                                                $interval = $currentDate->diff($eventDate);
                                                $daysUntilEvent = $interval->days;

                                                // Only show delete button if event is more than 7 days away
                                                if ($eventDate > $currentDate && $daysUntilEvent > 7) {
                                                    echo "<button onclick=\"deleteEvent('{$row['id']}', '" . addslashes($row['name']) . "')\" class=\"btn btn-danger\">Delete</button>";
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
            <?php include "./component/footer.php"; ?>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
        crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
    <script>
        function deleteEvent(eventId, eventName) {
            if (confirm(`Are you sure you want to delete the event "${eventName}"? This action cannot be undone.`)) {
                const formData = new FormData();
                formData.append('eventId', eventId);

                fetch('php/deleteEvent.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            alert('Event deleted successfully');
                            location.reload();
                        } else {
                            alert(data.error || 'Failed to delete event');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while deleting the event. Please try again later.');
                    });
            }
        }
    </script>
</body>

</html>