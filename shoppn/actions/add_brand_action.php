<?php
// Action: handles the Add Brand form submit.
// Reads $_POST, validates, calls the controller, sets a message, redirects. No SQL, no HTML.
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

//  require_admin() at the top
require_admin();

// POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/admin/brand.php');
}

// sanitise the brand name
$name = trim(strip_tags($_POST['brand_name'] ?? ''));

// Server-side validation (same regex as the JavaScript; max 100 = DB column)
if (!preg_match("/^[A-Za-z0-9][A-Za-z0-9 &'().,\-]{1,99}$/", $name)) {
    $_SESSION['error'] = 'Brand name must be 2-100 characters: letters, numbers, spaces and & \' ( ) . , - only.';
    redirect('views/admin/brand.php');
}

// Brief: call ProductController → addBrand()
$ctrl = new ProductController();

if ($ctrl->addBrand($name)) {
    $_SESSION['success'] = 'Brand added.';          // brief's exact message
} else {
    $_SESSION['error'] = 'Could not add the brand. Please try again.';
}

redirect('views/admin/brand.php');