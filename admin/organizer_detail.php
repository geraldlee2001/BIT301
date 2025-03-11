<?php
include "../php/databaseConnection.php";
require_once '../vendor/autoload.php';
include "../php/tokenDecoding.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Your secret key (must match the one used for encoding)
$key = 'bit210';
$jwt = $_COOKIE['token']; // Replace with the actual JWT you want to decode
$decoded = JWT::decode($jwt, new Key($key, 'HS256'));
$query = "SELECT * FROM merchants where id = \"$_GET[id]\"";
$result = $conn->query($query);
$data = $result->fetch_assoc();

?>

<head>
  <title>Profile</title>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
  <link href="css/styles.css" rel="stylesheet" />
  <script src="js/productDetail.js"></script>
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<link href="css/styles.css" rel="stylesheet" />

<body class="sb-nav-fixed">
  <?php include "./component/header.php" ?>
  <div id="layoutSidenav">
    <?php include "./component/sidebar.php" ?>
    <div id="layoutSidenav_content">
      <main>
        <div class="container bootstrap snippet mt-3">
          <div class="row">
            <div class="col-sm-10">
              <?php echo '<h1 >' . strtoupper($data['merchantName']) . '</h1>' ?>
            </div>
          </div>
          <div class="row mt-3">

            <div class="col-sm-12">
              <div class="tab-content">
                <div class="tab-pane active" id="home">
                  <hr>
                  <form class="form-horizontal" action="./php/updateMerchant.php" method="post" id="registrationForm">
                    <input type="hidden" name="merchantId" value="<?php echo $_GET['id'] ?>">
                    <div class="form-group">
                      <label for="merchantName">Organizer Name</label>
                      <input type="text" class="form-control" name="merchantName" id="merchantName" placeholder="Organizer Name" value="<?php echo $data['merchantName'] ?>" title="Enter your full name">
                    </div>

                    <div class="form-group">
                      <label for="contactNumber">Contact Number</label>
                      <input type="contactNumber" class="form-control" name="contactNumber" id="contactNumber" placeholder="Enter Contact Number" value="<?php echo $data['contactNumber'] ?>" title="Enter your email">
                    </div>

                    <div class="form-group">
                      <label for="companyDescription">Company Description</label>
                      <textarea class="form-control" id="companyDescription" name="companyDescription" rows="5"><?php echo $data['companyDescription'] ?></textarea>
                    </div>

                    <div class="form-group mt-3">
                      <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                  </form>

                  <hr>

                </div><!--/tab-pane-->
              </div><!--/tab-pane-->
            </div><!--/tab-content-->

          </div><!--/col-9-->
        </div><!--/row-->
      </main>
      <?php include "./component/footer.php" ?>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="js/scripts.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
  <script src="assets/demo/chart-area-demo.js"></script>
  <script src="assets/demo/chart-bar-demo.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
  <script src="js/datatables-simple-demo.js"></script>
</body>