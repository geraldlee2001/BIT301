<?php
require_once '../vendor/autoload.php';
include '../php/databaseConnection.php';

use Ramsey\Uuid\Uuid;

function isUsernameExists($username, $conn)
{
  $username = $conn->real_escape_string($username);
  $sql = "SELECT id FROM user WHERE userName = '$username'";
  $result = $conn->query($sql);
  return $result->num_rows > 0;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $userId = Uuid::uuid4();
  $merchantId = Uuid::uuid4();
  $documentId = Uuid::uuid4();

  $username = $_POST['username'];
  $password = $_POST['password'];
  $merchantName = $_POST['merchantName'];
  $contactNumber = $_POST['contactNumber'];
  $companyDescription = $_POST['companyDescription'];
  $document = $_FILES['documentUpload'];

  if (isUsernameExists($username, $conn)) {
    echo "<script>alert('Username already exists. Please choose a different username.');</script>";
  } else {
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $sqlUser = "INSERT INTO user (id, userName, password, type) VALUES ('$userId', '$username', '$hashedPassword', 'MERCHANT')";

    if ($conn->query($sqlUser) === TRUE) {
      // Handle document upload
      if ($document['error'] === UPLOAD_ERR_OK) {
        $fileName = $document['name'];
        $fileTempPath = $document['tmp_name'];
        $destinationPath = __DIR__ . '/../uploads/' . $fileName;

        if (move_uploaded_file($fileTempPath, $destinationPath)) {
          $imageFilePath = 'uploads/' . $fileName;

          $documentSql = "INSERT INTO document (id, fileName, fileUrl) VALUES ('$documentId', '$fileName', '$imageFilePath')";
          if ($conn->query($documentSql) === TRUE) {
            $merchantSql = "INSERT INTO merchants (id, merchantName, contactNumber, companyDescription, documentId, userId)
                                        VALUES ('$merchantId', '$merchantName', '$contactNumber', '$companyDescription', '$documentId', '$userId')";

            if ($conn->query($merchantSql) === TRUE) {
              echo "<script>alert('Organizer profile created successfully.'); window.location.href='organizer_list.php';</script>";
            } else {
              echo "Error inserting merchant: " . $conn->error;
            }
          } else {
            echo "Error saving document info: " . $conn->error;
          }
        } else {
          echo "Failed to upload document.";
        }
      } else {
        echo "Document upload error.";
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
                <input type="text" class="form-control" id="username" name="username" placeholder="Enter Username" required />
                <label for="username">Username</label>
              </div>
              <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required />
                <label for="password">Password</label>
              </div>
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="merchantName" name="merchantName" placeholder="Enter Organizer Name" required />
                <label for="merchantName">Organizer Name</label>
              </div>
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="contactNumber" name="contactNumber" placeholder="Enter Contact Number" required />
                <label for="contactNumber">Contact Number</label>
              </div>
              <div class="form-floating mb-3" style="height: 180px;">
                <textarea style="height: 150px;" class="form-control" id="companyDescription" name="companyDescription" placeholder="Enter Company Description" rows="5" required></textarea>
                <label for="companyDescription">Company Description</label>
              </div>
              <div class="mb-3">
                <label for="documentUpload" class="form-label">Document</label>
                <input type="file" class="form-control" name="documentUpload" id="documentUpload" required accept="application/msword, text/plain, application/pdf">
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