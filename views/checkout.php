<?php
// Brief fix: the cart's Checkout button (Task 12) links to views/checkout.php,
// but Task 13 calls the payment page views/payment.php. This file just sends the customer there.
require_once __DIR__ . '/../core/core.php';
redirect('views/payment.php');