<?php
require_once '../vendor/autoload.php';
include '../php/databaseConnection.php';
use Ramsey\Uuid\Uuid;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function isUsernameExists($username, $conn) {
    $username = $conn->real_escape_string($username);
    $sql = "SELECT id FROM user WHERE userName = '$username'";
    $result = $conn->query($sql);
    return $result->num_rows > 0;
}

// 生成随机密码
function generateRandomPassword($length = 10) {
    return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()"), 0, $length);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = Uuid::uuid4();
    $merchantId = Uuid::uuid4();

    $username = $_POST['username'];
    $email = $_POST['email'];
    $merchantName = $_POST['merchantName'];
    $contactNumber = $_POST['contactNumber'];

    // 生成默认密码
    $defaultPassword = generateRandomPassword();
    $hashedPassword = password_hash($defaultPassword, PASSWORD_BCRYPT);

    if (isUsernameExists($username, $conn)) {
        echo "<script>alert('Username already exists. Please choose a different username.');</script>";
    } else {
        $sqlUser = "INSERT INTO user (id, userName, email, password, role) 
                    VALUES ('$userId', '$username', '$email', '$hashedPassword', 'MERCHANT')";

        if ($conn->query($sqlUser) === TRUE) {
            $merchantSql = "INSERT INTO merchants (id, merchantName, contactNumber, userId) 
                            VALUES ('$merchantId', '$merchantName', '$contactNumber', '$userId')";

            if ($conn->query($merchantSql) === TRUE) {
                // 发送邮件
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'yapfongkiat53@gmail.com';
                    $mail->Password = 'momfaxlauusnbnvl';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('yapfongkiat53@gmail.com', 'SuperConcert');
                    $mail->addAddress($email, $merchantName);
                    $mail->isHTML(true);
                    $mail->Subject = 'Welcome to SuperConcert!';
                    $mail->Body = "
                        <html>
                        <body>
                        <h1>Welcome to SuperConcert!</h1>
                        <p>Dear $merchantName,</p>
                        <p>Your account has been created successfully. Below are your login details:</p>
                        <p>Email: $email</p>
                        <p>Password: <strong>$defaultPassword</strong></p>
                        <p>Please log in and change your password for security purposes.</p>
                        <p><a href='http://localhost/SuperConcert/php/organiser_Login.php'>Login Now</a></p>
                        </body>
                        </html>
                    ";

                    $mail->send();
                    echo "<script> 
                            alert('Registration Successful! Your account has been created successfully. Please check your email for login details.');
                            window.location.href='Register_Organizer.php';
                        </script>";
                } catch (Exception $e) {
                    echo "<script> 
                            alert('Error in sending email: {$mail->ErrorInfo}');
                            window.location.href='Register_Organizer.php';
                        </script>";
                }
            } else {
                echo "Error inserting merchant: " . $conn->error;
            }
        } else {
            echo "Error creating user: " . $conn->error;
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Create Organizer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }

    .container {
      max-width: 600px;
    }

    .form-container {
      background: white;
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      margin-top: 50px;
    }
  </style>
</head>

<body>
  <?php include "./component/header.php" ?>
  <div id="layoutSidenav">
    <?php include "./component/sidebar.php" ?>
    <div id="layoutSidenav_content">
      <main>
        <div class="container">
          <div class="form-container">
            <h2 class="mb-4 text-center">Create Organizer Account</h2>
            <form action="organizer_create.php" method="POST" enctype="multipart/form-data">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter Username"
                  required />
                <label for="username">Username</label>
              </div>
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="email" name="email" placeholder="Enter Email" required />
                <label for="password">Email</label>
              </div>
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="merchantName" name="merchantName"
                  placeholder="Enter Organizer Name" required />
                <label for="merchantName">Organizer Name</label>
              </div>
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="contactNumber" name="contactNumber"
                  placeholder="Enter Contact Number" required />
                <label for="contactNumber">Contact Number</label>
              </div>
              <div class="align-items-end mt-4 mb-0">
                <input type="submit" value="Create Organizer" class="btn btn-primary w-100">
              </div>
            </form>
          </div>
        </div>
      </main>
      <?php include "./component/footer.php" ?>
    </div>
  </div>

</body>

</html>