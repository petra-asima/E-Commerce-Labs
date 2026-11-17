<?php
// Cart page: shows this visitor's cart and lets them remove items or change quantities.
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

$cartCtrl = new CartController();
$ip       = get_ip();

// Load the cart contents
$items    = $cartCtrl->getCartItems($ip);
$subtotal = $cartCtrl->getCartTotal($ip);

$page_title = 'Your cart | shoppn';
require __DIR__ . '/layout/header.php';
?>
<main id="main" class="wrap cart-page">

    <div class="admin-head">
        <h1>Your cart</h1>
        <p><?= count($items) ?> <?= count($items) === 1 ? 'item' : 'items' ?></p>
    </div>

    <?php if (empty($items)): ?>
        <div class="empty-state">
            <p class="empty-title">Your cart is empty</p>
            <p>Browse our wax prints, kente and ready-to-wear pieces and add something you love.</p>
            <p><a class="btn" href="<?= url('index.php') ?>">Continue shopping</a></p>
        </div>
    <?php else: ?>
        <!-- Two separate forms. The table inputs join one or the other with the form="" attribute. -->
        <form id="remove-form" method="POST" action="<?= url('actions/remove_from_cart_action.php') ?>"></form>
        <form id="update-form" method="POST" action="<?= url('actions/update_qty_action.php') ?>" novalidate></form>

        <section class="panel panel--flush">
            <div class="table-wrap">
                <table class="table cart-table">
                    <thead>
                        <tr>
                            <th scope="col">Remove</th>
                            <th scope="col">Product</th>
                            <th scope="col">Qty</th>
                            <th scope="col" class="cell-price">Unit price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $i => $item): ?>
                            <?php $pid = (int) $item['p_id']; ?>
                            <tr>
                                <td class="cell-check">
                                    <input type="checkbox" name="remove[]" value="<?= $pid ?>" form="remove-form"
                                           id="remove-<?= $pid ?>" aria-label="Remove <?= e($item['product_title']) ?>">
                                </td>
                                <td>
                                    <a class="cart-product" href="<?= url('views/single_product.php?pro_id=' . $pid) ?>">
                                        <?php if (!empty($item['product_image'])): ?>
                                            <img src="<?= url('images/products/' . rawurlencode($item['product_image'])) ?>" alt="">
                                        <?php endif; ?>
                                        <span><?= e($item['product_title']) ?></span>
                                    </a>
                                </td>
                                <td class="cell-qty">
                                    <!-- Quantity input plus a hidden product id -->
                                    <label class="visually-hidden" for="qty-<?= $pid ?>">Quantity of <?= e($item['product_title']) ?></label>
                                    <input type="text" inputmode="numeric" name="qty[]" id="qty-<?= $pid ?>" form="update-form"
                                           value="<?= (int) $item['qty'] ?>" maxlength="2">
                                    <input type="hidden" name="update[]" value="<?= $pid ?>" form="update-form">
                                </td>
                                <td class="cell-price">GH₵ <?= number_format((float) $item['product_price'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="cart-subtotal">
                            <th scope="row" colspan="3">Subtotal</th>
                            <td class="cell-price">GH₵ <?= number_format($subtotal, 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <p class="field-error cart-error" id="cart-error" aria-live="polite"></p>
        </section>

        <div class="cart-actions">
            <div class="cart-actions-edit">
                <button type="submit" class="btn btn-ghost" form="remove-form">Remove from cart</button>
                <button type="submit" class="btn btn-ghost" form="update-form">Update quantity</button>
            </div>
            <div class="cart-actions-go">
                <a class="btn btn-ghost" href="<?= url('index.php') ?>">Continue shopping</a>
                <a class="btn" href="<?= url('views/checkout.php') ?>">Checkout</a>
            </div>
        </div>
    <?php endif; ?>
</main>

<script src="<?= url('js/validate.js') ?>" defer></script>
<?php require __DIR__ . '/layout/footer.php'; ?>