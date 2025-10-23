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
  console.log("work");

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
