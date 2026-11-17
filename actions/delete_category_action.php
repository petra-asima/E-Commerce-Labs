<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . 'views/admin/category.php');
}

$categoryId = $_POST['cat_id'] ?? '';

if (
    !filter_var($categoryId, FILTER_VALIDATE_INT) ||
    (int)$categoryId <= 0
) {
    $_SESSION['error'] = 'Invalid category ID.';
    redirect(BASE_URL . 'views/admin/category.php');
}

$categoryId = (int)$categoryId;

$controller = new ProductController();

$result = $controller->deleteCategory($categoryId);

if ($result) {
    $_SESSION['success'] = 'Category deleted.';
} else {
    $_SESSION['error'] = 'Failed to delete category.';
}

redirect(BASE_URL . 'views/admin/category.php');