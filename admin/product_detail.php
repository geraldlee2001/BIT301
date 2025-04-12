<?php
include "../php/databaseConnection.php";
// Retrieve data from customer table

$query = "SELECT *FROM product where id = \"$_GET[id]\"";
$result = $conn->query($query);
$data = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Product List</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="js/productDetail.js"></script>
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

</head>

<body class="sb-nav-fixed">
    <?php include "./component/header.php" ?>
    <div id="layoutSidenav">
        <?php include "./component/sidebar.php" ?>
        <div id="layoutSidenav_content">
            <main>
                <h1 class="text-center">Product Detail</h1><br>
                <form id="productForm" action="./php/updateProduct.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="productId" value="<?php echo $_GET['id'] ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $data['name'] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="productCode" class="form-label">Product Code</label>
                        <input type="text" class="form-control" id="productCode" name="productCode" value="<?php echo $data['productCode'] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5"><?php echo $data['description'] ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <img id="imagePreview" src="../<?php echo $data['imageUrl'] ?>" alt="Preview">
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="./ticket_type_management.php?id=<?php echo $_GET['id'] ?>" class="btn btn-secondary mt-3">Manage Tickets</a>
                </form>

                <div class="qr-code-section mt-4">
                    <h3>Scan QR Code to View Seats</h3>
                    <div id="qrcode" class="mt-3"></div>
                    <script>
                        // Generate QR code when page loads
                        window.onload = function() {
                            generateQRCode('<?php echo $_GET["id"] ?>');
                        };
                    </script>
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

<style>
    /* QR Code section styling */
    .qr-code-section {
        text-align: center;
        margin: 20px auto;
        max-width: 500px;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #fff;
    }

    #qrcode {
        display: flex;
        justify-content: center;
        margin: 15px 0;
    }

    #qrcode img {
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 5px;
    }

    /* General form styling */
    #productForm {
        max-width: 500px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #fff;
    }

    /* Label styling */
    #productForm label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    /* Input field styling */
    #productForm input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        outline: none;
        font-size: 14px;
    }

    /* Button styling */
    #productForm button {
        display: block;
        background-color: #007bff;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        margin-top: 10px;
    }

    /* Image preview styling */
    #imagePreview {
        width: 100%;
        height: auto;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }
</style>