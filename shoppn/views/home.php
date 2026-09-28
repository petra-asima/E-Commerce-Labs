<?php
//Home page view. 
$page_title = 'shoppn | Home';
require __DIR__ . '/layout/header.php';
?>
<div class="wrap page-layout">
    <?php require __DIR__ . '/layout/sidebar.php'; ?>

    <main id="main">
        <section class="intro">
            <h1>Everything you need, delivered on campus.</h1>
            <p>Browse by category or brand, or search for a product by name.</p>
        </section>

        <section>
            <h2>Products</h2>
            <p class="empty-state">No products have been added yet. Check back soon.</p>
        </section>
    </main>
</div>
<?php require __DIR__ . '/layout/footer.php'; ?>