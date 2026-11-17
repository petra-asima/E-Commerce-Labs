<?php
// Handles the Update Quantity form submit.
// Loops through qty[] and update[] (product ids), validates each quantity, updates, redirects.
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

// POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/cart.php');
}

$qtys = $_POST['qty'] ?? [];
$ids  = $_POST['update'] ?? [];

// The two arrays must line up: qty[0] belongs to update[0], and so on
if (!is_array($qtys) || !is_array($ids) || count($qtys) !== count($ids) || count($ids) === 0) {
    $_SESSION['cart_notice'] = ['type' => 'error', 'text' => 'Could not update your cart. Please try again.'];
    redirect('views/cart.php');
}

$ip   = get_ip();
$cart = new CartController();
$bad  = 0;

foreach (array_values($ids) as $i => $rawId) {
    $productId = filter_var($rawId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    // Each quantity must be a positive integer (capped at 99 per item)
    $qty = filter_var(trim((string) array_values($qtys)[$i]), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 99]]);

    if ($productId === false || $qty === false) {
        $bad++;
        continue;
    }
    $cart->updateQty($productId, $qty, $ip);
}

$_SESSION['cart_notice'] = $bad === 0
    ? ['type' => 'success', 'text' => 'Cart updated.']
    : ['type' => 'error', 'text' => 'Quantities must be whole numbers from 1 to 99. The other items were updated.'];

redirect('views/cart.php');