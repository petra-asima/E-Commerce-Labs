<?php
// Admin: manage categories — add (Task 7) and edit (Task 8) in one page.
require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/ProductController.php';

// Brief: require_admin() at the top — before any HTML
require_admin();

// Show success / error messages, then clear them
$success = $_SESSION['success'] ?? null;
$error   = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);

$ctrl = new ProductController();

// Task 8: if ?edit_id=N is present, load that category into the form
$editCategory = null;
if (isset($_GET['edit_id'])) {
    $editId       = filter_var($_GET['edit_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $editCategory = $editId ? $ctrl->getCategoryById($editId) : false;
    if (!$editCategory) {
        $error        = $error ?? 'Category not found.';
        $editCategory = null;
    }
}

// Fetch all categories via ProductController → getAllCategories()
$categories = $ctrl->getAllCategories();

$page_title = 'Categories | Admin';
require __DIR__ . '/../layout/header.php';
?>
<main id="main" class="wrap admin-page">

    <div class="admin-head">
        <h1>Categories</h1>
        <p><?= count($categories) ?> <?= count($categories) === 1 ? 'category' : 'categories' ?> in the store</p>
    </div>

    <?php if ($success): ?>
        <p class="alert alert-success" role="status"><?= e($success) ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p class="alert alert-error" role="alert"><?= e($error) ?></p>
    <?php endif; ?>

    <?php if ($editCategory): ?>
        <!-- Task 8: edit mode — form targets update_category_action.php -->
        <section class="panel">
            <h2>Edit category</h2>
            <form id="category-form" class="inline-form" method="POST"
                  action="<?= url('actions/update_category_action.php') ?>" novalidate>
                <input type="hidden" name="cat_id" value="<?= (int) $editCategory['cat_id'] ?>">
                <div class="field">
                    <label for="cat_name">Category name</label>
                    <input id="cat_name" name="cat_name" type="text" maxlength="100"
                           value="<?= e($editCategory['cat_name']) ?>">
                    <span class="field-error" id="category-error" aria-live="polite"></span>
                </div>
                <div class="inline-actions">
                    <button type="submit" class="btn">Save changes</button>
                    <a class="btn btn-ghost" href="<?= url('views/admin/category.php') ?>">Cancel</a>
                </div>
            </form>
        </section>
    <?php else: ?>
        <!-- Task 7: add mode — form for adding a category name -->
        <section class="panel">
            <h2>Add a category</h2>
            <form id="category-form" class="inline-form" method="POST"
                  action="<?= url('actions/add_category_action.php') ?>" novalidate>
                <div class="field">
                    <label for="cat_name">Category name</label>
                    <input id="cat_name" name="cat_name" type="text" maxlength="100"
                           placeholder="e.g. Wax prints">
                    <span class="field-error" id="category-error" aria-live="polite"></span>
                </div>
                <div class="inline-actions">
                    <button type="submit" class="btn">Add category</button>
                </div>
            </form>
        </section>
    <?php endif; ?>

    <!-- Table of all categories with Edit buttons -->
    <section class="panel panel--flush">
        <h2>All categories</h2>
        <?php if (empty($categories)): ?>
            <p class="empty-inline">No categories yet. Add your first category above.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr><th scope="col">ID</th><th scope="col">Category name</th><th scope="col"><span class="visually-hidden">Actions</span></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $c): ?>
                            <tr<?= ($editCategory && (int) $editCategory['cat_id'] === (int) $c['cat_id']) ? ' class="is-editing"' : '' ?>>
                                <td class="cell-id"><?= (int) $c['cat_id'] ?></td>
                                <td><?= e($c['cat_name']) ?></td>
                                <td class="cell-action">
                                    <a class="btn btn-small btn-ghost"
                                       href="<?= url('views/admin/category.php?edit_id=' . (int) $c['cat_id']) ?>">Edit</a>
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