<?php
include "./php/databaseConnection.php";
require_once './vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Your secret key (must match the one used for encoding)
$key = 'bit210';

// The JWT you want to decode\
if ($_COOKIE['token']) {
  $jwt = $_COOKIE['token']; // Replace with the actual JWT you want to decode
  $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
  $query = "SELECT * FROM customer where id = \"$decoded->customerId\"";
  $result = $conn->query($query);
  $data = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Check if the username exists
  $fullName = $_POST['fullName']; // Replace with the username to check
  $email = $_POST['email']; // Replace with the username to check
  $phoneNumber = $_POST['phoneNumber'];
  $birthday = $_POST['birthday'];

  $sql = "UPDATE customer
  SET fullName =  '$fullName', email = '$email', phoneNumber = '$phoneNumber', birthday='$birthday'
  WHERE id = \"$decoded->customerId\"";
  if ($conn->query($sql) === TRUE) {
    echo  "<script>alert('Registration Successful');</script>";
    header('Location: ../'); // Redirect to a welcome page
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
}
?>

<head>
  <title>Profile</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.6/jquery.inputmask.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<link href="css/styles.css" rel="stylesheet" />

<hr>
<?php include "./component/header.php" ?>
<div class="container bootstrap snippet mt-6">
  <div class="row">
    <div class="col-sm-10 mt-2">
      <?php echo '<h1 >' . strtoupper($decoded->username) . '</h1>' ?>
    </div>
  </div>
  <div class="row mt-3">
    <div class="col-sm-12">
      <div class="tab-content">
        <div class="tab-pane active" id="home">
          <hr>
          <form class="form-horizontal" action="profile.php" method="post" id="registrationForm">
            <div class="form-group">
              <label for="fullName">Full Name</label>
              <input type="text" class="form-control" name="fullName" id="fullName" placeholder="Full name" value="<?php echo $data['fullName'] ?>" title="Enter your full name">
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" class="form-control" name="email" id="email" placeholder="Enter email" value="<?php echo $data['email'] ?>" title="Enter your email">
            </div>

            <div class="form-group">
              <label for="phoneNumber">Phone</label>
              <input type="text" class="form-control" name="phoneNumber" id="phoneNumber" placeholder="Enter phone" value="<?php echo $data['phoneNumber'] ?>" title="Enter your phone number if any.">
            </div>

            <div class="form-group">
              <label for="birthday">Birthday</label>
              <input type="date" class="form-control" name="birthday" id="birthday" placeholder="DD/MM/YYYY" title="Select your birthday" value="<?php echo $data['birthday'] ?>">
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>

          </form>

          <hr>

        </div><!--/tab-pane-->
      </div><!--/tab-pane-->
    </div><!--/tab-content-->

  </div>
</div><!--/row-->

<script>
  document.querySelector("#submit").addEventListener("click", function() {
    // Display the success message.
    document.querySelector("#success-message").style.display = "block";
    // Return to the same page after 3 seconds.
    setTimeout(function() {
      window.location.reload();
    }, 3000);
  });


  // const submitButton = document.querySelector('button[type="submit"]');
  // submitButton.addEventListener('click', function(event) {
  // event.preventDefault();
  // });


  $(document).ready(function() {


    var readURL = function(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
          $('.avatar').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
      }
    }


    $(".file-upload").on('change', function() {
      readURL(this);
    });
  });

  $(document).ready(function() {
    $('#myDate').inputmask('99/99/9999', {
      placeholder: 'dd/mm/yyyy'
    });
  });
</script>

<style>
  #success-message {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 100;
    text-align: center;
    padding-top: 20%;
  }

  #mainNav {
    padding-top: 1.5rem;
    padding-bottom: 1.5rem;
    border: none;
    background-color: #212529;
    transition: padding-top 0.3s ease-in-out, padding-bottom 0.3s ease-in-out;
    color: white;
  }
</style>