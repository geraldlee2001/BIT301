<?php
include '../../php/databaseConnection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Check if the username exists
  $fullName = $_POST['fullName']; // Replace with the username to check
  $email = $_POST['email']; // Replace with the username to check
  $phoneNumber = $_POST['phoneNumber'];
  $birthday = $_POST['birthday'];
  $customerId = $_POST['customerId'];

  $sql = "UPDATE customer
  SET fullName =  '$fullName', email = '$email', phoneNumber = '$phoneNumber', birthday='$birthday'
  WHERE id = \"$customerId\"";
  if ($conn->query($sql) === TRUE) {
    echo  "<script>alert('Update Successful');</script>";
    header('Location: /admin/index.php'); // Redirect to a welcome page
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
}
