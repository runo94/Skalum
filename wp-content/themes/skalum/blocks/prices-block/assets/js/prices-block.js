// assets/js/prices-block.js
(() => {
  const qs  = (s, c = document) => c.querySelector(s);
  const qsa = (s, c = document) => Array.from(c.querySelectorAll(s));

  function parsePlansJSON(root) {
    try { return JSON.parse(root.getAttribute('data-plans-json') || '[]'); }
    catch { return []; }
  }

  function activate(btn, group) {
    qsa('.plan-card', group).forEach(b => {
      b.classList.remove('is-active');
      b.setAttribute('aria-pressed', 'false');
    });
    btn.classList.add('is-active');
    btn.setAttribute('aria-pressed', 'true');
  }

  function setSelected(root, planId) {
    // 1) записати на CTA
    const cta = qs('[data-choose-plan]', root);
    if (cta) cta.dataset.plan = planId;

    // 2) оновити hidden у формі
    const inputSel = root.getAttribute('data-target-input') || '#selected-plan-input';
    const hidden = document.querySelector(inputSel);
    if (hidden) hidden.value = planId;
  }

  function renderFeatures(root, plan) {
    const list = qs('[data-features]', root);
    if (!list || !plan) return;

    list.innerHTML = '';

   

    // фічі
    (plan.features || []).forEach(f => {
      const li = document.createElement('li');
      li.className = 'features-list__item';

      const iconWrap = document.createElement('span');
      iconWrap.className = 'feat-icon';

      const url = f && typeof f.icon === 'string' ? f.icon : '';
      if (url) {
        const img = document.createElement('img');
        img.src = url;
        img.alt = '';
        iconWrap.appendChild(img);
      } else {
        iconWrap.textContent = '✓';
      }

      const text = document.createElement('span');
      text.className = 'feat-text';
      text.textContent = f && f.label ? String(f.label) : '';

      li.appendChild(iconWrap);
      li.appendChild(text);
      list.appendChild(li);
    });
  }

  // ---- Mobile accordion relocation: move right panel under active plan
  function relocatePanel(root) {
    const mql   = window.matchMedia('(max-width: 1023.98px)');
    const left  = qs('[data-plans]', root);
    const right = qs('.prices-block__right', root);
    if (!left || !right) return () => {};

    // Placeholder to restore position on desktop
    if (!right.__placeholder) {
      right.__placeholder = document.createComment('prices-panel-placeholder');
      right.parentNode.insertBefore(right.__placeholder, right.nextSibling);
    }

    const place = () => {
      const active = qs('.plan-card.is-active', left) || qs('.plan-card', left);
      if (mql.matches) {
        if (active && right.previousElementSibling !== active) {
          active.insertAdjacentElement('afterend', right);
        }
        right.classList.add('is-accordion');
      } else {
        if (right.__placeholder?.parentNode) {
          right.__placeholder.parentNode.insertBefore(right, right.__placeholder);
        }
        right.classList.remove('is-accordion');
      }
    };

    place();
    if (!right.__mqlBound) {
      mql.addEventListener('change', place);
      right.__mqlBound = true;
    }
    return place;
  }

  function initOne(root) {
    const data = parsePlansJSON(root);
    if (!data.length) return;

    const left = qs('[data-plans]', root);
    if (!left) return;

    // початковий активний
    const initialBtn = qs('.plan-card.is-active', left) || qs('.plan-card', left);
    const initialId  = initialBtn ? initialBtn.dataset.plan : data[0].id;
    const initial    = data.find(p => p.id === initialId) || data[0];

    if (initialBtn) activate(initialBtn, left);
    renderFeatures(root, initial);
    setSelected(root, initial.id);

    // мобільне розміщення панелі під активною
    const rePlace = relocatePanel(root);

    // перемикання планів
    left.addEventListener('click', e => {
      const btn = e.target.closest('.plan-card');
      if (!btn) return;

      const planId = btn.dataset.plan;
      const plan   = data.find(p => p.id === planId);
      if (!plan) return;

      activate(btn, left);
      renderFeatures(root, plan);
      setSelected(root, plan.id);
      rePlace();
    });

    // підстраховка на клік по CTA (наприклад, якщо відкриває модалку)
    const cta = qs('[data-choose-plan]', root);
    if (cta) {
      cta.addEventListener('click', () => {
        const current = qs('.plan-card.is-active', left);
        if (current) setSelected(root, current.dataset.plan);
      });
    }
  }

  function init() {
    qsa('[data-prices]').forEach(initOne);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
