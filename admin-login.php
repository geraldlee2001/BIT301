<?php
session_start();
require './php/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 查询管理员数据库
    $stmt = $conn->prepare("SELECT password FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // 绑定查询结果
        $stmt->bind_result($db_password);
        $stmt->fetch();

        // 验证密码
        if ($password === $db_password) // 确保数据库中的密码是哈希存储的
        {
            $_SESSION['is_admin'] = true;  // 登录状态
            $_SESSION['admin_email'] = $email; // 存储管理员邮箱
            header("Location: admin-dashboard.php");
            exit();
        } else {
            echo "<script>alert('Invalid password. Please try again.'); window.location.href='admin-login.php';</script>";
        }
    } else {
        echo "<script>alert('No account found with that email..'); window.location.href='admin-login.php';</script>";
    }

    $stmt->close();
}
$conn->close();
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/register-login.css">
    <link rel="icon" type="image/x-icon" href="../img/Logo.webp">
    <title>SuperConcert</title>
</head>

<body>
    <div class="container">
        <h1>SuperConcert</h1>

        <!-- Login Form -->
        <div id="login-section" class="form-section active">
            <h2>Admin Login</h2>
            <form id="login-form" action="admin-login.php" method="POST">
                <input type="hidden" name="login" value="1">
                <div class="form-group">
                    <label for="login-email">Email</label>
                    <input type="email" id="login-email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" name="password" placeholder="Enter your password"
                        required>
                </div>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
    <script src="../javascript/register-login.js"></script>
</body>

</html>