<?php

require_once '../vendor/autoload.php';
require_once './secrets.php';

\Stripe\Stripe::setApiKey($stripeSecretKey);
header('Content-Type: application/json');

$amount = $_GET['totalPrice'];
$bookingId = $_GET['bookingId'];
$YOUR_DOMAIN = 'http://localhost:3000';

$checkout_session = \Stripe\Checkout\Session::create([
  'line_items' => [[
    'price_data' => [
      'currency' => 'MYR',
      'unit_amount' => $amount * 100, // Stripe expects amount in cents
      'product_data' => [
        'name' => "Booking ID: $bookingId",
      ],
    ],
    'quantity' => 1,
  ]],
  'mode' => 'payment',
  'success_url' => $YOUR_DOMAIN . '/payment_result.php?bookingId=' . $bookingId . '&status=success&amount=' . $amount . '&session_id={CHECKOUT_SESSION_ID}',
  'cancel_url' =>  $YOUR_DOMAIN . '/payment_result.php?bookingId=' . $bookingId . '&status=cancel',
]);

// Redirect to Checkout
header("HTTP/1.1 303 See Other");
header("Location: " . $checkout_session->url);
