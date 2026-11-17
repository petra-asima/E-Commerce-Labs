<?php
// Search results page.
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

// Read ?user_query from GET
$query = trim(strip_tags($_GET['user_query'] ?? ''));
$query = mb_substr($query, 0, 100);   // same limit as the search box

$ctrl     = new ProductController();
$products = $query === '' ? [] : $ctrl->searchProducts($query);

$page_title = ($query === '' ? 'Search' : 'Search: ' . $query) . ' | shoppn';
require __DIR__ . '/layout/header.php';
?>
<main id="main">
    <div class="wrap page-layout">
        <?php require __DIR__ . '/layout/sidebar.php'; ?>

        <section class="products" aria-labelledby="results-heading">
            <div class="section-head">
                <?php if ($query === ''): ?>
                    <h2 id="results-heading">Search the shop</h2>
                    <p class="muted">Type a product name or keyword, such as "kente" or "wax", in the search box above.</p>
                <?php else: ?>
                    <h2 id="results-heading">Results for "<?= e($query) ?>"</h2>
                    <p class="muted"><?= count($products) ?> <?= count($products) === 1 ? 'product' : 'products' ?> found</p>
                <?php endif; ?>
            </div>

            <?php if ($query !== '' && empty($products)): ?>
                <div class="empty-state">
                    <p class="empty-title">No products found</p>
                    <p>Check the spelling, try a shorter word, or <a href="<?= url() ?>">browse all products</a>.</p>
                </div>
            <?php elseif (!empty($products)): ?>
                <!-- Same card format as home.php -->
                <?php require __DIR__ . '/layout/product_grid.php'; ?>
            <?php endif; ?>
        </section>
    </div>
</main>
<?php require __DIR__ . '/layout/footer.php'; ?>