<?php
include "./php/tokenDecoding.php";
include "./php/databaseConnection.php";

// Fetch completed orders
$sql = "SELECT
    cart.id AS cartId,
    cart.code AS cartCode,
    cartitem.seat AS seat,
    product.name AS productName,
    product.id AS productId,
    product.price AS productPrice,
    product.imageUrl AS productImageUrl,
    cartitem.createdAt AS purchasedAt
FROM cart
JOIN cartcartitem ON cart.id = cartcartitem.cart_id
JOIN cartitem ON cartcartitem.cart_item_id = cartitem.id
JOIN product ON cartitem.productId = product.id
WHERE cart.status = 'COMPLETED' AND cart.customerId = '$decoded->customerId'
ORDER BY purchasedAt DESC";

$result = $conn->query($sql);

// Organize by cartCode > product
$orders = [];
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $cartCode = $row['cartCode'];
    $productId = $row['productId'];

    if (!isset($orders[$cartCode])) {
      $orders[$cartCode] = [
        'cartId' => $row['cartId'],
        'purchasedAt' => $row['purchasedAt'],
        'products' => [],
      ];
    }

    if (!isset($orders[$cartCode]['products'][$productId])) {
      $orders[$cartCode]['products'][$productId] = [
        'productName' => $row['productName'],
        'productPrice' => $row['productPrice'],
        'productImage' => $row['productImageUrl'],
        'seats' => [],
      ];
    }

    $orders[$cartCode]['products'][$productId]['seats'][] = $row['seat'];
  }
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Purchased History</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link href="css/styles.css" rel="stylesheet" />
</head>

<body>
  <?php include "./component/header.php"; ?>

  <div class="container-fluid h-100" style="background-color: #eee;">
    <div class="container py-5">
      <div class="row d-flex justify-content-center align-items-center">
        <div class="col-10 mt-6">
          <h3 class="fw-normal mb-4 text-black">Purchased History</h3>

          <?php foreach ($orders as $cartCode => $order): ?>
            <div class="card rounded-3 mb-4">
              <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <div>
                    <h5 class="mb-0"><strong>Order Code:</strong> #<?= $cartCode ?></h5>
                    <small class="text-muted">Purchased at: <?= $order['purchasedAt'] ?></small>
                  </div>
                  <a class="btn btn-primary" href="/php/generateReceipt.php?id=<?= $order['cartId'] ?>">Generate receipt</a>
                </div>

                <?php $orderTotal = 0; ?>
                <?php foreach ($order['products'] as $product): ?>
                  <?php
                  $seats = implode(', ', array_unique($product['seats']));
                  $count = count($product['seats']);
                  $total = $count * $product['productPrice'];
                  $orderTotal += $total;
                  ?>
                  <div class="row d-flex justify-content-between align-items-center mb-3">
                    <div class="col-md-2 col-lg-2 col-xl-2">
                      <img src="<?= $product['productImage'] ?>" class="img-fluid rounded-3" alt="<?= $product['productName'] ?>">
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-6">
                      <p class="fw-bold mb-1"><?= $product['productName'] ?></p>
                      <p class="text-muted mb-1"><strong>Seats:</strong> <?= $seats ?></p>
                    </div>
                    <div class="col-md-4 col-lg-4 col-xl-4 text-end">
                      <p class="mb-0"><strong>Total:</strong> RM <?= number_format($total, 2) ?></p>
                    </div>
                  </div>
                <?php endforeach; ?>

                <div class="text-end mt-3">
                  <strong>Order Total: RM <?= number_format($orderTotal, 2) ?></strong>
                </div>
              </div>
            </div>
          <?php endforeach; ?>

          <?php if (empty($orders)): ?>
            <p>No purchase history found.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <?php include "./component/footer.php"; ?>
</body>

</html>