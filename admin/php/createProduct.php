<?php
include '../../php/databaseConnection.php';
require_once '../../vendor/autoload.php';

use Ramsey\Uuid\Uuid;

$id = Uuid::uuid4();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = $_POST['name'];
  $productCode = $_POST['productCode'];
  $description = $_POST['description'];
  $price = $_POST['price'];
  $amount = $_POST['amount'];
  $imageFile = $_FILES['image'];
  $merchantId = $_POST['merchantId'];

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
  $sql = "INSERT INTO product (id, name,productCode,description,price,amount,imageUrl,merchantID) 
  VALUES('$id','$name','$productCode','$description','$price','$amount','$imageFilePath','$merchantId')";
  if ($conn->query($sql) === TRUE) {
    echo  "<script>alert('Create Successful');</script>";
    header('Location: /admin/products.php');
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
}
