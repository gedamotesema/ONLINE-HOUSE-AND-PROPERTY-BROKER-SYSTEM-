document.addEventListener('DOMContentLoaded', () => {
    console.log('GNK housing Loaded');

    const alerts = document.querySelectorAll('.alert');
    if (alerts.length) {
        setTimeout(() => {
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000); 
    }

    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    const animateElements = document.querySelectorAll(
        '.property-card, .glass-panel:not(.nav-bar), .hero h1, .hero p, .footer-section'
    );

    animateElements.forEach(el => {
        el.classList.add('scroll-animate');
        observer.observe(el);
    });

    const inputs = document.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement?.classList.add('focused');
        });
        input.addEventListener('blur', () => {
            input.parentElement?.classList.remove('focused');
        });
    });

    const navToggle = document.querySelector('.nav-toggle');
    const navLinks = document.querySelector('.nav-links');
    const navLinksItems = document.querySelectorAll('.nav-links a');

    if (navToggle && navLinks) {
        navToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            const isOpen = navLinks.classList.toggle('active');

            document.body.classList.toggle('nav-open', isOpen);

            navToggle.setAttribute('aria-expanded', isOpen);

            const icon = navToggle.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-bars', !isOpen);
                icon.classList.toggle('fa-times', isOpen);
            }
        });

        navLinksItems.forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
                document.body.classList.remove('nav-open');
                navToggle.setAttribute('aria-expanded', 'false');

                const icon = navToggle.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        });

        document.addEventListener('click', e => {
            if (
                window.innerWidth <= 768 &&
                navLinks.classList.contains('active') &&
                !navLinks.contains(e.target) &&
                !navToggle.contains(e.target)
            ) {
                navLinks.classList.remove('active');
                document.body.classList.remove('nav-open');
                navToggle.setAttribute('aria-expanded', 'false');

                const icon = navToggle.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });
    }
});
