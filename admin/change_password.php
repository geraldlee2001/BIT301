<?php
session_start();
include '../php/databaseConnection.php';

// 检查用户是否已登录
if (!isset($_SESSION['organizer_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword === $confirmPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $userId = $_SESSION['organizer_id'];

        // 更新密码并将 is_first_login 设置为 0
        $sql = "UPDATE user SET password = '$hashedPassword', is_first_login = 0 WHERE id = '$userId'";
        if ($conn->query($sql) === TRUE) {
            // 清除 session 并重定向到登录页面
            unset($_SESSION['organizer_id']);
            header("Location: login.php");
            exit();
        } else {
            $error = "Error updating password.";
        }
    } else {
        $error = "Passwords do not match.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 400px;
            margin-top: 100px;
        }
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="text-center">Change Password</h2>
            <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
            <form action="change_password.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" class="form-control" name="new_password" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Update Password</button>
            </form>
        </div>
    </div>
</body>
</html>