<?php
// Shared page header: <head> and one top bar with the logo, Home, search, account links and the cart.
// Categories and brands are in the sidebar; admins also get Brands / Categories / Products links here.
// A page can set $minimal_header = true to show only the logo (used on login/register).
require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/CartController.php';

$minimal = !empty($minimal_header);

if (!$minimal) {
    // Which page we're on, so the current link can be highlighted
    $currentPage = basename($_SERVER['SCRIPT_NAME'] ?? '');
    $onHome      = $currentPage === 'index.php';

    // Cart summary, through the controller
    $cartCtrl  = new CartController();
    $cartCount = $cartCtrl->getCartCount(get_ip());
    $cartTotal = $cartCtrl->getCartTotal(get_ip());

    // A one-off cart message from add/remove/update (shown once, then cleared)
    $cartNotice = $_SESSION['cart_notice'] ?? null;
    unset($_SESSION['cart_notice']);

    // Greet by first name so a long full name doesn't crowd the header
    $navName      = $_SESSION['customer_name'] ?? '';
    $navFirstName = explode(' ', trim($navName))[0] ?? '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($page_title ?? 'shoppn') ?></title>
    <!-- Google Material Symbols: the cart, back-arrow and password icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,300,0,0&display=block">
    <link rel="stylesheet" href="<?= url('css/style.css') ?>">
    <script src="<?= url('js/site.js') ?>" defer></script>
</head>
<body>

<header class="site-header<?= $minimal ? ' site-header--minimal' : '' ?>">
    <div class="wrap header-top">
        <?php if ($minimal): ?>
            <!-- Login / register: a clear way back to the shop -->
            <a class="back-link header-back" href="<?= url() ?>">
                <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                <span class="header-back-text">Back to store</span>
            </a>
        <?php endif; ?>
        <a class="logo" href="<?= url() ?>">shoppn</a>

        <?php if (!$minimal): ?>
            <nav class="main-nav" aria-label="Main">
                <a href="<?= url() ?>" <?= $onHome ? 'aria-current="page"' : '' ?>>Home</a>
                <?php if (is_admin()): ?>
                    <!-- Admin links: only admins see these -->
                    <span class="admin-links">
                        <a href="<?= url('views/admin/brand.php') ?>" <?= $currentPage === 'brand.php' ? 'aria-current="page"' : '' ?>>Brands</a>
                        <a href="<?= url('views/admin/category.php') ?>" <?= $currentPage === 'category.php' ? 'aria-current="page"' : '' ?>>Categories</a>
                        <a href="<?= url('views/admin/product.php') ?>" <?= $currentPage === 'product.php' ? 'aria-current="page"' : '' ?>>Products</a>
                    </span>
                <?php endif; ?>
            </nav>

            <form class="search" action="<?= url('views/search_results.php') ?>" method="get" role="search">
                <label for="user_query" class="visually-hidden">Search products</label>
                <input id="user_query" name="user_query" type="search" maxlength="100"
                       placeholder="Search wax prints, kente, dresses…" value="<?= e($_GET['user_query'] ?? '') ?>">
                <button type="submit">Search</button>
            </form>

            <!-- Account links: Register | Log in, or Welcome [name] | My account | Log out -->
            <nav class="account-nav" aria-label="Account">
                <ul>
                    <?php if (is_logged_in()): ?>
                        <li class="nav-welcome">
                            <span class="nav-avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($navName, 0, 1))) ?></span>
                            <span class="nav-welcome-text">Welcome, <?= e($navFirstName) ?></span>
                        </li>
                        <li><a href="<?= url('views/account/my_account.php') ?>"
                               <?= $currentPage === 'my_account.php' ? 'aria-current="page"' : '' ?>>My account</a></li>
                        <li><a href="<?= url('logout.php') ?>">Log out</a></li>
                    <?php else: ?>
                        <li><a href="<?= url('views/register.php') ?>">Register</a></li>
                        <li><a class="nav-cta" href="<?= url('views/login.php') ?>">Log in</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

            <!-- Cart summary: bag icon with the number of items; hover shows the total -->
            <a class="cart-btn" href="<?= url('views/cart.php') ?>"
               title="<?= $cartCount ?> <?= $cartCount === 1 ? 'item' : 'items' ?> · GH₵ <?= number_format($cartTotal, 2) ?>"
               <?= $currentPage === 'cart.php' ? 'aria-current="page"' : '' ?>
               aria-label="Shopping cart: <?= $cartCount ?> <?= $cartCount === 1 ? 'item' : 'items' ?>, total GH₵ <?= number_format($cartTotal, 2) ?>. Go to cart">
                <span class="material-symbols-outlined" aria-hidden="true">shopping_bag</span>
                <span class="cart-badge"><?= $cartCount ?></span>
            </a>
        <?php endif; ?>
    </div>
</header>

<?php if (!$minimal && $cartNotice): ?>
    <div class="wrap notice-row">
        <p class="alert <?= $cartNotice['type'] === 'success' ? 'alert-success' : 'alert-error' ?>"
           role="<?= $cartNotice['type'] === 'success' ? 'status' : 'alert' ?>">
            <?= e($cartNotice['text']) ?>
            <?php if ($currentPage !== 'cart.php'): ?>
                <a href="<?= url('views/cart.php') ?>">View cart</a>
            <?php endif; ?>
        </p>
    </div>
<?php endif; ?>