<?php
// Home page view (Task 10): featured products, or products filtered by category or brand.
// Gets data through ProductController — no SQL here.
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$ctrl = new ProductController();

// Brief: ?cat=N → by category, ?brand=N → by brand, otherwise featured products
$catId   = filter_var($_GET['cat'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$brandId = filter_var($_GET['brand'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($catId) {
    $products = $ctrl->getProductsByCategory($catId);
    $category = $ctrl->getCategoryById($catId);
    $heading  = $category ? $category['cat_name'] : 'Category';
} elseif ($brandId) {
    $products = $ctrl->getProductsByBrand($brandId);
    $brand    = $ctrl->getBrandById($brandId);
    $heading  = $brand ? $brand['brand_name'] : 'Brand';
} else {
    $products = $ctrl->getFeaturedProducts(6);
    $heading  = 'Featured products';
}
$filtered = $catId || $brandId;

$page_title = $filtered ? $heading . ' | shoppn' : 'shoppn | African prints, kente and ready-to-wear';
require __DIR__ . '/layout/header.php';
?>
<main id="main">
    <?php if (!$filtered): ?>
        <section class="hero">
            <div class="wrap hero-grid">
                <div class="hero-copy">
                    <?php if (!empty($_SESSION['error'])): ?>
                        <p class="alert alert-error" role="alert"><?= e($_SESSION['error']) ?></p>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    <h1>Wear the story.</h1>
                    <p>Authentic wax prints, hand-woven kente and ready-to-wear pieces, straight from Ghana's makers.</p>
                    <div class="hero-actions">
                        <a class="btn" href="#products">Shop the collection</a>
                        <?php if (is_logged_in()): ?>
                            <a class="btn btn-ghost" href="<?= url('views/account/my_account.php') ?>">My account</a>
                        <?php else: ?>
                            <a class="btn btn-ghost" href="<?= url('views/register.php') ?>">Create an account</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <div class="wrap page-layout">
        <?php require __DIR__ . '/layout/sidebar.php'; ?>

        <div class="main-col">
        <section id="products" class="products" aria-labelledby="products-heading">
            <div class="section-head">
                <h2 id="products-heading"><?= e($heading) ?></h2>
                <?php if ($filtered): ?>
                    <p class="muted"><?= count($products) ?> <?= count($products) === 1 ? 'product' : 'products' ?>.
                        <a href="<?= url() ?>">Show all</a></p>
                <?php endif; ?>
            </div>

            <?php if (empty($products)): ?>
                <!-- Brief: friendly 'No products found' message -->
                <div class="empty-state">
                    <p class="empty-title">No products found</p>
                    <p>Try another category or brand, or <a href="<?= url() ?>">browse all products</a>.</p>
                </div>
            <?php else: ?>
                <?php require __DIR__ . '/layout/product_grid.php'; ?>
            <?php endif; ?>
        </section>
        </div>
    </div>
</main>
<?php require __DIR__ . '/layout/footer.php'; ?>