<?php
// Shared page header: <head>, logo, search box, navigation. No SQL here.
// A page can set $minimal_header = true to show only the logo (used on login/register).
require_once __DIR__ . '/../../core/core.php';
$minimal = !empty($minimal_header);
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
    <div class="wrap header-row">
        <a class="logo" href="<?= url() ?>">shoppn</a>

        <?php if (!$minimal): ?>
        <form class="search" action="<?= url('views/search_results.php') ?>" method="get">
            <label for="user_query" class="visually-hidden">Search products</label>
            <input id="user_query" name="user_query" type="search" maxlength="100"
                   placeholder="Search wax prints, kente, dresses…" value="<?= e($_GET['user_query'] ?? '') ?>">
            <button type="submit">Search</button>
        </form>

        <nav class="header-nav" aria-label="Main">
            <ul class="nav-links">
                <li><a href="<?= url() ?>">Home</a></li>

                <?php if (is_admin()): ?>
                    <li><a href="<?= url('views/admin/brand.php') ?>">Brand</a></li>
                    <li><a href="<?= url('views/admin/category.php') ?>">Category</a></li>
                <?php endif; ?>
            </ul>

            <!-- Account area: kept separate from the navigation links -->
            <ul class="nav-links nav-account">
                <?php if (is_logged_in()): ?>
                    <?php $navName = $_SESSION['customer_name'] ?? ''; ?>
                    <li class="nav-welcome">
                        <span class="nav-avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($navName, 0, 1))) ?></span>
                        Welcome, <?= e($navName) ?>
                    </li>
                    <li><a href="<?= url('views/account/my_account.php') ?>">My account</a></li>
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
