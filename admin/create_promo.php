<?php
require_once '../php/databaseConnection.php';
include '../php/tokenDecoding.php';
require_once './php/promo_code_management.php';

$merchantId = $decoded->merchantId;

// Get merchant's products for dropdown
$sql = "SELECT id, name FROM product WHERE merchantId = ? ORDER BY name ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $merchantId);
$stmt->execute();
$products = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Create Promo Code</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
  <link href="css/styles.css" rel="stylesheet" />
  <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
  <?php include "./component/header.php"; ?>
  <div id="layoutSidenav">
    <?php include "./component/sidebar.php"; ?>
    <div id="layoutSidenav_content">
      <main>
        <div class="container-fluid px-4">
          <h1 class="mt-4">Create Promo Code</h1>
          <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="promo_codes.php">Promo Codes</a></li>
            <li class="breadcrumb-item active">Create</li>
          </ol>

          <!-- Create Promo Code Form -->
          <div class="card mb-4">
            <div class="card-header">
              <i class="fas fa-tag me-1"></i> Create New Promo Code
            </div>
            <div class="card-body">
              <form id="createPromoForm" action="php/create_promo_code.php" method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label for="productId" class="form-label">Select Event</label>
                    <select class="form-select" id="productId" name="productId" required>
                      <option value="">Choose event...</option>
                      <?php while ($product = $products->fetch_assoc()) { ?>
                        <option value="<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label for="code" class="form-label">Promo Code</label>
                    <input type="text" class="form-control" id="code" name="code" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label for="discountAmount" class="form-label">Discount Amount</label>
                    <input type="number" class="form-control" id="discountAmount" name="discountAmount" required>
                  </div>
                  <div class="col-md-6">
                    <label for="discountType" class="form-label">Discount Type</label>
                    <select class="form-select" id="discountType" name="discountType" required>
                      <option value="percentage">Percentage (%)</option>
                      <option value="fixed">Fixed Amount (RM)</option>
                    </select>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-4">
                    <label for="startDate" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="startDate" name="startDate" required>
                  </div>
                  <div class="col-md-4">
                    <label for="expiryDate" class="form-label">Expiry Date</label>
                    <input type="date" class="form-control" id="expiryDate" name="expiryDate" required>
                  </div>
                  <div class="col-md-4">
                    <label for="usageLimit" class="form-label">Usage Limit</label>
                    <input type="number" class="form-control" id="usageLimit" name="usageLimit" required>
                  </div>
                </div>
                <div class="mt-3">
                  <button type="submit" class="btn btn-primary">Create Promo Code</button>
                  <a href="promo_codes.php" class="btn btn-secondary">Cancel</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </main>
      <?php include "./component/footer.php"; ?>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/scripts.js"></script>

</body>

</html>