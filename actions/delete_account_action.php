<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

require_login();


// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . 'views/account/my_account.php');
}


$customerId = $_SESSION['customer_id'];


$controller = new CustomerController();

$deleted = $controller->deleteAccount($customerId);


if ($deleted) {

    // Remove all session information
    session_unset();

    session_destroy();

    // Start a new session so we can display a message
    session_start();

    $_SESSION['success'] = 'Your account has been deleted successfully.';

    redirect(BASE_URL . 'index.php');
}


// Deletion failed
$_SESSION['error'] = 'Unable to delete your account.';

redirect(BASE_URL . 'views/account/my_account.php');

