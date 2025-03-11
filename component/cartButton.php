<?php
include "./php/tokenDecoding.php";
include "./php/databaseConnection.php";
$cartId = $decoded->cartId;

$query = "SELECT COUNT(*) FROM cartcartitem WHERE cart_id = \"$cartId\"";

$result = $conn->query($query)->fetch_assoc();
if ($decoded->customerId !== null) {
    echo  '<li class="nav-item">
    <button class="btn btn-primary" id="cartBtn">
    <i class="me-1 fa fa-shopping-basket"></i>
        Cart
        <span class="badge bg-yellow text-white ms-1 rounded-pill">' . $result['COUNT(*)'] . '</span>
    </button>
    </li>';
}
