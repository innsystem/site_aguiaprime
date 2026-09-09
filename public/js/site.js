(() => {
    const header = document.querySelector('.site-header');
    const revealItems = document.querySelectorAll('.reveal');
    const parallaxHost = document.querySelector('[data-parallax]');
    const parallaxImg = parallaxHost?.querySelector('.hero__img');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const onScrollHeader = () => {
        if (!header) return;
        header.classList.toggle('is-scrolled', window.scrollY > 24);
    };

    onScrollHeader();
    window.addEventListener('scroll', onScrollHeader, { passive: true });

    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        io.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
        );
        revealItems.forEach((el) => io.observe(el));
    } else {
        revealItems.forEach((el) => el.classList.add('is-visible'));
    }

    if (!reduceMotion && parallaxImg) {
        const updateParallax = () => {
            const y = window.scrollY;
            if (y < window.innerHeight) {
                parallaxImg.style.transform = `translate3d(0, ${y * 0.18}px, 0) scale(1.05)`;
            }
        };
        updateParallax();
        window.addEventListener('scroll', updateParallax, { passive: true });
    }

    document.querySelectorAll('#navOffcanvas a[href^="#"]').forEach((link) => {
        link.addEventListener('click', () => {
            const offcanvasEl = document.getElementById('navOffcanvas');
            const instance = bootstrap.Offcanvas.getInstance(offcanvasEl);
            instance?.hide();
        });
    });
})();
