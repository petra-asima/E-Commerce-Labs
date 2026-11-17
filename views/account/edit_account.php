<?php

require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/CustomerController.php';

require_login();


$controller = new CustomerController();

$customer = $controller->getCustomer(
    $_SESSION['customer_id']
);


if ($customer === false) {

    $_SESSION['error'] = 'Unable to load your account information.';

    redirect(BASE_URL . 'views/account/my_account.php');
}

?>

<?php require_once __DIR__ . '/../layout/header.php'; ?>

<main>

    <h1>Edit Account</h1>


    <?php if (isset($_SESSION['error'])): ?>

        <div class="error-message">

            <?php echo htmlspecialchars($_SESSION['error']); ?>

        </div>

        <?php unset($_SESSION['error']); ?>

    <?php endif; ?>


    <form
        action="<?php echo BASE_URL; ?>actions/edit_account_action.php"
        method="POST"
    >

        <div>

            <label for="name">
                Full Name
            </label>

            <input
                type="text"
                id="name"
                name="name"
                value="<?php echo htmlspecialchars($customer['customer_name']); ?>"
                required
            >

        </div>


        <div>

            <label for="email">
                Email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                value="<?php echo htmlspecialchars($customer['customer_email']); ?>"
                required
            >

        </div>


        <div>

            <label for="country">
                Country
            </label>

            <input
                type="text"
                id="country"
                name="country"
                value="<?php echo htmlspecialchars($customer['customer_country']); ?>"
                required
            >

        </div>


        <div>

            <label for="city">
                City
            </label>

            <input
                type="text"
                id="city"
                name="city"
                value="<?php echo htmlspecialchars($customer['customer_city']); ?>"
                required
            >

        </div>


        <div>

            <label for="contact">
                Contact Number
            </label>

            <input
                type="tel"
                id="contact"
                name="contact"
                value="<?php echo htmlspecialchars($customer['customer_contact']); ?>"
                required
            >

        </div>


        <button type="submit">
            Save Changes
        </button>

    </form>

</main>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

