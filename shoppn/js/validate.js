// js/validate.js — client-side validation for the register and login forms.
// This is for user experience only: the PHP action re-checks everything.

const patterns = {
    email:    /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
    phone:    /^[0-9\-\s]{6,14}$/,        // local number, without the country code
    password: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9])(?!.*\s).{8,72}$/  // upper, lower, digit, special, no spaces, 8–72
};

// Show (or clear, if message is '') the inline error for one field
function showError(input, errorId, message) {
    const slot = document.getElementById(errorId);
    if (slot) slot.textContent = message;
    input.classList.toggle('invalid', message !== '');
    input.setAttribute('aria-invalid', message ? 'true' : 'false');
}

// Run a list of checks: [input, errorId, passed?, message]
function runChecks(checks) {
    let firstBad = null;
    checks.forEach(([input, errorId, ok, message]) => {
        showError(input, errorId, ok ? '' : message);
        if (!ok && !firstBad) firstBad = input;
    });
    if (firstBad) firstBad.focus();
    return firstBad === null;
}

// Disable the button while the form submits, to stop double-clicks
function setLoading(form, text) {
    const btn = form.querySelector('button[type="submit"]');
    if (btn) { btn.disabled = true; btn.dataset.label = btn.textContent; btn.textContent = text; }
}

// ── Registration form ──────────────────────────────────
const registerForm = document.getElementById('register-form');
if (registerForm) {
    // Swap the flag picture when a different country code is chosen
    const dialSelect = document.getElementById('dial_code');
    const flag = document.getElementById('dial-flag');
    if (dialSelect && flag) {
        dialSelect.addEventListener('change', () => {
            flag.hidden = false;
            flag.src = 'https://flagcdn.com/w40/' + dialSelect.value.toLowerCase() + '.png';
        });
    }

    registerForm.addEventListener('submit', function (e) {
        const f = registerForm.elements;
        const email = f.customer_email.value.trim();
        const phoneDigits = f.customer_contact.value.replace(/[\s\-]/g, '').replace(/^0+/, '');

        const valid = runChecks([
            [f.customer_name,    'name-error',    f.customer_name.value.trim().length >= 2,
                'Enter your full name (at least 2 characters).'],
            [f.customer_email,   'email-error',   patterns.email.test(email) && email.length <= 50,
                'Enter a valid email address (max 50 characters).'],
            [f.customer_pass,    'pass-error',    patterns.password.test(f.customer_pass.value),
                'Password must be 8–72 characters with an uppercase letter, a lowercase letter, a number and a special character (no spaces).'],
            [f.customer_country, 'country-error', f.customer_country.value !== '',
                'Choose your country.'],
            [f.customer_city,    'city-error',    f.customer_city.value.trim().length >= 2,
                'Enter your city.'],
            [f.customer_contact, 'contact-error',
                patterns.phone.test(f.customer_contact.value.trim()) && /^[0-9]{6,12}$/.test(phoneDigits),
                'Enter a valid phone number (digits only, without the country code).'],
        ]);

        if (!valid) { e.preventDefault(); return; }
        setLoading(registerForm, 'Creating account…');
    });
}

// ── Login form (Task 4) ────────────────────────────────
const loginForm = document.getElementById('login-form');
if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
        const f = loginForm.elements;

        const valid = runChecks([
            [f.customer_email, 'email-error', patterns.email.test(f.customer_email.value.trim()),
                'Enter a valid email address.'],
            [f.customer_pass,  'pass-error',  f.customer_pass.value.length > 0,
                'Enter your password.'],
        ]);

        if (!valid) { e.preventDefault(); return; }
        setLoading(loginForm, 'Logging in…');
    });
}

// Clear a field's error as soon as the user edits it
document.querySelectorAll('.field input, .field select').forEach((input) => {
    input.addEventListener('input', () => {
        const slot = input.closest('.field').querySelector('.field-error');
        if (slot) slot.textContent = '';
        input.classList.remove('invalid');
        input.removeAttribute('aria-invalid');
    });
});

// Re-enable buttons if the user comes back with the Back button
window.addEventListener('pageshow', () => {
    document.querySelectorAll('button[data-label]').forEach((btn) => {
        btn.disabled = false;
        btn.textContent = btn.dataset.label;
    });
});


// ── Brand form (Task 5) — same rule as add_brand_action.php ──
const namePattern = /^[A-Za-z0-9][A-Za-z0-9 &'().,\-]{1,99}$/;

const brandForm = document.getElementById('brand-form');
if (brandForm) {
    brandForm.addEventListener('submit', function (e) {
        const input = brandForm.elements.brand_name;
        const valid = runChecks([
            [input, 'brand-error', namePattern.test(input.value.trim()),
                "Brand name must be 2–100 characters: letters, numbers, spaces and & ' ( ) . , - only."],
        ]);
        if (!valid) { e.preventDefault(); return; }
        setLoading(brandForm, 'Saving…');
    });
}



// ── Category form (Task 7) — same rule as add_category_action.php ──
const categoryForm = document.getElementById('category-form');
if (categoryForm) {
    categoryForm.addEventListener('submit', function (e) {
        const input = categoryForm.elements.cat_name;
        const valid = runChecks([
            [input, 'category-error', namePattern.test(input.value.trim()),
                "Category name must be 2–100 characters: letters, numbers, spaces and & ' ( ) . , - only."],
        ]);
        if (!valid) { e.preventDefault(); return; }
        setLoading(categoryForm, 'Saving…');
    });
}


// ── Product form (Task 9) — same rules as add/update_product_action.php ──
const productForm = document.getElementById('product-form');
if (productForm) {
    productForm.addEventListener('submit', function (e) {
        const f = productForm.elements;
        const price = f.product_price.value.trim();
        const file = f.product_image.files[0];
        const imageRequired = productForm.dataset.imageRequired === 'yes';
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        const maxSize = 2 * 1024 * 1024;   // 2 MB

        // Image: required when adding; if chosen, must be an allowed type and size
        let imageOk = true;
        let imageMsg = '';
        if (!file && imageRequired) {
            imageOk = false; imageMsg = 'Choose a product image.';
        } else if (file && !allowedTypes.includes(file.type)) {
            imageOk = false; imageMsg = 'The image must be a JPG, PNG, GIF or WEBP file.';
        } else if (file && file.size > maxSize) {
            imageOk = false; imageMsg = 'The image must be 2 MB or smaller.';
        }

        const valid = runChecks([
            [f.product_title, 'title-error', f.product_title.value.trim().length >= 2,
                'Enter a title (2–200 characters).'],
            [f.product_cat, 'cat-error', f.product_cat.value !== '',
                'Choose a category.'],
            [f.product_brand, 'brand-error', f.product_brand.value !== '',
                'Choose a brand.'],
            [f.product_price, 'price-error', /^\d{1,7}(\.\d{1,2})?$/.test(price) && parseFloat(price) > 0,
                'Enter a price greater than 0, e.g. 120 or 120.50.'],
            [f.product_keywords, 'keywords-error', f.product_keywords.value.trim().length <= 100,
                'Keywords must be 100 characters or fewer.'],
            [f.product_desc, 'desc-error', f.product_desc.value.trim().length <= 500,
                'Description must be 500 characters or fewer.'],
            [f.product_image, 'image-error', imageOk, imageMsg],
        ]);

        if (!valid) { e.preventDefault(); return; }
        setLoading(productForm, 'Saving…');
    });
}