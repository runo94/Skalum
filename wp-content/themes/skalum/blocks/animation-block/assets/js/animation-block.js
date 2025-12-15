(() => {
  // ====== GAUGE + BARS ======

  if (!document.querySelector(".animation-block__inner .ssp-card")) return;

  document.querySelectorAll(".animation-block__inner").forEach((card) => {
    if (!card.querySelector(".gc-group")) return;
    const group = card.querySelector(".gc-group");
    const gauge = group.querySelector(".gc");
    const needle = gauge.querySelector(".gc-needle");
    const track = gauge.querySelector(".gc-track");
    const valueEl = gauge.querySelector(".gc-value");

    const css = getComputedStyle(gauge);
    const cx = parseFloat(css.getPropertyValue("--gc-cx")) || 110;
    const cy = parseFloat(css.getPropertyValue("--gc-cy")) || 125;
    const r = parseFloat(css.getPropertyValue("--gc-r")) || 102;
    const theta0 = parseFloat(css.getPropertyValue("--gc-theta0")) || 0;

    if (track) {
      track.setAttribute("cx", cx);
      track.setAttribute("cy", cy);
      track.setAttribute("r", r);
    }

    const setNeedle = (deg) => {
      const a = deg + theta0;
      needle.setAttribute(
        "transform",
        `translate(${cx} ${cy}) rotate(${a}) translate(0 ${-r})`
      );
    };

    // gauge TL
    gsap.set(gauge, { "--gc-start": "180deg", "--gc-open": "0deg" });
    const gaugeState = { angle: 0 };
    setNeedle(gaugeState.angle);
    const counter = { v: 27 },
      COUNTER_TO = 49;
    valueEl.textContent = `${counter.v}`;

    const tlGauge = gsap.timeline();
    tlGauge
      .to(
        gauge,
        { "--gc-open": "360deg", duration: 0.9, ease: "power3.out" },
        0
      )
      .to(
        gaugeState,
        {
          angle: 123,
          duration: 0.94,
          ease: "power3.out",
          onUpdate: () => setNeedle(gaugeState.angle),
        },
        0
      )
      .to(
        counter,
        {
          v: COUNTER_TO,
          duration: 0.9,
          ease: "power3.out",
          onUpdate: () => (valueEl.textContent = `${Math.round(counter.v)}`),
        },
        0
      );

    // bars TL
    const barsRoot = group.querySelector(".gc-bars");
    const tlBars = gsap.timeline();
    barsRoot.querySelectorAll(".gc-bar").forEach((bar) => {
      const base = bar.querySelector(".gc-bar-fill--base");
      const hov = bar.querySelector(".gc-bar-fill--hover");
      const from = +bar.dataset.from || 0;
      const to = +bar.dataset.to || 100;

      gsap.set(base, { height: from + "%" });
      gsap.set(hov, { height: from + "%", opacity: 0 });

      tlBars
        .to(base, { height: to + "%", duration: 0.9, ease: "power3.out" }, 0)
        .to(
          hov,
          { height: to + "%", opacity: 1, duration: 0.9, ease: "power3.out" },
          0
        );
    });

    // ====== UI TL ======
    const tlUI = gsap.timeline();

    // delta: 12 -> 49
    const deltaEl = card.querySelector(".ssp-delta-val");
    const delta = { v: 12 };
    deltaEl.textContent = delta.v;
    tlUI.to(
      delta,
      {
        v: 49,
        duration: 0.9,
        ease: "power3.out",
        onUpdate: () => (deltaEl.textContent = Math.round(delta.v)),
      },
      0
    );

    // dots: base -> hover
    card.querySelectorAll(".ssp-dot").forEach((dot) => {
      const b = dot.querySelector(".ssp-dot-base");
      const h = dot.querySelector(".ssp-dot-hover");
      gsap.set(h, { opacity: 0 });
      tlUI.to(h, { opacity: 1, duration: 0.9, ease: "power3.out" }, 0);
      tlUI.to(b, { opacity: 0, duration: 0.9, ease: "power3.out" }, 0);
    });

    // note: slow -> fast
    // const slow = card.querySelector(".ssp-note-slow");
    // const fast = card.querySelector(".ssp-note-fast");
    // gsap.set(fast, { opacity: 0 });
    // tlUI.to(slow, { opacity: 0, duration: 0.4, ease: "power2.out" }, 0.35);
    // tlUI.to(fast, { opacity: 1, duration: 0.4, ease: "power2.out" }, 0.35);

    // metrics numbers (28/30/34 -> 98/98/89)
    card.querySelectorAll(".ssp-metric").forEach((el) => {
      const from = +el.dataset.from || 0;
      const to = +el.dataset.to || 0;
      const obj = { v: from };
      el.textContent = from;
      tlUI.to(
        obj,
        {
          v: to,
          duration: 0.9,
          ease: "power3.out",
          onUpdate: () => (el.textContent = Math.round(obj.v)),
        },
        0
      );
    });

    // ====== MASTER TL ======
    const tlMaster = gsap.timeline({ paused: true });
    tlMaster.add(tlGauge, 0).add(tlBars, 0).add(tlUI, 0);

    card.addEventListener("mouseenter", () => tlMaster.play());
    card.addEventListener("mouseleave", () => tlMaster.reverse());
    card.addEventListener("click", () =>
      tlMaster.reversed() ? tlMaster.play() : tlMaster.reverse()
    );
  });
})();

