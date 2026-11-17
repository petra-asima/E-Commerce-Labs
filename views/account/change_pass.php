
<?php

require_once __DIR__ . '/../../core/core.php';

require_login();

?>

<?php require_once __DIR__ . '/../layout/header.php'; ?>

<main>

    <h1>Change Password</h1>

    <?php if (isset($_SESSION['error'])): ?>

        <div class="error-message">
            <?php echo htmlspecialchars($_SESSION['error']); ?>
        </div>

        <?php unset($_SESSION['error']); ?>

    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
         <div class="success-message">
             <?php echo htmlspecialchars($_SESSION['success']); ?>
             </div> 

        <?php unset($_SESSION['success']); ?> <?php endif; ?>


    <form
        action="<?php echo BASE_URL; ?>actions/change_pass_action.php"
        method="POST"
    >

        <div>
            <label for="current_password">
                Current Password
            </label>

            <input
                type="password"
                id="current_password"
                name="current_password"
                required
            >
        </div>


        <div>
            <label for="new_password">
                New Password
            </label>

            <input
                type="password"
                id="new_password"
                name="new_password"
                required
            >
        </div>


        <div>
            <label for="confirm_password">
                Confirm New Password
            </label>

            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                required
            >
        </div>


        <button type="submit">
            Change Password
        </button>

    </form>

</main>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>

