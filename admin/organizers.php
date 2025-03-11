<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "BIT301";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Retrieve data from customer table
$query = "SELECT * FROM merchants";
$data = $conn->query($query);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Organizer List</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <?php include "./component/header.php" ?>
    <div id="layoutSidenav">
        <?php include "./component/sidebar.php" ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Organizer List</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Organizer List</li>

                    </ol>

                    <div class="card mb-4">
                        <div class="card-header d-flex flex-row justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-table me-1"></i>
                                Organizer List
                            </div>
                            <a class='btn btn-primary' href="/admin/organizer_create.php"> Create </a>
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>Organizer Name</th>
                                        <th>Contact Number</th>
                                        <th>Action</th>

                                    </tr>
                                </thead>

                                <?php
                                while ($item = $data->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $item["merchantName"] . "</td>";
                                    echo "<td>" . $item["contactNumber"] . "</td>";
                                    echo "<td><a href='/admin/organizer_detail.php?id=" . $item["ID"] . "' class='btn btn-primary btn-sm'>Edit</a></td>";
                                    echo "</tr>";
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
            <?php include "./component/footer.php" ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
</body>

</html>