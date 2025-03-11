<?php
require_once '../../vendor/autoload.php';
include '../../php/databaseConnection.php';
$productId = $_GET['id'];
$sql = "DELETE FROM product WHERE id = \"$productId\"";
$result = $conn->query($sql);
if ($conn->query($sql) === TRUE) {
  echo  "<script>alert('Delete Successful');</script>";
  header('Location: /admin/products.php');
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}
