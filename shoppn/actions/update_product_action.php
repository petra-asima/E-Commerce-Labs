<?php
// Action: handles the Edit Product form submit (Task 9).
// Reads $_POST and $_FILES, validates, saves a new image if given, calls the controller, redirects.
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

// Brief: require_admin(). POST only.
require_admin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/admin/product.php');
}

$ctrl = new ProductController();

// ── 1. The product being edited must exist ──────────────
$id = filter_var($_POST['product_id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$existing = $id ? $ctrl->getProductById($id) : false;
if (!$existing) {
    $_SESSION['error'] = 'Product not found.';
    redirect('views/admin/product.php');
}

// ── 2. Collect and sanitise ─────────────────────────────
$title    = trim(strip_tags($_POST['product_title'] ?? ''));
$price    = trim($_POST['product_price'] ?? '');
$desc     = trim(strip_tags($_POST['product_desc'] ?? ''));
$keywords = trim(strip_tags($_POST['product_keywords'] ?? ''));
$cat      = filter_var($_POST['product_cat'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$brand    = filter_var($_POST['product_brand'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

$errors = [];

// ── 3. Validate (same rules as adding) ──────────────────
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

// ── 4. A new image is optional when editing ─────────────
$allowed  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
$maxSize  = 2 * 1024 * 1024;   // 2 MB
$file     = $_FILES['product_image'] ?? null;
$newImage = $file && $file['error'] !== UPLOAD_ERR_NO_FILE;
$ext      = null;

if ($newImage) {
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || $file['size'] > $maxSize) {
        $errors[] = 'The image must be 2 MB or smaller.';
    } elseif ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'The image could not be uploaded. Please try again.';
    } else {
        $info = @getimagesize($file['tmp_name']);
        if ($info === false || !isset($allowed[$info['mime']])) {
            $errors[] = 'The image must be a JPG, PNG, GIF or WEBP file.';
        } else {
            $ext = $allowed[$info['mime']];
        }
    }
}

if ($errors) {
    $_SESSION['error'] = implode(' ', $errors);
    redirect('views/admin/product.php?edit_id=' . $id);
}

// ── 5. Brief: move a new image, otherwise keep the existing filename ──
// The existing filename comes from the database, not a hidden form field,
// so nobody can point the product at a file they choose.
$filename = $existing['product_image'] ?? '';
if ($newImage) {
    $filename = uniqid('p_') . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../images/products/' . $filename)) {
        $_SESSION['error'] = 'The image could not be saved. Check that the images/products folder exists.';
        redirect('views/admin/product.php?edit_id=' . $id);
    }
}

if ($ctrl->updateProduct($id, $cat, $brand, $title, (float) $price, $desc, $filename, $keywords)) {
    // Remove the old image file once the new one is saved, so unused images don't pile up
    $oldImage = $existing['product_image'] ?? '';
    if ($newImage && $oldImage !== '' && $oldImage === basename($oldImage)) {
        @unlink(__DIR__ . '/../images/products/' . $oldImage);
    }
    $_SESSION['success'] = 'Product updated.';
} else {
    $_SESSION['error'] = 'Could not update the product. Please try again.';
}

redirect('views/admin/product.php');