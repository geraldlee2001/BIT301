<?php
include '../../php/databaseConnection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $merchantName = $_POST['merchantName'];
  $contactNumber = $_POST['contactNumber'];
  $companyDescription = $_POST['companyDescription'];
  $merchantId = $_POST['merchantId'];

  $sql = $sql = "UPDATE merchants
    SET merchantName =  '$merchantName', contactNumber = '$contactNumber', companyDescription = '$companyDescription'
    WHERE id = \"$merchantId\"";
  if ($conn->query($sql) === TRUE) {
    echo  "<script>alert('Update Successful');</script>";
    header('Location: /admin/organizers.php'); // Redirect to a welcome page
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
}
