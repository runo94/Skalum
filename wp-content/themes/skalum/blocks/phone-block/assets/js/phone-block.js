const cvs = document.getElementById("pushes");
const ctx = cvs.getContext("2d");

// ---------- CONFIG ----------
const BASE =
  (window.SkalumPhoneBlock && window.SkalumPhoneBlock.imgBase) ||
  "/wp-content/themes/skalum/blocks/phone-block/assets/images/";

const ASSETS = {
  bg: BASE + "iphone.png",
  cards: ["push_1.png", "push_2.png", "push_3.png", "push_4.png"].map(f => BASE + f),
};

const LAYOUT = { stackX: 0.075, stackY: 0.52, cardW: 0.85, gap: 10, rows: 3 };
const TIMING = {
  arrivalEveryMs: 3600,
  pushPxPerMs: 0.12,
  damping: 0.095,       // чим менше — тим сильніше гасіння
  settlePx: 0.9,
  growFrom: 0.9,
  growOvershoot: 0.16,
  growSpeed: 0.003,
  constraintsIters: 3,
  TICK_MS: 1500,         // ⏸ тривалість тік-паузи
};

// ---------- DPR ----------
function fitDPR() {
  const dpr = Math.max(1, window.devicePixelRatio || 1);
  const cssW = parseInt(getComputedStyle(cvs).width, 10);
  const cssH = parseInt(getComputedStyle(cvs).height, 10);
  cvs.width = Math.round(cssW * dpr);
  cvs.height = Math.round(cssH * dpr);
  ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
}
fitDPR();
addEventListener("resize", fitDPR);

// ---------- LOAD ----------
const load = (src) => new Promise((resolve) => {
  const img = new Image();
  img.onload  = () => resolve(img);
  img.onerror = () => resolve(null);
  img.src = src;
});

