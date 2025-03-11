<?php
include "../php/databaseConnection.php";
include "../php/tokenDecoding.php";


$merchantSql = "SELECT * FROM merchants";
$merchantResult = $conn->query($merchantSql);

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
                <h1 class="text-center">Create Product</h1><br>
                <form id="productForm" action="./php/createProduct.php" method='post' enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name">
                    </div>
                    <div class="mb-3">
                        <label for="productCode" class="form-label">Product Code</label>
                        <input type="text" class="form-control" id="productCode" name="productCode">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" class="form-control" id="price" name="price">
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount">
                    </div>
                    <?php if ($decoded->role === "ADMIN") : ?>
                        <div class="mb-3">
                            <label for="merchantId" class="form-label">Merchants</label>
                            <select name=" merchantId" id="merchantId" class="form-control">
                                <?php
                                while ($item = $merchantResult->fetch_assoc()) {
                                    echo " <option value=" . $item['ID'] . ">" . $item['merchantName'] . "</option>";
                                } ?>
                            </select>

                        </div>
                    <?php else : ?>
                        <input type="hidden" class="form-control" id="amount" name="merchantId" value="<?php echo $decoded->merchantId ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <img id="imagePreview" src="../<?php echo $data['imageUrl'] ?>" alt="Preview">
                    </div>
                    <button type="submit" class="btn btn-primary">Create</button>
                </form>

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