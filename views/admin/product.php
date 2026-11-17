<?php
// Admin: manage products — add and edit in one page.
require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/ProductController.php';

// Admins only, checked before any output
require_admin();

// Show success / error messages, then clear them
$success = $_SESSION['success'] ?? null;
$error   = $_SESSION['error'] ?? null;
$old     = $_SESSION['old'] ?? [];
unset($_SESSION['success'], $_SESSION['error'], $_SESSION['old']);

$ctrl = new ProductController();

// Dropdowns come from getAllCategories() and getAllBrands()
$categories = $ctrl->getAllCategories();
$brands     = $ctrl->getAllBrands();

// Edit mode (?edit_id=N) pre-fills every field from getProductById()
$editProduct = null;
if (isset($_GET['edit_id'])) {
    $editId      = filter_var($_GET['edit_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $editProduct = $editId ? $ctrl->getProductById($editId) : false;
    if (!$editProduct) {
        $error       = $error ?? 'Product not found.';
        $editProduct = null;
    }
}

// Values to show in the form: the product being edited, or what was typed before an error
$form = [
    'title'    => $editProduct['product_title']    ?? ($old['title'] ?? ''),
    'price'    => $editProduct ? number_format((float) $editProduct['product_price'], 2, '.', '') : ($old['price'] ?? ''),
    'desc'     => $editProduct['product_desc']     ?? ($old['desc'] ?? ''),
    'keywords' => $editProduct['product_keywords'] ?? ($old['keywords'] ?? ''),
    'cat'      => (int) ($editProduct['product_cat']   ?? ($old['cat'] ?? 0)),
    'brand'    => (int) ($editProduct['product_brand'] ?? ($old['brand'] ?? 0)),
];

$products = $ctrl->getAllProducts();

$page_title = 'Products | Admin';
require __DIR__ . '/../layout/header.php';
?>
<main id="main" class="wrap admin-page">
<?php require __DIR__ . '/../layout/admin_nav.php'; ?>

    <div class="admin-head">
        <h1>Products</h1>
        <p><?= count($products) ?> <?= count($products) === 1 ? 'product' : 'products' ?> in the store</p>
    </div>

    <?php if ($success): ?>
        <p class="alert alert-success" role="status"><?= e($success) ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p class="alert alert-error" role="alert"><?= e($error) ?></p>
    <?php endif; ?>

    <?php if (empty($categories) || empty($brands)): ?>
        <!-- Products need a category and a brand (foreign keys), so those must exist first -->
        <section class="panel">
            <h2>Add a brand and a category first</h2>
            <p class="muted">Every product belongs to one category and one brand.
                <a href="<?= url('views/admin/brand.php') ?>">Add a brand</a> and
                <a href="<?= url('views/admin/category.php') ?>">add a category</a>, then come back here.</p>
        </section>
    <?php else: ?>
        <section class="panel">
            <h2><?= $editProduct ? 'Edit product' : 'Add a product' ?></h2>

            <!-- Multipart encoding is required for the image upload -->
            <form id="product-form" class="product-form" method="POST" enctype="multipart/form-data" novalidate
                  action="<?= url($editProduct ? 'actions/update_product_action.php' : 'actions/add_product_action.php') ?>"
                  data-image-required="<?= $editProduct ? 'no' : 'yes' ?>">

                <?php if ($editProduct): ?>
                    <input type="hidden" name="product_id" value="<?= (int) $editProduct['product_id'] ?>">
                <?php endif; ?>

                <div class="field">
                    <label for="product_title">Product title</label>
                    <input id="product_title" name="product_title" type="text" maxlength="200"
                           placeholder="e.g. Vlisco Classic Wax Print, 6 yards" value="<?= e($form['title']) ?>">
                    <span class="field-error" id="title-error" aria-live="polite"></span>
                </div>

                <div class="field-row">
                    <div class="field">
                        <label for="product_cat">Category</label>
                        <select id="product_cat" name="product_cat">
                            <option value="">Select</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= (int) $c['cat_id'] ?>" <?= $form['cat'] === (int) $c['cat_id'] ? 'selected' : '' ?>>
                                    <?= e($c['cat_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="field-error" id="cat-error" aria-live="polite"></span>
                    </div>

                    <div class="field">
                        <label for="product_brand">Brand</label>
                        <select id="product_brand" name="product_brand">
                            <option value="">Select</option>
                            <?php foreach ($brands as $b): ?>
                                <option value="<?= (int) $b['brand_id'] ?>" <?= $form['brand'] === (int) $b['brand_id'] ? 'selected' : '' ?>>
                                    <?= e($b['brand_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="field-error" id="brand-error" aria-live="polite"></span>
                    </div>
                </div>

                <div class="field-row">
                    <div class="field">
                        <label for="product_price">Price (GH₵)</label>
                        <input id="product_price" name="product_price" type="text" inputmode="decimal"
                               placeholder="e.g. 450.00" value="<?= e($form['price']) ?>">
                        <span class="field-error" id="price-error" aria-live="polite"></span>
                    </div>

                    <div class="field">
                        <label for="product_keywords">Keywords</label>
                        <input id="product_keywords" name="product_keywords" type="text" maxlength="100"
                               placeholder="e.g. wax, ankara, blue, 6 yards" value="<?= e($form['keywords']) ?>">
                        <span class="field-error" id="keywords-error" aria-live="polite"></span>
                    </div>
                </div>

                <div class="field">
                    <label for="product_desc">Description</label>
                    <textarea id="product_desc" name="product_desc" maxlength="500"
                              placeholder="Fabric, length, colours and how it can be worn."><?= e($form['desc']) ?></textarea>
                    <span class="field-error" id="desc-error" aria-live="polite"></span>
                </div>

                <div class="field">
                    <label for="product_image">Product image</label>
                    <?php if ($editProduct && !empty($editProduct['product_image'])): ?>
                        <!-- Show current image preview in edit mode -->
                        <div class="image-preview">
                            <img src="<?= url('images/products/' . rawurlencode($editProduct['product_image'])) ?>"
                                 alt="Current image of <?= e($editProduct['product_title']) ?>">
                            <span class="muted">Current image. Choose a new file only if you want to replace it.</span>
                        </div>
                    <?php endif; ?>
                    <input id="product_image" name="product_image" type="file"
                           accept="image/jpeg,image/png,image/gif,image/webp" aria-describedby="image-hint">
                    <small id="image-hint">JPG, PNG, GIF or WEBP, up to 2 MB.</small>
                    <span class="field-error" id="image-error" aria-live="polite"></span>
                </div>

                <div class="inline-actions">
                    <button type="submit" class="btn"><?= $editProduct ? 'Save changes' : 'Add product' ?></button>
                    <?php if ($editProduct): ?>
                        <a class="btn btn-ghost" href="<?= url('views/admin/product.php') ?>">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>
    <?php endif; ?>

    <!-- All products, each with an Edit button -->
    <section class="panel panel--flush">
        <h2>All products</h2>
        <?php if (empty($products)): ?>
            <p class="empty-inline">No products yet. Add your first product above.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col"><span class="visually-hidden">Image</span></th>
                            <th scope="col">Product</th>
                            <th scope="col">Category</th>
                            <th scope="col">Brand</th>
                            <th scope="col">Price</th>
                            <th scope="col"><span class="visually-hidden">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                            <tr<?= ($editProduct && (int) $editProduct['product_id'] === (int) $p['product_id']) ? ' class="is-editing"' : '' ?>>
                                <td class="cell-thumb">
                                    <?php if (!empty($p['product_image'])): ?>
                                        <img src="<?= url('images/products/' . rawurlencode($p['product_image'])) ?>" alt="">
                                    <?php endif; ?>
                                </td>
                                <td><?= e($p['product_title']) ?></td>
                                <td><?= e($p['cat_name']) ?></td>
                                <td><?= e($p['brand_name']) ?></td>
                                <td class="cell-price">GH₵ <?= number_format((float) $p['product_price'], 2) ?></td>
                                <td class="cell-action">
                                    <a class="btn btn-small btn-ghost"
                                       href="<?= url('views/admin/product.php?edit_id=' . (int) $p['product_id']) ?>">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>

<script src="<?= url('js/validate.js') ?>" defer></script>
<?php require __DIR__ . '/../layout/footer.php'; ?>