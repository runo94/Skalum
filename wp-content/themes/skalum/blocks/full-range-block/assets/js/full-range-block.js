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

  function isCustomItem(it) {
    if (!it) return false;
    if (it.custom === true) return true;
    const label = (it.label || it.hours || '').toString().toLowerCase();
    return label === 'custom';
  }

  function initBlock(root) {
    if (!root || root.nodeType !== 1) return;

    // prevent double init (front + editor, or repeated preview renders)
    if (root.getAttribute('data-range-initialized') === '1') return;
    root.setAttribute('data-range-initialized', '1');

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

    let current = clamp(
      parseInt(slider.getAttribute('data-range-selected') || '0', 10) || 0,
      0,
      items.length - 1
    );

    function percentForIndex(idx) {
      if (items.length <= 1) return 100;
      return (idx / (items.length - 1)) * 100;
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
        // helpful for SR: indicate current
        btn.setAttribute('aria-current', active ? 'true' : 'false');
      });

      const it = items[current] || {};
      const custom = isCustomItem(it);

      // content
      if (elTotal) elTotal.textContent = (it.total || '');
      if (elHours) elHours.textContent = (it.hours || it.label || '');
      if (elPer) elPer.textContent = (it.per || '');

      // a11y announce
      if (live) {
        if (custom) {
          live.textContent = 'Custom plan selected';
        } else {
          live.textContent = `${it.hours || ''} per month, total ${it.total || ''}, ${it.per || ''} per hour`;
        }
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

        const btn = dots[current];
        if (btn && typeof btn.focus === 'function') btn.focus();
      });
    }

    // init
    setActive(current);
  }

  function initAll(scope) {
    const root = scope || document;
    root.querySelectorAll('[data-range-block]').forEach(initBlock);
  }

  // Frontend
  document.addEventListener('DOMContentLoaded', () => initAll(document));

  // Gutenberg / ACF preview support
  if (window.acf && typeof window.acf.addAction === 'function') {
    window.acf.addAction('render_block_preview/type=skalum-full-range-block', function ($el) {
      // $el is jQuery element
      const node = $el && $el[0] ? $el[0] : null;
      if (node) initAll(node);
    });
  }
})();
