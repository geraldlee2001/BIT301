<!-- process_login.php -->
<?php
include './databaseConnection.php';
require_once '../vendor/autoload.php';


use Firebase\JWT\JWT;
use Ramsey\Uuid\Uuid;

// Your secret key
$key = 'bit210';
// Check if the username and password match (replace with your authentication logic)
$username = $_POST['username'];
$password = $_POST['password'];
$id = Uuid::uuid4();
// Query the database to retrieve the user's information
$query = "SELECT * FROM user WHERE userName = \"$username\"";
$result = $conn->query($query);
$user = $result->fetch_assoc();
$code = bin2hex(random_bytes(10));
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
if ($user && password_verify($password, $user['password'])) {
  if ($user['type'] === 'CUSTOMER') {
    $customerQuery = "SELECT * FROM customer WHERE userId = \"$user[id]\"";
    $customerResult = $conn->query($customerQuery);
    $customer = $customerResult->fetch_assoc();
    if (!$customer) {
      header('Location: /profile_create.php'); // Redirect to a profile create page
      return;
    }
    // Check if the user has a cart
    $cartQuery = "SELECT * FROM cart WHERE customerId = \"$customer[id]\" AND status =\"ADDING\"";
    $cartResult = $conn->query($cartQuery);
    $cart = $cartResult->fetch_assoc();
    if ($cart == null) {
      $cartQuery = "INSERT INTO cart (id, customerId, status,code) VALUES ('$id', '$customer[id]', 'ADDING','$code')";
      $newCartResult = $conn->query($cartQuery);
    }
    // Payload data
    $payload = array(
      "customerId" => $customer['id'],
      "cartId" => $cart == null ? $id : $cart['id'],
      "userId" => $user['id'],
      "username" =>  $user["userName"],
      "role" => $user['type'],
    );

    // Generate the JWT
    $token = JWT::encode($payload, $key, 'HS256');
    setcookie("token",  $token, time() + 3600 * 60, "/", "localhost");
    header('Location: ../event.php'); // Redirect to a welcome page

  } else {
    // User is not an admin
    echo "<script>
    alert('Invalid username or password.');
    window.location.href = '../login.html';
  </script>";
    exit;
  }
} else {
  // Invalid login credentials
  echo "<script>
    alert('Invalid username or password.');
    window.location.href = '../login.html';
  </script>";
}
