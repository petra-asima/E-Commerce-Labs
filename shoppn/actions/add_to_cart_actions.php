<?php
// Action: Add to Cart link (Task 11) — actions/add_to_cart_action.php?add_cart=N
// Reads the product id, checks for duplicates, calls the controller, redirects back. No SQL, no HTML.
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';
require_once __DIR__ . '/../controllers/ProductController.php';

/** Send the shopper back to the page they came from, but only if it is on this site. */
function back_to_referrer(): void
{
    $ref  = $_SERVER['HTTP_REFERER'] ?? '';
    $host = parse_url($ref, PHP_URL_HOST);
    if ($ref !== '' && $host !== null && strcasecmp($host, parse_url('//' . $_SERVER['HTTP_HOST'], PHP_URL_HOST)) === 0) {
        header('Location: ' . $ref);
        exit;
    }
    redirect('index.php');
}

// Brief: read ?add_cart=N from GET
$productId = filter_var($_GET['add_cart'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($productId === false || !(new ProductController())->getProductById($productId)) {
    $_SESSION['cart_notice'] = ['type' => 'error', 'text' => 'That product could not be found.'];
    back_to_referrer();
}

// Brief: get the client IP with get_ip() from core.php
$ip   = get_ip();
$cart = new CartController();

// Brief: duplicate check
if ($cart->isInCart($productId, $ip)) {
    $_SESSION['cart_notice'] = ['type' => 'error', 'text' => 'This item is already in your cart. Go to cart to update quantity.'];
    back_to_referrer();
}

// Save the customer id too when someone is logged in (guests get NULL)
$customerId = is_logged_in() ? (int) $_SESSION['customer_id'] : null;

if ($cart->addToCart($productId, 1, $ip, $customerId)) {
    $_SESSION['cart_notice'] = ['type' => 'success', 'text' => 'Added to your cart.'];
} else {
    $_SESSION['cart_notice'] = ['type' => 'error', 'text' => 'Could not add that item. Please try again.'];
}

// Brief: redirect back to the referring page
back_to_referrer();