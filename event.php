<?php
include "./php/databaseConnection.php";
// Retrieve data from customer table
$query = "SELECT
  p.id AS product_id,
  p.name AS product_name,
  p.date AS product_date,
  p.time AS product_time,
  p.imageUrl AS product_imageUrl,
  (SELECT COUNT(*) FROM seats s WHERE s.eventId = p.id) as total_seats,
  (SELECT COUNT(*) FROM seats s WHERE s.eventId = p.id AND s.isBooked = 1) as booked_seats,
  MIN(tt.price) as min_price,
  MAX(tt.price) as max_price
FROM product p
LEFT JOIN ticket_types tt ON tt.eventId = p.id
WHERE DATE(p.date) >= CURDATE()
GROUP BY p.id, p.name, p.date, p.time, p.imageUrl
ORDER BY p.date ASC;";
$data = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Shop Homepage - Start Bootstrap Template</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

    <!-- Bootstrap icons-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet" />
</head>

<body>
    <?php include "./component/header.php"; ?>
    <!-- Header-->
    <header class="bg-dark py-5">
        <div class="container px-4 px-lg-5 my-5">
            <div class="text-center text-white">
                <h1 class="display-4 fw-bolder">Shop in style</h1>
                <p class="lead fw-normal text-white-50 mb-0">With this shop hompeage template</p>
            </div>
        </div>
    </header>
    <!-- Section-->
    <section class="py-5">
        <div class="container px-4 px-lg-5 mt-5">
            <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                <?php
                while ($item = $data->fetch_assoc()) {
                    echo '<div class="col mb-5">
                        <a href="/product_detail.php?id=' . $item['product_id'] . '">
                            <div class="card h-100">
                                <!-- Product image-->
                                <img class="card-img-top" src="' . $item['product_imageUrl'] . '" alt="productImage" />
                                <!-- Product details-->
                                <div class="card-body p-4">
                                    <div class="text-center">
                                        <!-- Product name-->
                                        <h5 class="fw-bolder">' . $item['product_name'] . '</h5>
                                        <div class="d-flex justify-content-center small text-warning mb-2"></div>';
                    if ($item['min_price'] === null) {
                        echo '<p class="mb-2">Price not set</p>';
                    } else if ($item['min_price'] === $item['max_price']) {
                        echo '<p class="mb-2">RM ' . number_format($item['min_price'], 2) . '</p>';
                    } else {
                        echo '<p class="mb-2">RM ' . number_format($item['min_price'], 2) . ' - RM ' . number_format($item['max_price'], 2) . '</p>';
                    }
                    $remainingSeats = $item['total_seats'] - $item['booked_seats'];
                    echo '<p class="mb-2">Available Seats: ' . $remainingSeats . ' / ' . $item['total_seats'] . '</p>';
                    echo '<p class="mb-2">Date: ' . $item['product_date'] . '</p>';
                    echo '<p class="mb-2">Time: ' . $item['product_time'] . '</p>';
                    echo '          </div>
                                </div>
                            </div>
                        </a>
                    </div>';
                }
                ?>
            </div>
        </div>
    </section>
    <!-- Footer-->
    <?php include "./component/footer.php"; ?>
    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="js/scripts.js"></script>
</body>

</html>

<style>
    a {
        color: black;
        text-decoration: none;
    }

    #mainNav {
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
        border: none;
        background-color: #212529;
        transition: padding-top 0.3s ease-in-out, padding-bottom 0.3s ease-in-out;
    }
</style>