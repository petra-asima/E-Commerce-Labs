<?php
// Admin bar, shown at the top of the Brand, Category and Product admin pages.
// A back arrow plus tabs, so the admin can move between admin pages without going back to the home page.
require_once __DIR__ . '/../../controllers/ProductController.php';

$adminCtrl  = new ProductController();
$adminPage  = basename($_SERVER['SCRIPT_NAME'] ?? '');
$adminEdit  = isset($_GET['edit_id']);   // editing a brand, category or product

$adminTabs = [
    'brand.php'    => ['label' => 'Brands',     'icon' => 'sell',        'count' => count($adminCtrl->getAllBrands())],
    'category.php' => ['label' => 'Categories', 'icon' => 'category',    'count' => count($adminCtrl->getAllCategories())],
    'product.php'  => ['label' => 'Products',   'icon' => 'inventory_2', 'count' => count($adminCtrl->getAllProducts())],
];

// Back arrow: while editing, go back to this page's list; otherwise go back to the store
$backUrl   = $adminEdit ? url('views/admin/' . $adminPage) : url();
$backLabel = $adminEdit ? 'Back to all ' . strtolower($adminTabs[$adminPage]['label'] ?? 'items') : 'Back to store';
?>
<div class="admin-bar">
    <a class="back-link" href="<?= $backUrl ?>">
        <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
        <?= e($backLabel) ?>
    </a>

    <nav class="admin-tabs" aria-label="Store admin">
        <?php foreach ($adminTabs as $file => $tab): ?>
            <a href="<?= url('views/admin/' . $file) ?>" <?= $adminPage === $file ? 'aria-current="page"' : '' ?>>
                <span class="material-symbols-outlined" aria-hidden="true"><?= $tab['icon'] ?></span>
                <?= $tab['label'] ?>
                <span class="tab-count"><?= $tab['count'] ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
</div>