<?php
// Categories & brands sidebar (Task 10).
// Gets its lists through ProductController — no SQL here.
// Categories, Brands and (admins only) Store admin are dropdowns (<details>): tap the heading to open or close.
require_once __DIR__ . '/../../controllers/ProductController.php';

$sidebarCtrl       = new ProductController();
$sidebarCategories = $sidebarCtrl->getAllCategories();
$sidebarBrands     = $sidebarCtrl->getAllBrands();

// Which filter is active, so it can be highlighted and its list starts open
$activeCat   = (int) ($_GET['cat'] ?? 0);
$activeBrand = (int) ($_GET['brand'] ?? 0);
$showingAll  = !$activeCat && !$activeBrand;
?>
<aside class="sidebar" aria-label="Browse the store">
    <p class="sidebar-title">Shop by</p>

    <a class="sidebar-all" href="<?= url() ?>" <?= $showingAll ? 'aria-current="page"' : '' ?>>All products</a>

    <details class="sidebar-group" <?= $activeCat ? 'open' : '' ?>>
        <summary>Categories <span class="sidebar-count"><?= count($sidebarCategories) ?></span></summary>
        <?php if (empty($sidebarCategories)): ?>
            <p class="muted">No categories yet.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($sidebarCategories as $c): ?>
                    <li>
                        <a href="<?= url('index.php?cat=' . (int) $c['cat_id']) ?>"
                           <?= $activeCat === (int) $c['cat_id'] ? 'aria-current="page"' : '' ?>><?= e($c['cat_name']) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </details>

    <details class="sidebar-group" <?= $activeBrand ? 'open' : '' ?>>
        <summary>Brands <span class="sidebar-count"><?= count($sidebarBrands) ?></span></summary>
        <?php if (empty($sidebarBrands)): ?>
            <p class="muted">No brands yet.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($sidebarBrands as $b): ?>
                    <li>
                        <a href="<?= url('index.php?brand=' . (int) $b['brand_id']) ?>"
                           <?= $activeBrand === (int) $b['brand_id'] ? 'aria-current="page"' : '' ?>><?= e($b['brand_name']) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </details>

    <?php if (is_admin()): ?>
        <!-- Admin only (Tasks 5, 7, 9): links to manage the store -->
        <details class="sidebar-group sidebar-admin">
            <summary>Store admin</summary>
            <ul>
                <li><a href="<?= url('views/admin/brand.php') ?>">Brands</a></li>
                <li><a href="<?= url('views/admin/category.php') ?>">Categories</a></li>
                <li><a href="<?= url('views/admin/product.php') ?>">Products</a></li>
            </ul>
        </details>
    <?php endif; ?>
</aside>