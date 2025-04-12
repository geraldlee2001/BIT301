<!-- process_login.php -->
<?php
require_once '../vendor/autoload.php';

use Firebase\JWT\JWT;

$key = 'bit210';

include '../php/databaseConnection.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Check if the username and password match (replace with your authentication logic)
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Securely hash the user's password and store it in the database
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    // Query the database to retrieve the user's information
    $query = "SELECT * FROM user WHERE userName = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if ($user && password_verify($password, $user['password'])) {
        if ($user['type'] === 'ADMIN') {
            // Payload data
            $payload = array(
                "userId" => $user['id'],
                "username" =>  $user["userName"],
                "role" => $user['type'],
            );
            // Generate the JWT
            $token = JWT::encode($payload, $key, 'HS256');
            setcookie("token",  $token, time() + 3600 * 60, "/", "localhost");
            header('Location: /admin/index.php'); // Redirect to a welcome page
        } else if ($user['type'] === 'MERCHANT') {
            if ($user['isFirstTimeLogin'] === 1) {
                echo "<script>console.log('Redirecting to change_password.php');</script>"; // 调试输出
                $payload = array(
                    "userId" => $user['id'],
                    "username" =>  $user["userName"],
                    "role" => $user['type'],
                );
                // Generate the JWT
                $token = JWT::encode($payload, $key, 'HS256');
                setcookie("token",  $token, time() + 3600 * 60, "/", "localhost");
                header('Location: /admin/change_password.php');
                exit();
            }
            $merchantQuery = "SELECT * FROM merchants WHERE userId = \"$user[id]\"";
            $merchantQuery = $conn->query($merchantQuery);
            $merchant = $merchantQuery->fetch_assoc();
            // Payload data
            $payload = array(
                "merchantId" => $merchant['ID'],
                "userId" => $user['id'],
                "username" =>  $user["userName"],
                "role" => $user['type'],
            );
            // Generate the JWT
            $token = JWT::encode($payload, $key, 'HS256');
            setcookie("token",  $token, time() + 3600 * 60, "/", "localhost");
            header('Location: /admin/products.php'); // Redirect to a welcome page

        }
    } else {
        // Invalid login credentials
        echo "<script>alert('Invalid username or password.');</script>";
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
    <title>Login - EVENT X Admin</title>
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
                                    <form action="login.php" method="POST">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="username" name='username' placeholder="Username" />
                                            <label for="inputEmail">Username</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="password" type="password" name='password' placeholder="Password" />
                                            <label for="inputPassword">Password</label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" id="inputRememberPassword" type="checkbox" value="" />
                                            <label class="form-check-label" for="inputRememberPassword">Remember
                                                Password</label>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <a class="small" href="password.html">Forgot Password?</a>
                                            <input type="submit" value="Log In">
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><a href="signup.php">Need an account? Sign up!</a></div>
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