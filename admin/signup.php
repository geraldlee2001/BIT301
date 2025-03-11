<!-- process_login.php -->
<?php
require_once '../vendor/autoload.php';
include '../php/databaseConnection.php';

use Firebase\JWT\JWT;
use Ramsey\Uuid\Uuid;

$key = 'bit210';

$id = Uuid::uuid4();

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
        $sql = "INSERT INTO user (id, userName,password,type) VALUES ('$id','$newUsername','$hashedPassword','MERCHANT')";

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
            );

            // Generate the JWT
            $token = JWT::encode($payload, $key, 'HS256');
            setcookie("token",  $token, time() + 3600 * 60, "/", "localhost");
            header('Location: /admin/organizer_create.php'); // Redirect to a profile create page
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Login - SB Admin</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Login</h3>
                                </div>
                                <div class="card-body">
                                    <form action="signup.php" method="POST">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required />
                                            <label for="username">Username</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required />
                                            <label for="password">Password</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm password" required />
                                            <label for="confirmPassword">Confirm Password</label>
                                        </div>
                                        <div class="align-items-end mt-4 mb-0">
                                            <input type="submit" value="Sign Up">
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small">
                                        <a href="login.php">Have an account? Go to login</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Your Website 2023</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>

</html>