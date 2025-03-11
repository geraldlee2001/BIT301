<?php

use Ramsey\Uuid\Uuid;
use Firebase\JWT\JWT;

require_once './vendor/autoload.php';
include './php/databaseConnection.php';
$id = Uuid::uuid4();

// Your secret key
$key = 'bit210';

// Function to check if a username exists
function isUsernameExists($username, $conn)
{
  $username = $conn->real_escape_string($username);
  $sql = "SELECT id FROM user WHERE username = '$username'";
  $result = $conn->query($sql);

  return $result->num_rows > 0;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Check if the username exists
  $checkUsername = $_POST['username']; // Replace with the username to check
  $usernameExists = isUsernameExists($checkUsername, $conn);

  if ($usernameExists) {
    echo "<script>alert('Username already exists. Please choose a different username.');</script>";
  } else {
    // Username is available, proceed with registration
    $newUsername = $checkUsername; // Replace with the user's chosen username
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert the user into the database
    $sql = "INSERT INTO user (id, userName,password) VALUES ('$id','$newUsername','$hashedPassword')";

    if ($conn->query($sql) === TRUE) {
      echo  "<script>alert('Registration Successful');</script>";
      $sql = "SELECT * FROM user WHERE id = '$id'";
      $result = $conn->query($sql);
      $user = $result->fetch_assoc();
      // Payload data
      $payload = array(
        "userId" => $user['id'],
        "username" =>  $user["userName"],
        "role" => $user['type'],
        'customerId' => null,
      );

      // Generate the JWT
      $token = JWT::encode($payload, $key, 'HS256');
      setcookie("token",  $token, time() + 3600 * 60, "/", "localhost");
      header('Location: ../profile_create.php'); // Redirect to a profile create page
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  }
}



// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <title>TripAdvisor - Your best trip planner</title>
  <!-- Favicon-->
  <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
  <!-- Font Awesome icons (free version)-->
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
  <!-- Google fonts-->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
  <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700" rel="stylesheet" type="text/css" />
  <!-- Core theme CSS (includes Bootstrap)-->
  <link href="css/styles.css" rel="stylesheet" />
</head>

<body id="page-top">
  <!-- Navigation-->
  <nav class="navbar bg-dark" id="mainNav">
    <div class="container">
      <a class="navbar-brand" href="./"> <img src="images/Logo Creator (Community) (1).png" alt="..." width="50" style="margin-right: 10px;" />TripAdvisor</a>
    </div>
  </nav>

  <div class="container mt-6">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header text-center">
            <h2>Sign Up</h2>
          </div>
          <div class="card-body">
            <form method="post" action="signup.php">
              <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required />
              </div>
              <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required />
              </div>
              <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm password" required />
              </div>
              <button type="submit" class="btn btn-primary btn-block">
                Sign Up
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Bootstrap core JS-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Core theme JS-->
  <script src="js/scripts.js"></script>

  <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
  <!-- * *                               SB Forms JS                               * *-->
  <!-- * * Activate your form at https://startbootstrap.com/solution/contact-forms * *-->
  <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
</body>

</html>