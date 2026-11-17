<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

require_login();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    redirect(BASE_URL . 'views/account/change_pass.php');
}


$currentPassword = $_POST['current_password'] ?? '';

$newPassword = $_POST['new_password'] ?? '';

$confirmPassword = $_POST['confirm_password'] ?? '';


// Check that all fields were completed
if (
    $currentPassword === '' ||
    $newPassword === '' ||
    $confirmPassword === ''
) {

    $_SESSION['error'] = 'All password fields are required.';

    redirect(BASE_URL . 'views/account/change_pass.php');
}


// Check that the new passwords match
if ($newPassword !== $confirmPassword) {

    $_SESSION['error'] = 'The new passwords do not match.';

    redirect(BASE_URL . 'views/account/change_pass.php');
}


// Make sure the new password is different
if ($currentPassword === $newPassword) {

    $_SESSION['error'] =
        'Your new password must be different from your current password.';

    redirect(BASE_URL . 'views/account/change_pass.php');
}


// Check new password strength
if (
    strlen($newPassword) < 8 ||
    !preg_match('/[a-z]/', $newPassword) ||
    !preg_match('/[A-Z]/', $newPassword) ||
    !preg_match('/[0-9]/', $newPassword) ||
    !preg_match('/[^A-Za-z0-9]/', $newPassword)
) {

    $_SESSION['error'] =
        'Password must be at least 8 characters and include uppercase and lowercase letters, a number, and a special character (e.g., !, @, #, $).';

    redirect(BASE_URL . 'views/account/change_pass.php');
}


// Change the password
$controller = new CustomerController();

$changed = $controller->changePassword(
    $_SESSION['customer_id'],
    $currentPassword,
    $newPassword
);


if ($changed) {

    $_SESSION['success'] =
        'Your password has been changed successfully.';

    redirect(BASE_URL . 'views/account/change_pass.php');
}


// Current password was incorrect
$_SESSION['error'] = 'Current password is incorrect.';

redirect(BASE_URL . 'views/account/change_pass.php');