(async function start() {
  const bg = await load(ASSETS.bg);
  const imgs = (await Promise.all(ASSETS.cards.map(load))).filter(Boolean);
  if (!bg || imgs.length === 0) return;

  const nW = imgs[0].naturalWidth, nH = imgs[0].naturalHeight;

  function geo() {
    const W = parseInt(getComputedStyle(cvs).width, 10);
    const H = parseInt(getComputedStyle(cvs).height, 10);
    const cardW = Math.round(W * LAYOUT.cardW);
    const scale = cardW / nW;
    const cardH = Math.round(nH * scale);
    const x = Math.round(W * LAYOUT.stackX);
    const yTop = Math.round(H * LAYOUT.stackY);
    const screenBottom = yTop + (cardH + LAYOUT.gap) * LAYOUT.rows - LAYOUT.gap;
    return { W, H, cardW, cardH, x, yTop, screenBottom };
  }
  let G = geo();
  addEventListener("resize", () => { fitDPR(); G = geo(); });

  // ---------- HELPERS ----------
  const slotY = (i) => G.yTop + i * (G.cardH + LAYOUT.gap);

  // ---------- STATE ----------
  let uid = 1;
  // карта: {id, img, y, vy, s, a, __growing?}
  let cards = imgs.slice(0, LAYOUT.rows).map((img, i) => ({
    id: uid++, img, y: slotY(i), vy: 0, s: 1, a: 1
  }));
  let nextIdx = LAYOUT.rows % imgs.length;

  // послідовність + «тік»-пауза через ідентифікатори
  let seqActive = false;
  let lastPushAt = performance.now() - TIMING.arrivalEveryMs + 500;
  let pauseUntil = 0;

  // хто востаннє тригерив тік на слотах 1 і 2
  const tickGate = { slot1Id: null, slot2Id: null };

  function resetTickGate() { tickGate.slot1Id = tickGate.slot2Id = null; }
  function pause(ms, now) { pauseUntil = Math.max(pauseUntil, now + ms); }

  function pushNew() {
    if (seqActive || cards.length === 0) return;
    seqActive = true;
    resetTickGate();
    cards[0].vy += TIMING.pushPxPerMs * 16; // стартовий імпульс
  }

  // вставка нового зверху, коли перша пройшла нижче слота 1
  function maybeGrowNewTop() {
    if (!seqActive) return;
    if (cards[0].y >= slotY(1) && !cards.some(c => c.__growing)) {
      const img = imgs[nextIdx++ % imgs.length];
      cards.unshift({
        id: uid++,
        img, y: slotY(0), vy: 0,
        s: TIMING.growFrom, a: 0.0, __growing: true
      });
      // новий top з новим id → дозволяємо знову тік на слоті-1
      tickGate.slot1Id = null;
    }
  }

  // апдейт росту новачка (з overshoot)
  function updateGrow(dt) {
    const top = cards[0];
    if (!top || !top.__growing) return;
    const target = (top.s < 1 + TIMING.growOvershoot) ? (1 + TIMING.growOvershoot) : 1;
    top.s += (target - top.s) * (TIMING.growSpeed * dt);
    top.a += (1 - top.a) * (TIMING.growSpeed * dt * 0.9);
    if (Math.abs(top.s - 1) < 0.01) { top.s = 1; top.a = 1; top.__growing = false; }
  }

  // обмеження без перекриття
  function solveConstraints() {
    for (let iter = 0; iter < TIMING.constraintsIters; iter++) {
      for (let i = 0; i < cards.length - 1; i++) {
        const a = cards[i], b = cards[i + 1];
        const desired = G.cardH + LAYOUT.gap;
        const dist = b.y - a.y;
        const pen = desired - dist;
        if (pen > 0) {
          const half = pen * 0.5;
          a.y -= half; b.y += half;
          b.vy += a.vy * 0.35;
        }
      }
    }
  }

  // видалення лише коли картка повністю нижче екрану
  function cullOut() {
    const rowsOK = cards.slice(0, LAYOUT.rows).every((c, i) =>
      Math.abs(c.y - slotY(i)) < G.cardH * 0.08 && Math.abs(c.vy) < 0.04
    );
    if (!rowsOK) return;

    let removed = false;
    cards = cards.filter((c, idx) => {
      const out = c.y >= G.screenBottom;
      if (out && !removed && idx >= LAYOUT.rows) { removed = true; return false; }
      return true;
    });

    if (!removed && cards.length <= LAYOUT.rows) {
      const allSettled = cards.slice(0, LAYOUT.rows).every((c, i) =>
        Math.abs(c.y - slotY(i)) < TIMING.settlePx && Math.abs(c.vy) < 0.02 && !c.__growing
      );
      if (allSettled) { seqActive = false; resetTickGate(); }
    }
  }

  // рендер із примусовим alpha=1 для картки, найближчої до слота-0
  function render() {
    ctx.clearRect(0, 0, cvs.width, cvs.height);
    ctx.drawImage(bg, 0, 0, bg.naturalWidth, bg.naturalHeight, 0, 0, G.W, G.H);

    let primary = null, best = Infinity;
    for (const c of cards) {
      const d = Math.abs(c.y - slotY(0));
      if (d < best) { best = d; primary = c; }
    }

    const order = [...cards].sort((a, b) => a.y - b.y);
    for (const c of order) {
      const dw = G.cardW * c.s;
      const dh = G.cardH * c.s;
      const dx = G.x + (G.cardW - dw) / 2;
      const dy = Math.round(c.y) + (G.cardH - dh) / 2;
      ctx.globalAlpha = (c === primary) ? 1 : (c.a * 0.92);
      ctx.drawImage(c.img, 0, 0, c.img.naturalWidth, c.img.naturalHeight, dx, dy, dw, dh);
    }
    ctx.globalAlpha = 1;

    // нижня тінь
    const fadeH = G.H * 0.5;
    const grad = ctx.createLinearGradient(0, G.H - fadeH, 0, G.H);
    grad.addColorStop(0, "rgba(0,0,0,0)");
    grad.addColorStop(1, "rgba(0,0,0,0.9)");
    ctx.fillStyle = grad;
    ctx.fillRect(0, G.H - fadeH, G.W, fadeH);
  }

  // ---------- LOOP ----------
  let prev = performance.now();
  function tick(now) {
    const dt = Math.min(50, now - prev);
    prev = now;

    // автозапуск нової послідовності
    if (!seqActive && now - lastPushAt >= TIMING.arrivalEveryMs) {
      lastPushAt = now;
      pushNew();
    }

    if (seqActive) {
      const paused = now < pauseUntil;

      if (!paused) {
        cards[0].vy += TIMING.pushPxPerMs * dt;
      } else {
        for (const c of cards) c.vy *= 0.85; // пригашуємо під час паузи
      }

      // інтеграція
      for (const c of cards) {
        c.y += c.vy;
        c.vy *= 1 - TIMING.damping;
      }

      // обмеження
      solveConstraints();

      // ================= TICK-PAUSES КОЖНОГО РАЗУ =================
      // тік на слоті-1: нова top-карта дійшла до рівня слота-1
      if (cards[0] && cards[0].id !== tickGate.slot1Id && cards[0].y >= slotY(1) - 2) {
        tickGate.slot1Id = cards[0].id;
        pause(TIMING.TICK_MS, now);
      }
      // тік на слоті-2: нова second-карта дійшла до рівня слота-2
      if (cards[1] && cards[1].id !== tickGate.slot2Id && cards[1].y >= slotY(2) - 2) {
        tickGate.slot2Id = cards[1].id;
        pause(TIMING.TICK_MS, now);
      }
      // ============================================================

      // grow зверху + видалення знизу
      maybeGrowNewTop();
      updateGrow(dt);
      cullOut();
    }

    render();
    requestAnimationFrame(tick);
  }
  requestAnimationFrame(tick);
})();



