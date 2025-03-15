<?php

require_once '../vendor/autoload.php';
require_once '../php/secrets.php';
\Stripe\Stripe::setApiKey($stripeSecretKey);
header('Content-Type: application/json');
$amount = $_GET['totalPrice'];
$bookingId = $_GET['bookingId'];
$YOUR_DOMAIN = 'http://localhost:3000';
$stripe = new \Stripe\StripeClient($stripeSecretKey);
// Create a PaymentIntent
$paymentIntent = \Stripe\Price::create([
  'product_data' => [
    'name' => $bookingId,
  ],
  'unit_amount' => $amount * 100, // because stripe is in cents
  'currency' => 'MYR',
]);

$paymentIntentId = $paymentIntent->id;
$checkout_session = \Stripe\Checkout\Session::create([
  'line_items' => [[
    'price' => $paymentIntentId,
    'quantity' => 1,
  ]],
  'mode' => 'payment',
  'success_url' => $YOUR_DOMAIN . '/payment_result.php?bookingId=' . $bookingId . '&status=success&price=' . $amount,
  'cancel_url' =>  $YOUR_DOMAIN . '/payment_result.php?bookingId=' . $bookingId . '&status=cancel&price=' . $amount,
]);

header("HTTP/1.1 303 See Other");
header("Location: " . $checkout_session->url);
