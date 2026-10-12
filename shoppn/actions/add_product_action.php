<?php
// Action: handles the Add Product form submit (Task 9).
// Reads $_POST and $_FILES, validates, saves the image, calls the controller, redirects. No SQL, no HTML.
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

// Brief: require_admin(). POST only.
require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/admin/product.php');
}

// ── 1. Collect and sanitise ─────────────────────────────
$title    = trim(strip_tags($_POST['product_title'] ?? ''));
$price    = trim($_POST['product_price'] ?? '');
$desc     = trim(strip_tags($_POST['product_desc'] ?? ''));
$keywords = trim(strip_tags($_POST['product_keywords'] ?? ''));
$cat      = filter_var($_POST['product_cat'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$brand    = filter_var($_POST['product_brand'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

$ctrl   = new ProductController();
$errors = [];

// ── 2. Validate (limits match the products table) ───────
if (mb_strlen($title) < 2 || mb_strlen($title) > 200) {
    $errors[] = 'Title must be 2–200 characters.';
}
if (!preg_match('/^\d{1,7}(\.\d{1,2})?$/', $price) || (float) $price <= 0) {
    $errors[] = 'Price must be a number greater than 0, with up to 2 decimal places (e.g. 120 or 120.50).';
}
if (mb_strlen($desc) > 500) {
    $errors[] = 'Description must be 500 characters or fewer.';
}
if (mb_strlen($keywords) > 100) {
    $errors[] = 'Keywords must be 100 characters or fewer.';
}
if ($cat === false || !$ctrl->getCategoryById($cat)) {
    $errors[] = 'Choose a category from the list.';
}
if ($brand === false || !$ctrl->getBrandById($brand)) {
    $errors[] = 'Choose a brand from the list.';
}

// ── 3. Validate the image (type and size checked on the server) ──
$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
$maxSize = 2 * 1024 * 1024;   // 2 MB
$file    = $_FILES['product_image'] ?? null;
$ext     = null;

if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
    $errors[] = 'Choose a product image.';
} elseif ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || $file['size'] > $maxSize) {
    $errors[] = 'The image must be 2 MB or smaller.';
} elseif ($file['error'] !== UPLOAD_ERR_OK) {
    $errors[] = 'The image could not be uploaded. Please try again.';
} else {
    // Read the real file type from the file's contents, not the browser's label
    $info = @getimagesize($file['tmp_name']);
    if ($info === false || !isset($allowed[$info['mime']])) {
        $errors[] = 'The image must be a JPG, PNG, GIF or WEBP file.';
    } else {
        $ext = $allowed[$info['mime']];
    }
}

// Send the admin back with the errors, keeping what they typed
if ($errors) {
    $_SESSION['error'] = implode(' ', $errors);
    $_SESSION['old']   = compact('title', 'price', 'desc', 'keywords', 'cat', 'brand');
    redirect('views/admin/product.php');
}

// ── 4. Save the image with a safe, unique name ──────────
$filename = uniqid('p_') . '.' . $ext;
if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../images/products/' . $filename)) {
    $_SESSION['error'] = 'The image could not be saved. Check that the images/products folder exists.';
    $_SESSION['old']   = compact('title', 'price', 'desc', 'keywords', 'cat', 'brand');
    redirect('views/admin/product.php');
}

// ── 5. Brief: pass $filename to ProductController → addProduct() ──
if ($ctrl->addProduct($cat, $brand, $title, (float) $price, $desc, $filename, $keywords)) {
    $_SESSION['success'] = 'Product added.';
} else {
    @unlink(__DIR__ . '/../images/products/' . $filename);   // don't leave an orphan image
    $_SESSION['error'] = 'Could not add the product. Please try again.';
}

redirect('views/admin/product.php');