<?php
include '../php/tokenDecoding.php';
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "BIT301";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Retrieve data from customer table
$query = "SELECT id,fullName, phoneNumber, birthday FROM customer";
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
    <title>Customer List</title>
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
                    <h1 class="mt-4">Customer List</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Customer List</li>
                    </ol>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Customer List
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>Full Name</th>
                                        <th>Birthday</th>
                                        <th>Contact Number</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <?php
                                while ($item = $data->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $item["fullName"] . "</td>";
                                    echo "<td>" . date_format(date_create($item["birthday"]), 'd/m/Y')  . "</td>";
                                    echo "<td>" . $item["phoneNumber"] . "</td>";
                                    echo "<td><a href='customer_detail.php?id=" . $item["id"] . "' class='btn btn-primary btn-sm'>Edit</a></td>";
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