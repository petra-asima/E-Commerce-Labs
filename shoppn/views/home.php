<?php
// Home page view. From Task 10 this shows the product grid.
$page_title = 'shoppn | African prints, kente and ready-to-wear';
require __DIR__ . '/layout/header.php';
?>
<main id="main">
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

    <div class="wrap page-layout">
        <?php require __DIR__ . '/layout/sidebar.php'; ?>

        <section id="products" class="products" aria-labelledby="products-heading">
            <div class="section-head">
                <h2 id="products-heading">New in the shop</h2>
            </div>
            <div class="empty-state">
                <p class="empty-title">No products yet</p>
                <p>Fabrics and ready-to-wear pieces will appear here as soon as they're added.</p>
            </div>
        </section>
    </div>
</main>
<?php require __DIR__ . '/layout/footer.php'; ?>
