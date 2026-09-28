<?php
//handles the login form submit.
//Reads $_POST, calls the controller, sets the session, redirects.
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

//POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/login.php');
}

$email = trim($_POST['customer_email'] ?? '');
$pass  = $_POST['customer_pass'] ?? '';

//Server-side validation 
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $pass === '') {
    $_SESSION['error'] = 'Enter a valid email address and your password.';
    redirect('views/login.php');
}

//calls CustomerController → login()
$ctrl   = new CustomerController();
$result = $ctrl->login($email, $pass);

//on failure, stores the error and go back to the login page
if (isset($result['error'])) {
    $_SESSION['error'] = $result['error'];
    redirect('views/login.php');
}

//new session ID at login
session_regenerate_id(true);

//stores customer_id, customer_name, customer_email, user_role
$_SESSION['customer_id']    = $result['customer_id'];
$_SESSION['customer_name']  = $result['customer_name'];
$_SESSION['customer_email'] = $result['customer_email'];
$_SESSION['user_role']      = (int) $result['user_role'];

//redirects to index.php
redirect('index.php');