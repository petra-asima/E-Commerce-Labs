<?php
// Product cards, shared by home.php and search_results.php so both use the same card format.
// Expects $products (an array of rows from ProductController). Display only — no SQL.
?>
<div class="product-grid">
    <?php foreach ($products as $p): ?>
        <article class="product-card">
            <a class="product-media" href="<?= url('views/single_product.php?pro_id=' . (int) $p['product_id']) ?>">
                <?php if (!empty($p['product_image'])): ?>
                    <img src="<?= url('images/products/' . rawurlencode($p['product_image'])) ?>"
                         alt="<?= e($p['product_title']) ?>" loading="lazy">
                <?php endif; ?>
            </a>
            <div class="product-info">
                <span class="brand"><?= e($p['brand_name']) ?></span>
                <h3>
                    <a href="<?= url('views/single_product.php?pro_id=' . (int) $p['product_id']) ?>"><?= e($p['product_title']) ?></a>
                </h3>
                <span class="price">GH₵ <?= number_format((float) $p['product_price'], 2) ?></span>
                <div class="actions">
                    <a class="btn btn-small btn-ghost"
                       href="<?= url('views/single_product.php?pro_id=' . (int) $p['product_id']) ?>">Details</a>
                    <a class="btn btn-small"
                       href="<?= url('actions/add_to_cart_action.php?add_cart=' . (int) $p['product_id']) ?>">Add to cart</a>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
</div>