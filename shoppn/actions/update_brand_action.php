<?php
// Action: handles the Edit Brand form submit.
// Reads $_POST, validates, calls the controller, sets a message, redirects. No SQL, no HTML.
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

// require_admin()
require_admin();

//  POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/admin/brand.php');
}

// validate brand_id (must be a positive integer) and brand_name
$id   = filter_var($_POST['brand_id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$name = trim(strip_tags($_POST['brand_name'] ?? ''));

if ($id === false) {
    $_SESSION['error'] = 'Invalid brand.';
    redirect('views/admin/brand.php');
}
if (!preg_match("/^[A-Za-z0-9][A-Za-z0-9 &'().,\-]{1,99}$/", $name)) {
    $_SESSION['error'] = 'Brand name must be 2-100 characters: letters, numbers, spaces and & \' ( ) . , - only.';
    redirect('views/admin/brand.php?edit_id=' . $id);
}

$ctrl = new ProductController();

// The id must belong to a brand that actually exists
if (!$ctrl->getBrandById($id)) {
    $_SESSION['error'] = 'Brand not found.';
    redirect('views/admin/brand.php');
}

// call ProductController → updateBrand(), redirect with a success message
if ($ctrl->updateBrand($id, $name)) {
    $_SESSION['success'] = 'Brand updated.';
} else {
    $_SESSION['error'] = 'Could not update the brand. Please try again.';
}

redirect('views/admin/brand.php');