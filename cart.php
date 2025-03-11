<?php
include "./php/tokenDecoding.php";
include './php/databaseConnection.php';

// Safety check
if (!isset($decoded->cartId)) {
  die("Cart ID is missing or invalid.");
}

$cartId = $decoded->cartId;

$query = "SELECT
  cci.cart_item_id,
  cci.cart_id,
  ci.createdAt AS cart_item_createdAt,
  ci.updatedAt AS cart_item_updatedAt,
  ci.seat AS cart_item_seat,
  ci.productId,
  p.ID AS product_id,
  p.createdAt AS product_createdAt,
  p.updatedAt AS product_updatedAt,
  p.name AS product_name,
  p.productCode,
  p.description AS product_description,
  p.price AS product_price,
  p.amount AS product_amount,
  p.imageUrl AS product_imageUrl,
  p.merchantID
FROM cartcartitem cci
JOIN cartitem ci ON cci.cart_item_id = ci.id
JOIN product p ON ci.productId = p.ID
WHERE cci.cart_id = \"$cartId\"";

$cartQuery = "SELECT * FROM cart WHERE id = \"$cartId\"";
$data = $conn->query($query);
$cartResult = $conn->query($cartQuery);
$cartData = $cartResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shopping Cart</title>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="css/styles.css">
  <script src="js/cartList.js"></script>
</head>

<body>
  <?php include "./component/header.php" ?>

  <div class="container-fluid h-100" style="background-color: #eee;">
    <div class="container py-5">
      <div class="row d-flex justify-content-center align-items-center">
        <div class="col-10 mt-6">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-normal mb-0 text-black">Shopping Cart</h3>
          </div>

          <?php
          $groupedItems = [];
          $totalPrice = 0;

          if (!$data) {
            echo "<p>Query failed: " . $conn->error . "</p>";
          } elseif ($data->num_rows === 0) {
            echo "<p>No items in your cart.</p>";
          } else {
            while ($item = $data->fetch_assoc()) {
              $productId = $item['product_id'];

              if (!isset($groupedItems[$productId])) {
                $groupedItems[$productId] = [
                  'product_name' => $item['product_name'],
                  'product_price' => $item['product_price'],
                  'product_imageUrl' => $item['product_imageUrl'],
                  'seats' => [],
                  'quantity' => 0
                ];
              }

              $groupedItems[$productId]['quantity'] += 1;
              $groupedItems[$productId]['seats'][] = $item['cart_item_seat'];
              $totalPrice += $item['product_price'];
            }

            echo "<p>" . $data->num_rows . " item(s) in your cart.</p>";

            // Render grouped items
            foreach ($groupedItems as $productId => $item) {
              $uniqueSeats = array_unique($item['seats']);
              echo '<form method="post" action="cart.php" class="cart-item-form">
                      <input type="hidden" name="productId" value="' . $productId . '">
                      <div class="card rounded-3 mb-4">
                        <div class="card-body p-4">
                          <div class="row d-flex justify-content-between align-items-center">
                            <div class="col-md-2 col-lg-2 col-xl-2">
                              <img src="' . $item['product_imageUrl'] . '" class="img-fluid rounded-3" alt="' . $item['product_name'] . '">
                            </div>
                            <div class="col-md-3 col-lg-3 col-xl-3">
                              <p class="lead fw-normal mb-2">' . $item['product_name'] . '</p>
                              <p class="text-muted mb-0"><strong>Seats:</strong> ' . implode(', ', $uniqueSeats) . '</p>
                              <p class="text-muted mb-0"><strong>Quantity:</strong> ' . $item['quantity'] . '</p>
                            </div>
                            <div class="col-md-3 col-lg-3 col-xl-2 d-flex">
                              <!-- Quantity controls (optional) -->
                            </div>
                            <div class="col-md-3 col-lg-2 col-xl-2 offset-lg-1">
                              <h5 class="mb-0">RM ' . number_format($item['product_price'] * $item['quantity'], 2) . '</h5>
                            </div>
                          </div>
                        </div>
                      </div>
                    </form>';
            }
          }
          ?>

          <?php if ($totalPrice > 0): ?>
            <div class="card mb-4">
              <div class="card-body p-4 d-flex flex-row">
                <div class="form-outline flex-fill align-self-end text-right">
                  <label class="form-label"><strong>Total Price: RM <?php echo number_format($totalPrice, 2); ?></strong></label>
                </div>
              </div>
            </div>

            <div class="card">
              <div class="card-body">
                <form action="./php/checkout.php" method="POST">
                  <input type="hidden" name="totalPrice" value="<?php echo $totalPrice ?>">
                  <input type="hidden" name="cartCode" value="<?php echo $cartData['code'] ?>">
                  <button type="submit" class="btn btn-warning btn-block btn-lg">Proceed to Pay</button>
                </form>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <style>
    #mainNav {
      padding-top: 1.5rem;
      padding-bottom: 1.5rem;
      border: none;
      background-color: #212529;
      transition: padding-top 0.3s ease-in-out, padding-bottom 0.3s ease-in-out;
      color: white;
    }
  </style>
</body>

</html>