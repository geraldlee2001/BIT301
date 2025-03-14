<?php
session_start(); // 启用会话
require_once '../vendor/autoload.php';

use Firebase\JWT\JWT;

$key = 'bit210';

include '../php/databaseConnection.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 获取 email 和 password
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 查询数据库
    $query = "SELECT * FROM user WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email); // 绑定 $email 到查询
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc(); // 获取用户数据

    // 检查用户是否存在且密码正确
    if ($user && password_verify($password, $user['password'])) {
        if ($user['type'] === 'ADMIN') {
            // Payload 数据
            $payload = array(
                "userId" => $user['id'],
                "email" => $user["email"], 
                "role" => $user['type'],
            );
            $token = JWT::encode($payload, $key, 'HS256');
            setcookie("token", $token, time() + 3600 * 60, "/", "localhost");
            header('Location: /admin/index.php'); 
            exit();
        } else if ($user['type'] === 'MERCHANT') {
            // 检查是否是第一次登录
            if ($user['is_first_login'] == 1) {
                echo "<script>console.log('Redirecting to change_password.php');</script>"; // 调试输出
                $_SESSION['organizer_id'] = $user['id']; 
                header('Location: /admin/change_password.php');
                exit();
            }

            $merchantQuery = "SELECT * FROM merchants WHERE userId = \"$user[id]\"";
            $merchantQuery = $conn->query($merchantQuery);
            $merchant = $merchantQuery->fetch_assoc();
            if (!$merchant) {
                $payload = array(
                    "merchantId" => null,
                    "userId" => $user['id'],
                    "email" => $user["email"],
                    "role" => $user['type'],
                );
                $token = JWT::encode($payload, $key, 'HS256');
                setcookie("token", $token, time() + 3600 * 60, "/", "localhost");
                header('Location: /admin/index.php'); 
                exit();
            }
            $payload = array(
                "merchantId" => $merchant['ID'],
                "userId" => $user['id'],
                "email" => $user["email"], 
                "role" => $user['type'],
            );
            $token = JWT::encode($payload, $key, 'HS256');
            setcookie("token", $token, time() + 3600 * 60, "/", "localhost");
            header('Location: /admin/index.php'); 
            exit();
        }
    } else {
        echo "<script>alert('Invalid email or password.'); window.location.href='/admin/login.php';</script>";
        exit();
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
                                    <form action="login.php" method="POST">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="email" name='email' placeholder="email" />
                                            <label for="inputEmail">email</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="password" type="password" name='password'
                                                placeholder="Password" />
                                            <label for="inputPassword">Password</label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" id="inputRememberPassword" type="checkbox"
                                                value="" />
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>

</html>