(() => {
  if (!document.querySelector(".animation-block__inner .google_list_card"))
    return;
  
  const cnv = document.getElementById("c");
  const pen = cnv.getContext("2d");
  const dpr = window.devicePixelRatio || 1;

  if (!cnv) return;
  const block = cnv.closest(".animation-block__inner") || cnv;

  const BASE =
    (window.SkalumAnimatedBlock && window.SkalumAnimatedBlock.imgBase) ||
    "/wp-content/themes/skalum/blocks/animation-block/assets/images/";

  const imgA = new Image();
  const imgB = new Image();
  const imgC = new Image();
  imgA.src = BASE + "bg.png";
  imgB.src = BASE + "main_item.png";
  imgC.src = BASE + "group.png";

  const geom = {
    B: { x: 28, yRest: 260, yHover: 130 },
    C: { x: 28, yRest: 130, yHover: 210 },
  };

  let sX = 1,
    sY = 1;
  let t = 0;
  let goal = 0;

  const easeOut = (x) => 1 - Math.pow(1 - x, 1);

  block.addEventListener("mouseenter", () => (goal = 1));
  block.addEventListener("mouseleave", () => (goal = 0));
  window.addEventListener("resize", fitCanvas);

  function fitCanvas() {
    const cssW = cnv.clientWidth || cnv.width;
    const cssH = cnv.clientHeight || cnv.height;
    cnv.width = cssW * dpr;
    cnv.height = cssH * dpr;
    sX = cnv.width / imgA.naturalWidth;
    sY = cnv.height / imgA.naturalHeight;
  }

  function draw() {
    pen.clearRect(0, 0, cnv.width, cnv.height);

    pen.drawImage(
      imgA,
      0,
      0,
      imgA.naturalWidth,
      imgA.naturalHeight,
      0,
      0,
      cnv.width,
      cnv.height
    );

    t += (goal - t) * 0.12;
    const k = easeOut(t);

    const yB = geom.B.yRest + (geom.B.yHover - geom.B.yRest) * k;
    const yC = geom.C.yRest + (geom.C.yHover - geom.C.yRest) * k;

    pen.drawImage(
      imgB,
      geom.B.x * sX,
      yB * sY,
      imgB.naturalWidth * sX,
      imgB.naturalHeight * sY
    );

    pen.drawImage(
      imgC,
      geom.C.x * sX,
      yC * sY,
      imgC.naturalWidth * sX,
      imgC.naturalHeight * sY
    );

    requestAnimationFrame(draw);
  }

  let loaded = 0;
  [imgA, imgB, imgC].forEach(
    (im) =>
      (im.onload = () => {
        loaded++;
        if (loaded === 3) {
          fitCanvas();
          draw();
        }
      })
  );
})();

