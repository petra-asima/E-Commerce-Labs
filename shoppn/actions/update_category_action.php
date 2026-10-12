<?php
// Action: handles the Edit Category form submit (Task 8).
// Reads $_POST, validates, calls the controller, sets a message, redirects. No SQL, no HTML.
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

// Brief: require_admin()
require_admin();

// Brief: POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/admin/category.php');
}

// Brief: validate id (must be a positive integer) and name
$id   = filter_var($_POST['cat_id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$name = trim(strip_tags($_POST['cat_name'] ?? ''));

if ($id === false) {
    $_SESSION['error'] = 'Invalid category.';
    redirect('views/admin/category.php');
}
if (!preg_match("/^[A-Za-z0-9][A-Za-z0-9 &'().,\-]{1,99}$/", $name)) {
    $_SESSION['error'] = 'Category name must be 2–100 characters: letters, numbers, spaces and & \' ( ) . , - only.';
    redirect('views/admin/category.php?edit_id=' . $id);
}

$ctrl = new ProductController();

// The id must belong to a category that actually exists
if (!$ctrl->getCategoryById($id)) {
    $_SESSION['error'] = 'Category not found.';
    redirect('views/admin/category.php');
}

// Brief: update and redirect
if ($ctrl->updateCategory($id, $name)) {
    $_SESSION['success'] = 'Category updated.';
} else {
    $_SESSION['error'] = 'Could not update the category. Please try again.';
}

redirect('views/admin/category.php');