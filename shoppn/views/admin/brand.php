<?php
// Admin: manage brands — add and edit in one page.
require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/ProductController.php';

// Admins only, checked before any output
require_admin();

// Show success / error messages, then clear them
$success = $_SESSION['success'] ?? null;
$error   = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

$ctrl = new ProductController();

// If ?edit_id=N is present, load that brand into the form
$editBrand = null;
if (isset($_GET['edit_id'])) {
    $editId    = filter_var($_GET['edit_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $editBrand = $editId ? $ctrl->getBrandById($editId) : false;
    if (!$editBrand) {
        $error     = $error ?? 'Brand not found.';
        $editBrand = null;
    }
}

// Fetch all brands via ProductController → getAllBrands()
$brands = $ctrl->getAllBrands();

$page_title = 'Brands | Admin';
require __DIR__ . '/../layout/header.php';
?>
<main id="main" class="wrap admin-page">
<?php require __DIR__ . '/../layout/admin_nav.php'; ?>

    <div class="admin-head">
        <h1>Brands</h1>
        <p><?= count($brands) ?> <?= count($brands) === 1 ? 'brand' : 'brands' ?> in the store</p>
    </div>

    <?php if ($success): ?>
        <p class="alert alert-success" role="status"><?= e($success) ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p class="alert alert-error" role="alert"><?= e($error) ?></p>
    <?php endif; ?>

    <?php if ($editBrand): ?>
        <!-- Edit mode — form targets update_brand_action.php -->
        <section class="panel">
            <h2>Edit brand</h2>
            <form id="brand-form" class="inline-form" method="POST"
                  action="<?= url('actions/update_brand_action.php') ?>" novalidate>
                <input type="hidden" name="brand_id" value="<?= (int) $editBrand['brand_id'] ?>">
                <div class="field">
                    <label for="brand_name">Brand name</label>
                    <input id="brand_name" name="brand_name" type="text" maxlength="100"
                           value="<?= e($editBrand['brand_name']) ?>">
                    <span class="field-error" id="brand-error" aria-live="polite"></span>
                </div>
                <div class="inline-actions">
                    <button type="submit" class="btn">Save changes</button>
                    <a class="btn btn-ghost" href="<?= url('views/admin/brand.php') ?>">Cancel</a>
                </div>
            </form>
        </section>
    <?php else: ?>
        <!-- Add mode -->
        <section class="panel">
            <h2>Add a brand</h2>
            <form id="brand-form" class="inline-form" method="POST"
                  action="<?= url('actions/add_brand_action.php') ?>" novalidate>
                <div class="field">
                    <label for="brand_name">Brand name</label>
                    <input id="brand_name" name="brand_name" type="text" maxlength="100"
                           placeholder="e.g. Vlisco">
                    <span class="field-error" id="brand-error" aria-live="polite"></span>
                </div>
                <div class="inline-actions">
                    <button type="submit" class="btn">Add brand</button>
                </div>
            </form>
        </section>
    <?php endif; ?>

    <!-- Table of all brands with an Edit button per row -->
    <section class="panel panel--flush">
        <h2>All brands</h2>
        <?php if (empty($brands)): ?>
            <p class="empty-inline">No brands yet. Add your first brand above.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr><th scope="col">ID</th><th scope="col">Brand name</th><th scope="col"><span class="visually-hidden">Actions</span></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($brands as $b): ?>
                            <tr<?= ($editBrand && (int) $editBrand['brand_id'] === (int) $b['brand_id']) ? ' class="is-editing"' : '' ?>>
                                <td class="cell-id"><?= (int) $b['brand_id'] ?></td>
                                <td><?= e($b['brand_name']) ?></td>
                                <td class="cell-action">
                                    <a class="btn btn-small btn-ghost"
                                       href="<?= url('views/admin/brand.php?edit_id=' . (int) $b['brand_id']) ?>">Edit</a>
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
