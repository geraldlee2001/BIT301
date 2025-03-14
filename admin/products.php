<?php
require_once '../php/databaseConnection.php';
include '../component/organizer_header.php';
include '../php/tokenDecoding.php';

// **获取所有活动**
$sql = "SELECT id, event_name, description, event_date, event_time, image FROM events ORDER BY event_date DESC";
$events = $conn->query($sql);

// **获取所有产品**
$query = isset($decoded->merchantId) ? "SELECT * FROM product WHERE merchantId = '$decoded->merchantId'" : "SELECT * FROM product";
$products = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Events & Products</title>
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
                    <h1 class="mt-4">Events & Products</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Events & Products</li>
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
                                    <?php while ($row = $events->fetch_assoc())
                                    { ?>
                                        <tr>
                                            <td>
                                                <?php
                                                if (!empty($row['image']) && file_exists($row['image']))
                                                {
                                                    echo '<img src="' . htmlspecialchars($row['image']) . '" class="event-img" alt="Event Poster">';
                                                }
                                                else
                                                {
                                                    echo '<img src="../uploads/default.jpg" class="event-img" alt="No Image">';
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                                            <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                                            <td><?php echo htmlspecialchars($row['event_time']); ?></td>
                                            <td><a href="edit_events.php?id=<?php echo $row['id']; ?>"
                                                    class="btn btn-warning">Edit</a></td>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
        crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>

</html>