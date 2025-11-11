(function () {
  const EL_ID = "particles-js";

  const debounce = (fn, ms = 200) => {
    let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); };
  };

  function buildConfig() {
    const w = innerWidth || 1024;
    const touch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
    const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;

    const isTiny = w <= 420;
    const isMobile = w <= 768;

    const baseCount = 355;

    const count = reduced ? baseCount : (isTiny ? 60 : isMobile ? 120 : baseCount);
    const moveSpeed = reduced ? 0 : (isTiny ? 0.25 : isMobile ? 0.5 : 1);
    const sizeValue = isMobile ? 1.2 : 2;
    const valueArea = isTiny ? 400 : isMobile ? 600 : 789.15;

    return {
      particles: {
        number: { value: count, density: { enable: true, value_area: valueArea } },
        color: { value: "#ffffff" },
        shape: { type: "circle", stroke: { width: 0, color: "#000000" }, polygon: { nb_sides: 5 } },
        opacity: {
          value: 0.49,
          random: false,
          anim: { enable: !reduced, speed: 1, opacity_min: 0, sync: false }
        },
        size: {
          value: sizeValue,
          random: true,
          anim: { enable: !reduced, speed: 0.333, size_min: 0, sync: false }
        },
        line_linked: { enable: false, distance: 150, color: "#ffffff", opacity: 0.4, width: 1 },
        move: {
          enable: true,            // <-- не вимикаємо повністю
          speed: moveSpeed,        // <-- при reduced буде 0 (статична сітка)
          direction: "none",
          random: true,
          straight: false,
          out_mode: "out",
          bounce: false,
          attract: { enable: false, rotateX: 600, rotateY: 1200 }
        }
      },
      interactivity: {
        detect_on: "canvas",
        events: {
          onhover: { enable: !touch && !reduced, mode: "bubble" },
          onclick: { enable: !reduced && !isTiny, mode: "push" },
          resize: true
        },
        modes: {
          grab: { distance: 400, line_linked: { opacity: 1 } },
          bubble: { distance: 84, size: 1, duration: 3, opacity: 1, speed: 3 },
          repulse: { distance: 200, duration: 0.4 },
          push: { particles_nb: isMobile ? 2 : 4 },
          remove: { particles_nb: 2 }
        }
      },
      retina_detect: true         // <-- спростили: завжди true
    };
  }

  function destroyParticles() {
    const inst = window.pJSDom && window.pJSDom.find(d => d?.pJS?.canvas?.el?.id === EL_ID);
    if (inst && inst.pJS?.fn?.vendors?.destroypJS) {
      inst.pJS.fn.vendors.destroypJS();
      window.pJSDom = (window.pJSDom || []).filter(d => d !== inst);
    }
  }

  function initParticles() {
    if (!document.getElementById(EL_ID)) return;
    if (typeof window.particlesJS !== 'function') return; // бібліотека ще не завантажена
    destroyParticles();
    particlesJS(EL_ID, buildConfig());
  }

  function setRunning(run) {
    const inst = window.pJSDom && window.pJSDom.find(d => d?.pJS?.canvas?.el?.id === EL_ID);
    if (!inst?.pJS) return;
    inst.pJS.particles.move.enable = run;
    inst.pJS.fn?.particlesRefresh && inst.pJS.fn.particlesRefresh();
  }

  document.addEventListener('visibilitychange', () => setRunning(!document.hidden));

  // ініт тільки коли DOM готовий, а також якщо скрипт particles.js підключився пізніше
  const boot = () => initParticles();
  document.readyState === 'loading'
    ? document.addEventListener('DOMContentLoaded', boot, { once: true })
    : boot();

  // якщо particles.js підвантажується асинхронно після цього блоку
  window.addEventListener('load', initParticles, { once: true });

  addEventListener('resize', debounce(initParticles, 250), { passive: true });
  addEventListener('orientationchange', debounce(initParticles, 250), { passive: true });
})();