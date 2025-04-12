<?php
require_once '../php/databaseConnection.php';
include '../php/tokenDecoding.php';
require_once './php/promo_code_management.php';

$merchantId = $decoded->merchantId;
$promoId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$promoId) {
  header('Location: promo_codes.php');
  exit;
}

// Get promo code details
$sql = "SELECT p.*, pr.name as product_name FROM promo_codes p 
JOIN product pr ON p.productId = pr.id 
WHERE p.id = ? AND p.merchantId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $promoId, $merchantId);
$stmt->execute();
$promoCode = $stmt->get_result()->fetch_assoc();

if (!$promoCode) {
  header('Location: promo_codes.php');
  exit;
}

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
  <title>Edit Promo Code</title>
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
          <h1 class="mt-4">Edit Promo Code</h1>
          <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="promo_codes.php">Promo Codes</a></li>
            <li class="breadcrumb-item active">Edit</li>
          </ol>

          <!-- Edit Promo Code Form -->
          <div class="card mb-4">
            <div class="card-header">
              <i class="fas fa-edit me-1"></i> Edit Promo Code
            </div>
            <div class="card-body">
              <form id="editPromoForm">
                <input type="hidden" name="promoId" value="<?php echo $promoCode['id']; ?>">
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label for="productId" class="form-label">Select Event</label>
                    <select class="form-select" id="productId" name="productId" required>
                      <option value="">Choose event...</option>
                      <?php while ($product = $products->fetch_assoc()) { ?>
                        <option value="<?php echo $product['id']; ?>" <?php echo ($product['id'] == $promoCode['productId']) ? 'selected' : ''; ?>>
                          <?php echo htmlspecialchars($product['name']); ?>
                        </option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label for="code" class="form-label">Promo Code</label>
                    <input type="text" class="form-control" id="code" name="code" value="<?php echo htmlspecialchars($promoCode['code']); ?>" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label for="discountAmount" class="form-label">Discount Amount</label>
                    <input type="number" class="form-control" id="discountAmount" name="discountAmount" value="<?php echo $promoCode['discount_amount']; ?>" required>
                  </div>
                  <div class="col-md-6">
                    <label for="discountType" class="form-label">Discount Type</label>
                    <select class="form-select" id="discountType" name="discountType" required>
                      <option value="percentage" <?php echo ($promoCode['discount_type'] == 'percentage') ? 'selected' : ''; ?>>Percentage (%)</option>
                      <option value="fixed" <?php echo ($promoCode['discount_type'] == 'fixed') ? 'selected' : ''; ?>>Fixed Amount (RM)</option>
                    </select>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-4">
                    <label for="startDate" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="startDate" name="startDate" value="<?php echo date('Y-m-d', strtotime($promoCode['start_date'])); ?>" required>
                  </div>
                  <div class="col-md-4">
                    <label for="expiryDate" class="form-label">Expiry Date</label>
                    <input type="date" class="form-control" id="expiryDate" name="expiryDate" value="<?php echo date('Y-m-d', strtotime($promoCode['expiry_date'])); ?>" required>
                  </div>
                  <div class="col-md-4">
                    <label for="usageLimit" class="form-label">Usage Limit</label>
                    <input type="number" class="form-control" id="usageLimit" name="usageLimit" value="<?php echo $promoCode['usage_limit']; ?>" required>
                  </div>
                </div>
                <div class="mt-3">
                  <button type="submit" class="btn btn-primary">Save Changes</button>
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
  <script>
    $(document).ready(function() {
      $('#editPromoForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('merchantId', '<?php echo $merchantId; ?>');

        $.ajax({
          url: 'php/update_promo_code.php',
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          success: function(response) {
            const result = JSON.parse(response);
            if (result.success) {
              alert('Promo code updated successfully!');
              window.location.href = 'promo_codes.php';
            } else {
              alert(result.message || 'Error updating promo code');
            }
          },
          error: function() {
            alert('Error updating promo code');
          }
        });
      });
    });
  </script>
</body>

</html>