<?php
// Shared page header: <head> and one top bar with the logo, Home, search and account links.
// Categories, brands and the admin links are in the sidebar.
// A page can set $minimal_header = true to show only the logo (used on login/register). No SQL here.
require_once __DIR__ . '/../../core/core.php';

$minimal = !empty($minimal_header);

if (!$minimal) {
    // Which page we're on, so the current link can be highlighted
    $currentPage = basename($_SERVER['SCRIPT_NAME'] ?? '');
    $onHome      = $currentPage === 'index.php';

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
    <link rel="stylesheet" href="<?= url('css/style.css') ?>">
</head>
<body>

<header class="site-header<?= $minimal ? ' site-header--minimal' : '' ?>">
    <div class="wrap header-top">
        <a class="logo" href="<?= url() ?>">shoppn</a>

        <?php if (!$minimal): ?>
            <nav class="main-nav" aria-label="Main">
                <a href="<?= url() ?>" <?= $onHome ? 'aria-current="page"' : '' ?>>Home</a>
            </nav>

            <form class="search" action="<?= url('views/search_results.php') ?>" method="get" role="search">
                <label for="user_query" class="visually-hidden">Search products</label>
                <input id="user_query" name="user_query" type="search" maxlength="100"
                       placeholder="Search wax prints, kente, dresses…" value="<?= e($_GET['user_query'] ?? '') ?>">
                <button type="submit">Search</button>
            </form>

            <!-- Account links (Task 4): Register | Log in, or Welcome [name] | My account | Log out -->
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
        <?php endif; ?>
    </div>
</header>