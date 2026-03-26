const revealItems = document.querySelectorAll('[data-reveal]');

if (revealItems.length > 0) {
  if (!('IntersectionObserver' in window)) {
    revealItems.forEach((item) => item.classList.add('is-visible'));
  } else {
    const revealObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) {
            return;
          }

          entry.target.classList.add('is-visible');
          revealObserver.unobserve(entry.target);
        });
      },
      {
        threshold: 0.12,
        rootMargin: '0px 0px -8% 0px',
      }
    );

    revealItems.forEach((item) => revealObserver.observe(item));
  }
}

/* Smooth parallax-like float for hero image */
const heroMedia = document.querySelector('.landing-hero__media');
if (heroMedia && window.matchMedia('(prefers-reduced-motion: no-preference)').matches) {
  let ticking = false;
  window.addEventListener(
    'scroll',
    () => {
      if (!ticking) {
        requestAnimationFrame(() => {
          const rect = heroMedia.getBoundingClientRect();
          const center = rect.top + rect.height / 2;
          const viewCenter = window.innerHeight / 2;
          const offset = (center - viewCenter) * 0.03;
          heroMedia.style.transform = `translateY(${offset}px)`;
          ticking = false;
        });
        ticking = true;
      }
    },
    { passive: true }
  );
}
