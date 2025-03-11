<?php
session_start();
require './php/config.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (isset($_POST['login_organiser'])) {
        // 查询 Organiser 的信息
        $stmt = $conn->prepare("SELECT id, password, is_first_login FROM Organisers WHERE email = ?");
    } elseif (isset($_POST['login_attendee'])) {
        // 查询 Attendee 的信息
        $stmt = $conn->prepare("SELECT attendee_id, password FROM Attendee WHERE email = ?");
    } else {
        exit("<p style='color:red;'>Invalid login attempt.</p>");
    }

    // 绑定参数并执行查询
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        if (isset($_POST['login_organiser'])) {
            $stmt->bind_result($user_id, $db_password, $is_first_login);
        } else {
            $stmt->bind_result($user_id, $db_password);
        }
        $stmt->fetch();

        // **密码验证**
        if ($password === $db_password) {
            if (isset($_POST['login_organiser'])) {
                $_SESSION['organiser_logged_in'] = true;
                $_SESSION['organiser_id'] = $user_id;
                $_SESSION['organiser_email'] = $email;
                $_SESSION['is_first_login'] = $is_first_login; // ✅ 添加此项

                if ($is_first_login == 1) {
                    session_write_close();
                    header("Location: dashboard.php"); // ✅ 让 Dashboard.php 处理弹窗
                    exit();
                }

                session_write_close();
                header("Location: dashboard.php");
                exit();
            } elseif (isset($_POST['login_attendee'])) {
                // Check if the user has a cart
                $cartQuery = "SELECT * FROM cart WHERE customerId = \"$customer[id]\" AND status =\"ADDING\"";
                $cartResult = $conn->query($cartQuery);
                $cart = $cartResult->fetch_assoc();
                if ($cart == null) {
                    $cartQuery = "INSERT INTO cart (id, customerId, status,code) VALUES ('$id', '$customer[id]', 'ADDING','$code')";
                    $newCartResult = $conn->query($cartQuery);
                }
                $_SESSION['attendee_logged_in'] = true;
                $_SESSION['attendee_id'] = $user_id;
                $_SESSION['attendee_email'] = $email;
                $_SESSION['cart_id'] = $cart == null ? $id : $cart['id'];
                session_write_close();
                header("Location: attendee-dashboard.php");
                exit();
            }
        } else {
            echo "<script>alert('Invalid password. Please try again.'); window.location.href='login.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('No account found with that email.'); window.location.href='login.php';</script>";
        exit();
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
    <link rel="stylesheet" href="./css/organiser_Login.css">
    <link rel="icon" type="image/x-icon" href="../img/Logo.webp">
    <title>SuperConcert - Login</title>

<body>

    <div class="container">
        <h1>SuperConcert</h1>

        <!-- Tab Buttons -->
        <div class="tab-buttons">
            <button class="tab-link active" onclick="openTab('organiser')">Organiser Login</button>
            <button class="tab-link" onclick="openTab('attendee')">Attendee Login</button>
        </div>

        <!-- Organiser Login Form -->
        <div id="organiser" class="form-section active">
            <h2>Organiser Login</h2>
            <form action="login.php" method="POST">
                <input type="hidden" name="login_organiser" value="1">
                <div class="form-group">
                    <label for="organiser-email">Email</label>
                    <input type="email" id="organiser-email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="organiser-password">Password</label>
                    <input type="password" id="organiser-password" name="password" placeholder="Enter your password"
                        required>
                </div>
                <button type="submit">Login</button>
            </form>
        </div>

        <!-- Attendee Login Form -->
        <div id="attendee" class="form-section">
            <h2>Attendee Login</h2>
            <form action="login.php" method="POST">
                <input type="hidden" name="login_attendee" value="1">
                <div class="form-group">
                    <label for="attendee-email">Email</label>
                    <input type="email" id="attendee-email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label for="attendee-password">Password</label>
                    <input type="password" id="attendee-password" name="password" placeholder="Enter your password"
                        required>
                </div>
                <div class="form-group">
                    <a href="register.php">Don't have account? Click me register</a>
                </div>
                <button type="submit">Login</button>
            </form>
        </div>

    </div>

    <script>
        function openTab(tabName) {
            document.querySelectorAll(".form-section").forEach(section => {
                section.classList.remove("active");
            });

            document.getElementById(tabName).classList.add("active");

            document.querySelectorAll(".tab-link").forEach(button => {
                button.classList.remove("active");
            });

            document.querySelector(`button[onclick="openTab('${tabName}')"]`).classList.add("active");
        }
    </script>

</body>

</html>