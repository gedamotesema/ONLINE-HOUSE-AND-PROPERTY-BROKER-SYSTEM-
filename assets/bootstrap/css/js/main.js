// assets/js/main.js

// Example: Client-side validation for forms (add more as needed)
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus for modals
    var modalEls = document.querySelectorAll('.modal');
    modalEls.forEach(function(modal) {
        modal.addEventListener('shown.bs.modal', function () {
            const input = modal.querySelector('input[autofocus]');
            if (input) input.focus();
        });
    });

    // Example: Validate registration form before submit
    var regForm = document.querySelector('form[action="register.php"]');
    if (regForm) {
        regForm.addEventListener('submit', function(e) {
            var username = regForm.username.value.trim();
            var email = regForm.email.value.trim();
            var password = regForm.password.value;
            if (username.length < 3) {
                alert("Username too short.");
                regForm.username.focus();
                e.preventDefault();
                return false;
            }
            if (!/^[a-zA-Z0-9_]{3,20}$/.test(username)) {
                alert("Username must be only letters, numbers, or _");
                regForm.username.focus();
                e.preventDefault();
                return false;
            }
            if (password.length < 6) {
                alert("Password must be at least 6 characters.");
                regForm.password.focus();
                e.preventDefault();
                return false;
            }
        });
    }

    // Example: Bootstrap dropdowns
    document.querySelectorAll('.dropdown-toggle').forEach(function(el) {
        el.addEventListener('click', function(e) {
            new bootstrap.Dropdown(el).toggle();
        });
    });

    // Example: Responsive menu (if you implement hamburger toggler)
    var navToggler = document.querySelector('.navbar-toggler');
    var navCollapse = document.querySelector('#navbarNav');
    if (navToggler && navCollapse) {
        navToggler.addEventListener('click', function() {
            navCollapse.classList.toggle('show');
        });
    }
});

// You can add more features: AJAX for search/filter, modals, etc.
