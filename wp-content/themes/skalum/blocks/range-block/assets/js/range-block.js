(function () {
  function clamp(n, min, max) {
    return Math.max(min, Math.min(max, n));
  }

  function safeParseJSON(str, fallback) {
    try {
      return JSON.parse(str);
    } catch (e) {
      return fallback;
    }
  }

  function initBlock(root) {
    const slider = root.querySelector('[data-range-slider]');
    if (!slider) return;

    const items = safeParseJSON(slider.getAttribute('data-range-items') || '[]', []);
    if (!Array.isArray(items) || items.length === 0) return;

    const dotsWrap = slider.querySelector('[data-range-dots]');
    const dots = Array.from(slider.querySelectorAll('[data-range-dot]'));
    const fill = slider.querySelector('[data-range-fill]');
    const live = slider.querySelector('[data-range-live]');

    const elTotal = root.querySelector('[data-range-total]');
    const elHours = root.querySelector('[data-range-hours]');
    const elPer = root.querySelector('[data-range-per]');

    let current = clamp(parseInt(slider.getAttribute('data-range-selected') || '0', 10) || 0, 0, items.length - 1);

    function percentForIndex(idx) {
      if (items.length <= 1) return 100;
      return ((idx) / (items.length - 1)) * 100;
    }

    function setActive(idx) {
      current = clamp(idx, 0, items.length - 1);

      // fill
      if (fill) fill.style.width = percentForIndex(current) + '%';

      // dots active
      dots.forEach((btn, i) => {
        const active = i === current;
        btn.classList.toggle('is-active', active);
        btn.setAttribute('aria-pressed', active ? 'true' : 'false');
      });

      // content
      const it = items[current] || {};
      if (elTotal) elTotal.textContent = it.total || '';
      if (elHours) elHours.textContent = it.hours || '';
      if (elPer) elPer.textContent = it.per || '';

      // a11y announce
      if (live) {
        live.textContent = `${it.hours || ''} per month, total ${it.total || ''}, ${it.per || ''} per hour`;
      }
    }

    // click dots
    dots.forEach((btn) => {
      btn.addEventListener('click', () => {
        const idx = parseInt(btn.getAttribute('data-index') || '0', 10) || 0;
        setActive(idx);
      });
    });

    // keyboard: left/right on dots container
    if (dotsWrap) {
      dotsWrap.addEventListener('keydown', (e) => {
        const key = e.key;
        if (key !== 'ArrowLeft' && key !== 'ArrowRight') return;

        e.preventDefault();
        const next = key === 'ArrowRight' ? current + 1 : current - 1;
        setActive(next);

        // move focus to active dot (nice UX)
        const btn = dots[current];
        if (btn && typeof btn.focus === 'function') btn.focus();
      });
    }

    // init
    setActive(current);
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-range-block]').forEach(initBlock);
  });
})();
