<?php
// Registration form view. Submits to actions/register_action.php.
require_once __DIR__ . '/../core/core.php';

// Already logged in? No need to register.
if (is_logged_in()) {
    redirect('views/account/my_account.php');
}

// Read (then clear) any error and previously typed values from the action
$error = $_SESSION['error'] ?? null;
$old   = $_SESSION['old'] ?? [];
unset($_SESSION['error'], $_SESSION['old']);

$selectedDial = $old['dial'] ?? 'GH';   // Ghana by default
if (!isset(DIAL_CODES[$selectedDial])) {
    $selectedDial = 'GH';
}

$page_title = 'Create an account | shoppn';
$minimal_header = true;
require __DIR__ . '/layout/header.php';
?>
<main id="main" class="auth">
    <aside class="auth-art">
        <div class="auth-art-inner">
            <h2>Every cloth carries a story.</h2>
            <p>Wax prints, kente and ready-to-wear, sourced from Ghana's makers.</p>
            <ul class="auth-points">
                <li>Shop authentic wax prints and kente</li>
                <li>Save your details for faster checkout</li>
                <li>See all your orders in one place</li>
            </ul>
        </div>
    </aside>

    <div class="auth-panel">
        <div class="auth-card">
            <h1>Create your account</h1>
            <p class="auth-lead">Save your details for faster checkout.</p>

            <?php if ($error): ?>
                <p class="alert alert-error" role="alert"><?= e($error) ?></p>
            <?php endif; ?>

            <form id="register-form" method="POST" action="<?= url('actions/register_action.php') ?>" novalidate>
                <div class="field">
                    <label for="customer_name">Full name</label>
                    <input id="customer_name" name="customer_name" type="text" maxlength="100"
                           autocomplete="name" value="<?= e($old['name'] ?? '') ?>">
                    <span class="field-error" id="name-error" aria-live="polite"></span>
                </div>

                <div class="field">
                    <label for="customer_email">Email</label>
                    <input id="customer_email" name="customer_email" type="email" maxlength="50"
                           autocomplete="email" value="<?= e($old['email'] ?? '') ?>">
                    <span class="field-error" id="email-error" aria-live="polite"></span>
                </div>

                <div class="field">
                    <label for="customer_pass">Password</label>
                    <input id="customer_pass" name="customer_pass" type="password" maxlength="72"
                           autocomplete="new-password" aria-describedby="pass-hint">
                    <small id="pass-hint">8+ characters with uppercase, lowercase, a number and a special character (e.g. Akwaaba@2026).</small>
                    <span class="field-error" id="pass-error" aria-live="polite"></span>
                </div>

                <div class="field-row">
                    <div class="field">
                        <label for="customer_country">Country</label>
                        <select id="customer_country" name="customer_country">
                            <option value="">Select</option>
                            <?php foreach (COUNTRIES as $c): ?>
                                <option value="<?= e($c) ?>" <?= ($old['country'] ?? '') === $c ? 'selected' : '' ?>>
                                    <?= e($c) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="field-error" id="country-error" aria-live="polite"></span>
                    </div>

                    <div class="field">
                        <label for="customer_city">City</label>
                        <input id="customer_city" name="customer_city" type="text" maxlength="30"
                               autocomplete="address-level2" value="<?= e($old['city'] ?? '') ?>">
                        <span class="field-error" id="city-error" aria-live="polite"></span>
                    </div>
                </div>

                <div class="field">
                    <label for="customer_contact">Contact number</label>
                    <div class="phone-input">
                        <img id="dial-flag" class="flag" width="24" height="18" alt="" onerror="this.hidden = true"
                             src="https://flagcdn.com/w40/<?= e(strtolower($selectedDial)) ?>.png">
                        <label for="dial_code" class="visually-hidden">Country code</label>
                        <select id="dial_code" name="dial_code">
                            <?php foreach (DIAL_CODES as $iso => [$code, $countryName]): ?>
                                <option value="<?= e($iso) ?>" <?= $selectedDial === $iso ? 'selected' : '' ?>>
                                    <?= e($iso . ' ' . $code) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input id="customer_contact" name="customer_contact" type="tel" maxlength="14"
                               autocomplete="tel-national" placeholder="24 123 4567"
                               value="<?= e($old['contact'] ?? '') ?>">
                    </div>
                    <span class="field-error" id="contact-error" aria-live="polite"></span>
                </div>

                <button type="submit" class="btn btn-block">Create account</button>
            </form>

            <p class="form-switch">Already have an account? <a href="<?= url('views/login.php') ?>">Log in</a></p>
        </div>
    </div>
</main>

<script src="<?= url('js/validate.js') ?>" defer></script>
<?php require __DIR__ . '/layout/footer.php'; ?>
