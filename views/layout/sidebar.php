<?php
// Categories & brands sidebar.
// Categories and Brands are dropdowns (<details>): tap the heading to open or close the list.
require_once __DIR__ . '/../../controllers/ProductController.php';

$sidebarCtrl       = new ProductController();
$sidebarCategories = $sidebarCtrl->getCategoriesWithProducts();   // empty categories are hidden
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
        <summary>Categories</summary>
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
        <summary>Brands</summary>
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
</aside>