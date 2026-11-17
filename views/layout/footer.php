<footer class="site-footer">
    <?php if (empty($minimal_header)): ?>
    <div class="wrap footer-grid">
        <div>
            <a class="logo logo--light" href="<?= url() ?>">shoppn</a>
            <p>Wax prints, kente and ready-to-wear, sourced from Ghana's textile makers and weavers.</p>
        </div>
        <div>
            <h2>Shop</h2>
            <ul>
                <li><a href="<?= url('index.php#products') ?>">New in the shop</a></li>
            </ul>
        </div>
        <div>
            <h2>Account</h2>
            <ul>
                <?php if (is_logged_in()): ?>
                    <li><a href="<?= url('views/account/my_account.php') ?>">My account</a></li>
                    <li><a href="<?= url('logout.php') ?>">Log out</a></li>
                <?php else: ?>
                    <li><a href="<?= url('views/login.php') ?>">Log in</a></li>
                    <li><a href="<?= url('views/register.php') ?>">Create an account</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>
    <div class="wrap footer-bottom">
        <p>&copy; <?= date('Y') ?> shoppn. All rights reserved.</p>
    </div>
</footer>
</body>
</html>
