
<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

require_login();


$customerId = $_SESSION['customer_id'];


$controller = new CustomerController();

$deleted = $controller->deleteAccount($customerId);


if ($deleted) {

    session_unset();

    session_destroy();

    session_start();

    $_SESSION['success'] = 'Your account has been deleted.';

    redirect(BASE_URL . 'index.php');
}


$_SESSION['error'] = 'Unable to delete your account.';

redirect(BASE_URL . 'views/account/my_account.php');

