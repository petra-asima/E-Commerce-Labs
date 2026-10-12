<?php
// Action: handles the Add Category form submit (Task 7).
// Reads $_POST, validates, calls the controller, sets a message, redirects. No SQL, no HTML.
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

// require_admin()
require_admin();

//  POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/admin/category.php');
}

//  sanitise
$name = trim(strip_tags($_POST['cat_name'] ?? ''));

// Server-side validation (same regex as the JavaScript; max 100 = DB column)
if (!preg_match("/^[A-Za-z0-9][A-Za-z0-9 &'().,\-]{1,99}$/", $name)) {
    $_SESSION['error'] = 'Category name must be 2-100 characters: letters, numbers, spaces and & \' ( ) . , - only.';
    redirect('views/admin/category.php');
}

//  call ProductController → addCategory(), redirect with success message
$ctrl = new ProductController();

if ($ctrl->addCategory($name)) {
    $_SESSION['success'] = 'Category added.';
} else {
    $_SESSION['error'] = 'Could not add the category. Please try again.';
}

redirect('views/admin/category.php');