<?php
session_start();
include '../php/databaseConnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT id, email, password, is_first_login FROM user WHERE email = '$email' AND role = 'MERCHANT'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['organizer_id'] = $row['id'];
            $_SESSION['email'] = $row['email'];

            // 如果是首次登录，强制更改密码
            if ((int)$row['is_first_login'] === 1){
                header("Location: change_password.php");
                exit();
            } else {
                header("Location: ../organizer/index.php");
                exit();
            }
        } else {
            $error = "Incorrect email or password.";
        }
    } else {
        $error = "Organizer not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Organizer Login</title>
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
            <h2 class="text-center">Organizer Login</h2>
            <?php if (isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
            <form action="../organizer/login.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