(() => {
  // ------------------ CONFIG ------------------

  const BASE =
    (window.SkalumAnimatedBlock && window.SkalumAnimatedBlock.imgBase) ||
    "/wp-content/themes/skalum/blocks/animation-block/assets/images/";
  const DATA = [
    { day: "Day 1", sales: 300, spend: 200, roi: 28 },
    { day: "Day 2", sales: 320, spend: 250, roi: 36 },
    { day: "Day 3", sales: 350, spend: 270, roi: 23 },
    { day: "Day 4", sales: 400, spend: 280, roi: 70 },
    { day: "Day 5", sales: 500, spend: 300, roi: 90 },
    { day: "Day 6", sales: 650, spend: 320, roi: 120 },
    { day: "Day 7", sales: 700, spend: 350, roi: 130 },
    { day: "Day 8", sales: 750, spend: 300, roi: 150 },
    { day: "Day 9", sales: 850, spend: 320, roi: 180 },
    { day: "Day 10", sales: 900, spend: 350, roi: 190 },
  ];

  class RoiCanvas {
    constructor(canvas, opts = {}) {
      this.cvs =
        typeof canvas === "string" ? document.querySelector(canvas) : canvas;
      this.ctx = this.cvs.getContext("2d");
      // layout
      this.CARD = { x: 110, y: 30, w: 290, h: 340, r: 8 };
      this.HEADER_H = 50;
      this.ROW_H = 40;
      this.RIBBON = {
        y: this.CARD.y + this.HEADER_H + this.ROW_H * 3.5 - this.ROW_H / 2,
        startX: 52,
        endX: 30,
        label: "skalum",
      };
      this.DURATION = 900;

      // state
      this.t = 0; // 0..1  (A->B)
      this.t0 = 0;
      this.dir = 0; // 1 forward, -1 back
      this.tStart = 0;
      this.raf = 0;

      this.block = this.cvs.closest(".animation-block__inner") || this.cvs;

      this._fitDPR();
      window.addEventListener("resize", () => {
        this._fitDPR();
        this.renderScene(this.t);
      });

      // hover → animate
      this.block.addEventListener("mouseenter", () => this.toB());
      this.block.addEventListener("mouseleave", () => this.toA());

      this.icons = {};
      this._loadIcons({
        shopify: BASE + "/shopy.svg", // шлях з кореня
        ads: BASE + "/google_ads.svg", // пробіл → %20
      }).then(() => this.renderScene(0));
      // first paint
      this.renderScene(0);
    }

    _loadIcons(map) {
      const tasks = Object.entries(map).map(
        ([key, src]) =>
          new Promise((res) => {
            const im = new Image();
            im.onload = () => {
              this.icons[key] = im;
              res();
            };
            im.src = src;
          })
      );
      return Promise.all(tasks);
    }

    // --------------- LOW-LEVEL UTILS ---------------

    _fitDPR() {
      const BASE_WIDTH = 420;
      const BASE_HEIGHT = 460;
      const dpr = Math.max(1, window.devicePixelRatio || 1);

      // фактична ширина блока, в якому лежить canvas
      const rect = this.cvs.getBoundingClientRect();
      const containerWidth = rect.width || BASE_WIDTH;

      // масштаб відносно базового макета
      // якщо не хочеш, щоб на десктопі канвас ставав більшим за макет —
      // використовуй Math.min(1, containerWidth / BASE_WIDTH)
      const scale = containerWidth / BASE_WIDTH;

      // задаємо реальні піксельні розміри canvas
      this.cvs.width = containerWidth * dpr;
      this.cvs.height = BASE_HEIGHT * scale * dpr;

      // щоб браузер висотою не розтягнув/стиснув div — явно ставимо CSS-висоту
      this.cvs.style.height = BASE_HEIGHT * scale + "px";

      // Нова система координат:
      // логічно ми як і раніше малюємо в 360×320,
      // але все це ще множиться на scale і dpr.
      this.ctx.setTransform(dpr * scale, 0, 0, dpr * scale, 0, 0);
    }

    ease(k) {
      return k < 0.5 ? 4 * k * k * k : 1 - Math.pow(-2 * k + 2, 3) / 2;
    }
    lerp(a, b, k) {
      return a + (b - a) * k;
    }
    roiColor(v) {
      const cl = Math.max(0, Math.min(130, v)) / 130;
      const r = Math.round(255 * (1 - cl));
      const g = Math.round(30 + 180 * cl);
      const b = Math.round(30 + 30 * cl);
      return `rgb(${r},${g},${b})`;
    }
    roundTopRectPath(x, y, w, h, r) {
      // тільки верхні кути заокруглені
      const rr = Math.min(r, w / 2);
      const ctx = this.ctx;
      ctx.beginPath();
      ctx.moveTo(x, y + h);
      ctx.lineTo(x, y + rr);
      ctx.quadraticCurveTo(x, y, x + rr, y);
      ctx.lineTo(x + w - rr, y);
      ctx.quadraticCurveTo(x + w, y, x + w, y + rr);
      ctx.lineTo(x + w, y + h);
      ctx.closePath();
    }
    roundRectPath(x, y, w, h, r) {
      const rr = Math.min(r, w / 2, h / 2);
      const ctx = this.ctx;
      ctx.beginPath();
      ctx.moveTo(x + rr, y);
      ctx.arcTo(x + w, y, x + w, y + h, rr);
      ctx.arcTo(x + w, y + h, x, y + h, rr);
      ctx.arcTo(x, y + h, x, y, rr);
      ctx.arcTo(x, y, x + w, y, rr);
      ctx.closePath();
    }

    // --------------- RENDER PARTS ---------------
    renderScene(tt) {
      const ctx = this.ctx;
      const k = this.ease(tt);

      // BG
      ctx.clearRect(0, 0, this.cvs.width, this.cvs.height);
      ctx.fillStyle = "transparent";
      ctx.fillRect(0, 0, this.cvs.width, this.cvs.height);

      // card
      this.renderCard(k);

      // badge (фон + текст)
      this.renderBadge(k);

      // stripe — суцільна поверх усього
      this.renderStripe(k);

      // крайова крапка
      ctx.beginPath();
      ctx.arc(this.CARD.x + this.CARD.w + 6, this.RIBBON.y, 4, 0, Math.PI * 2);
      ctx.fillStyle = "#ffa000";
      ctx.fill();
    }

    renderCard(k) {
      const ctx = this.ctx;
      const C = this.CARD;
      // оболонка картки
      ctx.save();
      this.roundRectPath(C.x, C.y, C.w, C.h, C.r);
      ctx.fillStyle = "rgba(16,24,32,.88)";
      ctx.fill();
      ctx.strokeStyle = "rgba(255,255,255,0.06)";
      ctx.stroke();
      ctx.clip();

      // таблиця всередині
      this.renderTable(k);

      ctx.restore();
    }

    renderTable(k) {
      const ctx = this.ctx;
      const C = this.CARD,
        H = this.HEADER_H,
        RH = this.ROW_H;

      // «скрол» на ~2.4 рядка вгору
      const scrollY = this.lerp(0, -RH * 3, k);

      // rows
      let ry = C.y + H + scrollY;
      ctx.font = "12px Inter, system-ui, -apple-system, Segoe UI, Roboto";
      DATA.forEach((r, i) => {
        ctx.fillStyle =
          i % 2 ? "rgba(255,255,255,0.02)" : "rgba(255,255,255,0.05)";
        ctx.fillRect(C.x, ry, C.w, RH);
        ctx.fillStyle = "rgba(255,255,255,.06)";
        ctx.fillRect(C.x, ry + RH - 1, C.w, 1);

        ctx.fillStyle = "rgba(233,237,241,.85)";
        ctx.fillText(r.day, C.x + 10, ry + RH / 2);
        ctx.fillText(r.sales, C.x + 176, ry + RH / 2);
        ctx.fillText(r.spend, C.x + 104, ry + RH / 2);

        ctx.textAlign = "right";
        ctx.fillStyle = this.roiColor(r.roi);
        ctx.fillText(r.roi + "%", C.x + C.w - 10, ry + RH / 2);
        ctx.textAlign = "left";

        // header
        ctx.fillStyle = "rgba(16,24,32,1";
        ctx.fillRect(C.x, C.y, C.w, H);

        // іконки (18×18) + підписи
        const icoY = C.y + 10; // верх іконки
        const txtY = C.y + H - 10; // підпис під іконкою

        if (this.icons.shopify)
          ctx.drawImage(this.icons.shopify, C.x + 105, icoY, 18, 18);
        if (this.icons.ads)
          ctx.drawImage(this.icons.ads, C.x + 178, icoY, 18, 18);

        ctx.font = "600 12px Inter, system-ui, -apple-system, Segoe UI, Roboto";
        ctx.textBaseline = "middle";
        ctx.fillStyle = "rgba(154,163,173,.75)";
        ctx.fillText("Sales", C.x + 100, txtY);
        ctx.fillText("Spend", C.x + 170, txtY);
        ctx.fillStyle = "rgba(233,237,241,.92)";
        ctx.fillText("ROI", C.x + C.w - 32, txtY);
        ry += RH;
      });

      // легкий red→green градієнт-оверлей
      const grd = ctx.createLinearGradient(C.x, C.y, C.x, C.y + C.h);
      grd.addColorStop(0, `rgba(255,0,0,${0.1 * k})`);
      grd.addColorStop(1, `rgba(0,200,0,${0.16 * k})`);
      ctx.fillStyle = grd;
      ctx.fillRect(C.x, C.y, C.w, C.h);
    }

    renderBadge(k) {
      const ctx = this.ctx;
      const x = this.lerp(this.RIBBON.startX, this.RIBBON.endX, k);
      const y = this.RIBBON.y;
      const padX = 10,
        h = 22,
        r = 6;

      // фон бейджа (скруглення лише зверху)
      this.roundTopRectPath(x, y - h / 1, this.badgeWidth(ctx), h, r);
      ctx.fillStyle = "#ffb300";
      ctx.fill();

      // текст поверх
      ctx.font = "12px Inter, system-ui, -apple-system, Segoe UI, Roboto";
      ctx.textBaseline = "middle";
      ctx.fillStyle = "#1b1b1b";
      ctx.fillText(this.RIBBON.label, x + padX, y - h / 2.2);
    }

    renderStripe(k) {
      const ctx = this.ctx;
      const x = this.lerp(this.RIBBON.startX, this.RIBBON.endX, k);
      const y = this.RIBBON.y;

      // суцільна горизонтальна лінія через весь канвас
      ctx.beginPath();
      ctx.moveTo(this.CARD.x, y + 1);
      ctx.lineTo(this.CARD.x + this.CARD.w + 6, y + 1);
      ctx.lineWidth = 2;
      ctx.strokeStyle = "#ffa000";
      ctx.stroke();

      // маленький "хвостик" від правого краю бейджа до картки — не обов'язковий,
      // бо смуга вже суцільна, але залишимо легкий акцент
      const bx = x + this.badgeWidth(ctx);
      ctx.beginPath();
      ctx.moveTo(bx - 58, y + 1);
      ctx.lineTo(this.CARD.x - 0, y + 1);
      ctx.lineWidth = 2;
      ctx.strokeStyle = "#ffa000";
      ctx.stroke();
    }

    badgeWidth(ctx) {
      ctx.save();
      ctx.font = "12px Inter, system-ui, -apple-system, Segoe UI, Roboto";
      const w = Math.ceil(ctx.measureText(this.RIBBON.label).width) + 20; // 2*padX
      ctx.restore();
      return w;
    }

    // --------------- ANIMATION CTRL ---------------
    toB() {
      this._play(1);
    }
    toA() {
      this._play(-1);
    }
    _play(dir) {
      this.dir = dir;
      this.t0 = this.t;
      this.tStart = performance.now();
      cancelAnimationFrame(this.raf);
      const loop = (now) => {
        const dt = Math.min(1, (now - this.tStart) / this.DURATION);
        this.t =
          dir === 1 ? Math.min(1, this.t0 + dt) : Math.max(0, this.t0 - dt);
        this.renderScene(this.t);
        if ((dir === 1 && this.t < 1) || (dir === -1 && this.t > 0))
          this.raf = requestAnimationFrame(loop);
      };
      this.raf = requestAnimationFrame(loop);
    }
  }

  // --------------- BOOT ---------------
  new RoiCanvas("#roi-canvas");
})();
