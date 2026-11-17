<?php
// Single product page.
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

// Require a valid ?pro_id=N — redirect home if missing or invalid
$id = filter_var($_GET['pro_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id) {
    redirect('index.php');
}

$ctrl    = new ProductController();
$product = $ctrl->getProductById($id);
if (!$product) {
    redirect('index.php');
}

$page_title = $product['product_title'] . ' | shoppn';
require __DIR__ . '/layout/header.php';
?>
<main id="main" class="wrap product-page">
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="<?= url() ?>">Home</a>
        <span aria-hidden="true">/</span>
        <a href="<?= url('index.php?cat=' . (int) $product['product_cat']) ?>"><?= e($product['cat_name']) ?></a>
    </nav>

    <div class="product-detail">
        <div class="product-detail-media">
            <?php if (!empty($product['product_image'])): ?>
                <img src="<?= url('images/products/' . rawurlencode($product['product_image'])) ?>"
                     alt="<?= e($product['product_title']) ?>">
            <?php endif; ?>
        </div>

        <div class="product-detail-info">
            <a class="product-brand" href="<?= url('index.php?brand=' . (int) $product['product_brand']) ?>"><?= e($product['brand_name']) ?></a>
            <h1><?= e($product['product_title']) ?></h1>
            <p class="product-price">GH₵ <?= number_format((float) $product['product_price'], 2) ?></p>

            <a class="btn" href="<?= url('actions/add_to_cart_action.php?add_cart=' . (int) $product['product_id']) ?>">Add to cart</a>

            <?php if (!empty($product['product_desc'])): ?>
                <section class="product-section">
                    <h2>Description</h2>
                    <p><?= nl2br(e($product['product_desc'])) ?></p>
                </section>
            <?php endif; ?>

            <dl class="details">
                <div><dt>Category</dt><dd><?= e($product['cat_name']) ?></dd></div>
                <div><dt>Brand</dt><dd><?= e($product['brand_name']) ?></dd></div>
                <?php if (!empty($product['product_keywords'])): ?>
                    <div><dt>Keywords</dt><dd><?= e($product['product_keywords']) ?></dd></div>
                <?php endif; ?>
            </dl>
        </div>
    </div>
</main>
<?php require __DIR__ . '/layout/footer.php'; ?>