(function setupClock() {
  const timeEl = document.getElementById('phone_time');
  const dateEl = document.getElementById('phone_date');

  // Налаштування: 12/24-годинний формат і локаль для назв днів/місяців
  const USE_12H = true;               // true = 12-hour без am/pm; false = 24-hour
  const LOCALE  = 'en-GB';            // дає порядок "Tuesday, 23 June" (як у прикладі)

  const fmtWeekday = new Intl.DateTimeFormat(LOCALE, { weekday: 'long' });
  const fmtMonth   = new Intl.DateTimeFormat(LOCALE, { month: 'long' });

  function fmtTime(d) {
    const h24 = d.getHours();
    const h   = USE_12H ? ((h24 + 11) % 12) + 1 : h24; // 1..12 або 0..23
    const m   = d.getMinutes().toString().padStart(2, '0');
    return `${h}:${m}`;
  }

  function fmtDate(d) {
    const wd = fmtWeekday.format(d);    // Tuesday
    const day = d.getDate();            // 23
    const mo = fmtMonth.format(d);      // June
    return `${wd}, ${day} ${mo}`;       // "Tuesday, 23 June"
  }

  function update() {
    const now = new Date();
    timeEl.textContent = fmtTime(now);
    dateEl.textContent = fmtDate(now);
  }

  // оновлюємо зараз…
  update();

  // …і синхронізуємо інтервал до початку наступної хвилини
  function schedule() {
    const now = new Date();
    const msToNextMinute = (60 - now.getSeconds()) * 1000 - now.getMilliseconds();
    setTimeout(() => {
      update();
      setInterval(update, 60 * 1000);   // далі щохвилини
    }, Math.max(0, msToNextMinute));
  }
  schedule();

  // опційно: оновлювати при зміні видимості вкладки (повернувся — миттєве оновлення)
  document.addEventListener('visibilitychange', () => {
    if (!document.hidden) update();
  });
})();
