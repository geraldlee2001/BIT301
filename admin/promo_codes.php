<?php
require_once '../php/databaseConnection.php';
include '../php/tokenDecoding.php';
require_once './php/promo_code_management.php';

$merchantId = $decoded->merchantId;
$promoCodes = getPromoCodesByMerchant($merchantId);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Promo Codes Management</title>
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
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h1 class="mt-4">Promo Codes</h1>
              <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Promo Codes</li>
              </ol>
            </div>
            <div>
              <a href="create_promo.php" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Create New Promo Code
              </a>
            </div>
          </div>

          <!-- Promo Codes List -->
          <div class="card mb-4">
            <div class="card-header">
              <i class="fas fa-table me-1"></i> Existing Promo Codes
            </div>
            <div class="card-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Event</th>
                    <th>Code</th>
                    <th>Discount</th>
                    <th>Valid Period</th>
                    <th>Usage</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($promoCodes as $promo) {
                    $isExpired = strtotime($promo['expiry_date']) < time();
                    $isFullyUsed = $promo['current_usage'] >= $promo['usage_limit'];
                    $status = $isExpired ? 'Expired' : ($isFullyUsed ? 'Fully Used' : 'Active');
                    $statusClass = $status === 'Active' ? 'text-success' : 'text-danger';
                  ?>
                    <tr>
                      <td><?php echo htmlspecialchars($promo['product_name']); ?></td>
                      <td><?php echo htmlspecialchars($promo['code']); ?></td>
                      <td>
                        <?php
                        echo $promo['discount_type'] === 'percentage'
                          ? $promo['discount_amount'] . '%'
                          : 'RM' . number_format($promo['discount_amount'], 2);
                        ?>
                      </td>
                      <td>
                        <?php
                        echo date('Y-m-d', strtotime($promo['start_date'])) . ' to ' .
                          date('Y-m-d', strtotime($promo['expiry_date']));
                        ?>
                      </td>
                      <td><?php echo $promo['current_usage'] . '/' . $promo['usage_limit']; ?></td>
                      <td class="<?php echo $statusClass; ?>"><?php echo $status; ?></td>
                      <td>
                        <a href="edit_promo.php?id=<?php echo $promo['id']; ?>" class="btn btn-sm btn-primary">
                          <i class="fas fa-edit"></i> Edit
                        </a>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
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