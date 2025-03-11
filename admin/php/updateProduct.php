<?php
require_once '../../vendor/autoload.php';
include '../../php/databaseConnection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = $_POST['name'];
  $productCode = $_POST['productCode'];
  $description = $_POST['description'];
  $price = $_POST['price'];
  $amount = $_POST['amount'];
  $productId = $_POST['productId'];
  $imageFile = $_FILES['image'];

  // If an image file was uploaded, save it to the server.
  if ($imageFile['error'] === UPLOAD_ERR_OK) {
    // Get the uploaded image file's name.
    $imageName = $imageFile['name'];

    // Get the uploaded image file's temporary path.
    $imageTempPath = $imageFile['tmp_name'];

    $destinationPath = __DIR__ . '/../../uploads/' . $imageName;
    // Save the uploaded image file to the server.
    move_uploaded_file($imageTempPath, $destinationPath);

    $imageFilePath = 'uploads/' . $imageName;
  }
  $sql = "UPDATE product SET name ='$name', productCode = '$productCode', description = '$description', price = '$price',
   amount = '$amount', imageUrl='$imageFilePath' WHERE id = \"$productId\"";
  $result = $conn->query($sql);
  if ($conn->query($sql) === TRUE) {
    echo  "<script>alert('Update Successful');</script>";
    header('Location: /admin/products.php');
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
}
