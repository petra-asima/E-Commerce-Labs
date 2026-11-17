<?php
// Handles the Remove from Cart form submit.
// Reads the ticked remove[] boxes, removes each product, redirects to the cart.
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

// POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/cart.php');
}

$ticked = $_POST['remove'] ?? [];
if (!is_array($ticked) || count($ticked) === 0) {
    $_SESSION['cart_notice'] = ['type' => 'error', 'text' => 'Tick the items you want to remove.'];
    redirect('views/cart.php');
}

$ip      = get_ip();
$cart    = new CartController();
$removed = 0;

foreach ($ticked as $value) {
    // Validate product_id is a positive integer
    $productId = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($productId !== false && $cart->removeFromCart($productId, $ip)) {
        $removed++;
    }
}

$_SESSION['cart_notice'] = $removed > 0
    ? ['type' => 'success', 'text' => $removed === 1 ? 'Item removed from your cart.' : $removed . ' items removed from your cart.']
    : ['type' => 'error', 'text' => 'Nothing was removed. Please try again.'];

redirect('views/cart.php');