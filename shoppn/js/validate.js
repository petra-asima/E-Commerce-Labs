//js/validate.js — client-side validation for the register and login forms.

const patterns = {
    email:    /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
    phone:    /^[0-9\-\s]{6,14}$/,        // local number, without the country code
    password: /^(?=.*\d).{8,72}$/         // 8–72 chars, at least one digit
};

//Show (or clear, if message is'') the inline error for one field
function showError(input, errorId, message) {
    const slot = document.getElementById(errorId);
    if (slot) slot.textContent = message;
    input.classList.toggle('invalid', message !== '');
    input.setAttribute('aria-invalid', message ? 'true' : 'false');
}

//Run a list of checks: [input, errorId, passed?, message]
function runChecks(checks) {
    let firstBad = null;
    checks.forEach(([input, errorId, ok, message]) => {
        showError(input, errorId, ok ? '' : message);
        if (!ok && !firstBad) firstBad = input;
    });
    if (firstBad) firstBad.focus();
    return firstBad === null;
}

//Disable the button while the form submits, to stop double-clicks
function setLoading(form, text) {
    const btn = form.querySelector('button[type="submit"]');
    if (btn) { btn.disabled = true; btn.dataset.label = btn.textContent; btn.textContent = text; }
}

//Registration form 
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
                'Password must be 8–72 characters and include a number.'],
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

//Login form
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

//Re-enable buttons if the user comes back with the Back button
window.addEventListener('pageshow', () => {
    document.querySelectorAll('button[data-label]').forEach((btn) => {
        btn.disabled = false;
        btn.textContent = btn.dataset.label;
    });
});