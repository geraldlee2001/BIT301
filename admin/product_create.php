<?php
require_once '../php/databaseConnection.php';
require_once '../php/tokenDecoding.php';

use Ramsey\Uuid\Uuid;

$merchantId = $decoded->merchantId;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $productCode = $_POST['productCode'];

    // Check if product code already exists
    $checkQuery = "SELECT * FROM product WHERE productCode = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $productCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Product code already exists. Please use a different code.');</script>";
    } else {
        // Handle file upload
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageUrl = ''; // Store image path
        if (!empty($_FILES['product_image']['name'])) {
            $fileExt = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'png', 'jpeg'];

            if (in_array($fileExt, $allowedExts) && $_FILES['product_image']['size'] <= 5 * 1024 * 1024) {
                $fileName = uniqid('product_') . '.' . $fileExt;
                $imageUrl = $uploadDir . $fileName;
                move_uploaded_file($_FILES['product_image']['tmp_name'], $imageUrl);
            } else {
                echo "<script>alert('Invalid file format or size exceeds 5MB!');</script>";
                exit;
            }
        }
        $productId = Uuid::uuid4();

        // Insert into database
        $insertQuery = "INSERT INTO product (ID,name, description, date, time, productCode, merchantId, imageUrl) VALUES (?,?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ssssssss", $productId, $name, $description, $date, $time, $productCode, $merchantId, $imageUrl);

        if ($stmt->execute()) {
            echo "<script>alert('Product created successfully!'); window.location.href='ticket_type_management.php?id=" . $productId . "';</script>";
        } else {
            echo "<script>alert('Failed to create product: " . $conn->error . "');</script>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <?php include "./component/header.php"; ?>
    <div id="layoutSidenav">
        <?php include "./component/sidebar.php"; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Create Product</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="products.php">Products</a></li>
                        <li class="breadcrumb-item active">Create Product</li>
                    </ol>

                    <form action="product_create.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" id="date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Time</label>
                            <input type="time" name="time" id="time" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Product Code</label>
                            <input type="text" name="productCode" class="form-control" maxlength="20" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Image (JPG, PNG, JPEG only, Max 5MB)</label>
                            <input type="file" name="product_image" class="form-control" accept=".jpg, .png, .jpeg">
                        </div>

                        <button type="submit" class="btn btn-primary">Create Product</button>
                    </form>
                </div>
            </main>
            <?php include '../component/organizer_footer.php'; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>

</html>