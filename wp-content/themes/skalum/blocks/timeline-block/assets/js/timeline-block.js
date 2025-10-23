(function () {
  const sections = document.querySelectorAll('.timeline-block');
  if (!sections.length) return;

  const clamp = (v, min, max) => Math.max(min, Math.min(max, v));

  sections.forEach(section => {
    const body = section.querySelector('.timeline-block__body');
    const spine = section.querySelector('.tl-spine');
    const lineBg = section.querySelector('.tl-line--bg');
    const lineProgress = section.querySelector('.tl-line--progress');
    const items = section.querySelectorAll('.tl-item');

    if (!body || !spine || !lineBg || !lineProgress || !items.length) return;

    const resize = () => {
      // тягнемо сіру лінію на всю висоту контенту
      const rect = body.getBoundingClientRect();
      const fullH = Math.max(body.scrollHeight, body.offsetHeight);
      lineBg.style.height = fullH + 'px';
    };

    const onScroll = () => {
      const bodyRect = body.getBoundingClientRect();
      const docY = window.scrollY || window.pageYOffset;
      const bodyTop = docY + bodyRect.top;
      const bodyBottom = bodyTop + body.scrollHeight;

      // точка до якої «росте» лінія: середина вікна
      const progressTarget = docY + (window.innerHeight * 0.5);
      const progressH = clamp(progressTarget - bodyTop, 0, bodyBottom - bodyTop);

      lineProgress.style.height = progressH + 'px';

      // підсвічування елементів
      items.forEach(item => {
        const itemTop = item.offsetTop; // відносно body
        if (itemTop <= progressH) {
          item.classList.add('is-in-view');
        } else {
          item.classList.remove('is-in-view');
        }
      });
    };

    // дебаунс не критичний, але хай буде плавно
    let raf = null;
    const schedule = () => {
      if (raf) return;
      raf = requestAnimationFrame(() => {
        raf = null;
        resize();
        onScroll();
      });
    };

    // init
    schedule();
    window.addEventListener('load', schedule, { passive: true });
    window.addEventListener('resize', schedule, { passive: true });
    window.addEventListener('scroll', onScroll, { passive: true });
  });
})();
