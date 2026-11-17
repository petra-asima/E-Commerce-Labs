<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . 'views/admin/brand.php');
}

$brandId = $_POST['brand_id'] ?? '';

if (
    !filter_var($brandId, FILTER_VALIDATE_INT) ||
    (int)$brandId <= 0
) {
    $_SESSION['error'] = 'Invalid brand ID.';
    redirect(BASE_URL . 'views/admin/brand.php');
}

$brandId = (int)$brandId;

$controller = new ProductController();

$result = $controller->deleteBrand($brandId);

if ($result) {
    $_SESSION['success'] = 'Brand deleted.';
} else {
    $_SESSION['error'] = 'Failed to delete brand.';
}

redirect(BASE_URL . 'views/admin/brand.php');