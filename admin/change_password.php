<?php
include '../php/databaseConnection.php';
include '../php/tokenDecoding.php';

// 检查用户是否已登录
if (!isset($decoded->userId)) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword === $confirmPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $userId = $decoded->userId;

        // 更新密码并将 is_first_login 设置为 0
        $sql = "UPDATE user SET password = '$hashedPassword', isFirstTimeLogin = 0 WHERE id = '$userId'";
        if ($conn->query($sql) === TRUE) {
            // delete cookies
            setcookie("token", "", time() - 3600, "/", "localhost");
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
                                    <h3 class="text-center font-weight-light my-4">Change Password</h3>
                                </div>
                                <div class="card-body">
                                    <form action="change_password.php" method="POST">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" type="password" name='new_password' placeholder="New Password" />
                                            <label for="new_password">New Password</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="password" type="password" name='confirm_password' placeholder="Confirm Password" />
                                            <label for="confirm_password">Confirm Password</label>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <input type="submit" value="Update Password">
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>

</html>