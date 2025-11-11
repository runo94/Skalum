(function () {
  const EL_ID = "stars";

  const debounce = (fn, ms = 200) => {
    let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
  };

  function buildConfig() {
    const w = window.innerWidth || 1024;
    const dpr = window.devicePixelRatio || 1;
    const touch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const isTiny = w <= 420;
    const isMobile = w <= 768;

    // База
    const baseCount = 355;

    // Кількість частинок
    let count =
      reduced ? Math.round(baseCount * 0.15) :
      isTiny   ? 60 :
      isMobile ? 120 :
                 baseCount;

    // Швидкість
    let speed =
      reduced ? 0 :
      isTiny   ? 0.05 :
      isMobile ? 0.07 :
                 0.1;

    // Площа щільності
    let valueArea =
      isTiny   ? 400 :
      isMobile ? 600 :
                 789.15;

    // На мобілі відключаємо retina, щоб не множити навантаження
    const retinaDetect = !(isMobile || reduced) && dpr < 2.5;

    return {
      particles: {
        number: {
          value: count,
          density: { enable: true, value_area: valueArea }
        },
        color: { value: "#ffffff" },
        shape: {
          type: "star",
          stroke: { width: 0, color: "#000000" },
          polygon: { nb_sides: 5 },
          image: { src: "../images/star.svg", width: 100, height: 100 }
        },
        opacity: {
          value: 0.49,
          random: false,
          anim: {
            enable: !reduced,
            speed: 1,
            opacity_min: 0,
            sync: false
          }
        },
        size: {
          value: isMobile ? 0.8 : 1,
          random: true,
          anim: {
            enable: !reduced,
            speed: 0.333,
            size_min: 0,
            sync: false
          }
        },
        line_linked: { enable: false, distance: 150, color: "#ffffff", opacity: 0.4, width: 1 },
        move: {
          enable: !reduced,
          speed,
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
      retina_detect: retinaDetect
    };
  }

  function destroyParticles() {
    const inst = window.pJSDom && window.pJSDom[0];
    if (inst && inst.pJS && inst.pJS.fn && inst.pJS.fn.vendors && inst.pJS.fn.vendors.destroypJS) {
      inst.pJS.fn.vendors.destroypJS();
      window.pJSDom = [];
    }
  }

  function initParticles() {
    destroyParticles();
    particlesJS(EL_ID, buildConfig());
  }

  // Пауза/резюм при переключенні вкладки — зменшує витрати батареї/CPU
  function setRunning(run) {
    const inst = window.pJSDom && window.pJSDom[0];
    if (!inst || !inst.pJS) return;
    inst.pJS.particles.move.enable = run;
    // Перебудова, щоб застосувати зміну
    if (inst.pJS.fn && inst.pJS.fn.particlesRefresh) inst.pJS.fn.particlesRefresh();
  }

  document.addEventListener('visibilitychange', () => {
    if (document.hidden) setRunning(false); else setRunning(true);
  });

  // Ініт + адаптив на ресайз із дебаунсом
  window.addEventListener('resize', debounce(initParticles, 250), { passive: true });
  window.addEventListener('orientationchange', debounce(initParticles, 250), { passive: true });

  // Старт
  initParticles();
})();