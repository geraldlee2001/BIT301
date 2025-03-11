<?php

require_once '../vendor/autoload.php';
require_once '../php/secrets.php';
\Stripe\Stripe::setApiKey($stripeSecretKey);
header('Content-Type: application/json');
$amount = $_POST['totalPrice'];
$orderCode = $_POST['cartCode'];
$YOUR_DOMAIN = 'http://localhost:3000';

$stripe = new \Stripe\StripeClient($stripeSecretKey);
// Create a PaymentIntent
$paymentIntent = \Stripe\PaymentIntent::create([
  'amount' => $amount * 100, // because stripe is in cents
  'currency' => 'MYR',
  'metadata' => ['order_id' => $orderCode],
]);

$paymentIntentId = $paymentIntent->id;
$checkout_session = \Stripe\Checkout\Session::create([
  'line_items' => [[
    'price' => $paymentIntentId,
    'quantity' => 1,
  ]],
  'mode' => 'payment',
  'success_url' => $YOUR_DOMAIN . '/payment_result.php?code=' . $orderCode . '&status=success&price=' . $amount,
  'cancel_url' =>  $YOUR_DOMAIN . '/payment_result.php?code=' . $orderCode . '&status=cancel&price=' . $amount,
]);

header("HTTP/1.1 303 See Other");
header("Location: " . $checkout_session->url);
