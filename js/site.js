// Progressive enhancements for the header menus; the menus also work without JavaScript.
document.addEventListener('DOMContentLoaded', () => {
    const drops = document.querySelectorAll('.site-header details');

    drops.forEach((drop) => {
        drop.addEventListener('toggle', () => {
            if (!drop.open) return;
            // Only one menu open at a time
            drops.forEach((other) => { if (other !== drop) other.open = false; });
            // Put the cursor straight into the search box
            const input = drop.querySelector('input[type="search"]');
            if (input) input.focus();
        });
    });

    // Close menus when clicking elsewhere, or pressing Escape
    document.addEventListener('click', (e) => {
        drops.forEach((drop) => { if (drop.open && !drop.contains(e.target)) drop.open = false; });
    });
    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        drops.forEach((drop) => {
            if (drop.open) { drop.open = false; drop.querySelector('summary').focus(); }
        });
    });
});