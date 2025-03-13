<?php
include '../php/tokenDecoding.php';
include "../php/databaseConnection.php";
$query = @!!isset($decoded->merchantId) ? "SELECT *FROM product WHERE merchantId = '$decoded->merchantId'" : "SELECT *FROM product ";
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
    <title>Event List</title>
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
                    <h1 class="mt-4">Event List</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Event List</li>
                    </ol>

                    <div class="card mb-4">
                        <div class="card-header d-flex flex-row justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-table me-1"></i>
                                Event List
                            </div>
                            <a class='btn btn-primary' href="/admin/product_create.php"> Create </a>
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Event code</th>
                                        <th>Description</th>
                                        <th>Price</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <?php
                                while ($item = $data->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $item["name"] . "</td>";
                                    echo "<td>" . $item['productCode']  . "</td>";
                                    echo "<td>" . $item['description']  . "</td>";
                                    echo "<td> RM " . $item['price']  . "</td>";
                                    echo "<td>" . $item["amount"] . "</td>";
                                    echo "<td><a href='/admin/product_detail.php?id=" . $item["ID"] . "' class='btn btn-primary btn-sm'>Edit</a> 
                                    <a href='/admin/php/deleteProduct.php?id=" . $item["ID"] . "' class='btn btn-danger btn-sm'>Delete</a></td> ";
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