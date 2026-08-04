document.addEventListener("DOMContentLoaded", () => {
  console.log("Skalum theme loaded");
});

document.addEventListener("DOMContentLoaded", function () {
  const btn = document.getElementById("burgerBtn");
  const panel = document.getElementById("mobileNav");

  if (!btn || !panel) return;

  // Скрол сторінки під меню: overflow:hidden на body iOS Safari ігнорує,
  // тому фіксуємо body і запам'ятовуємо позицію, щоб повернути її при закритті.
  let scrollY = 0;
  const lockScroll = () => {
    scrollY = window.scrollY || window.pageYOffset || 0;
    document.body.style.top = `-${scrollY}px`;
    document.body.classList.add("nav-open");
  };
  const unlockScroll = () => {
    if (!document.body.classList.contains("nav-open")) return;
    document.body.classList.remove("nav-open");
    document.body.style.top = "";
    window.scrollTo(0, scrollY);
  };

  const open = () => {
    btn.classList.add("is-open");
    panel.classList.add("is-open");
    panel.hidden = false;
    btn.setAttribute("aria-expanded", "true");
    lockScroll();
  };
  const close = () => {
    btn.classList.remove("is-open");
    panel.classList.remove("is-open");
    btn.setAttribute("aria-expanded", "false");
    unlockScroll();

    setTimeout(() => {
      if (!panel.classList.contains("is-open")) panel.hidden = true;
    }, 250);
  };
  const toggle = () => (panel.classList.contains("is-open") ? close() : open());

  btn.addEventListener("click", toggle);

  panel.addEventListener("click", (e) => {
    const a = e.target.closest("a");
    if (a) close();
  });

  window.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && panel.classList.contains("is-open")) close();
  });

  // Перейшли на десктопну розкладку — панель більше не потрібна
  const mq = window.matchMedia("(min-width: 1025px)");
  mq.addEventListener("change", () => {
    if (mq.matches) close();
  });
});

/* ---------- Mega menu (desktop) ---------- */
document.addEventListener("DOMContentLoaded", function () {
  const nav = document.querySelector("[data-mega-nav]");
  if (!nav) return;

  const header = nav.closest(".site-header");
  const items = Array.from(nav.querySelectorAll("[data-mega-item]")).map(
    (el) => ({
      el,
      toggle: el.querySelector("[data-mega-toggle]"),
      panel: el.querySelector("[data-mega-panel]"),
    })
  );

  const desktop = window.matchMedia("(min-width: 1025px)");
  const hoverable = window.matchMedia("(hover: hover) and (pointer: fine)");
  let hoverTimer = null;

  const setState = (entry, open) => {
    entry.el.classList.toggle("is-open", open);
    if (entry.toggle) entry.toggle.setAttribute("aria-expanded", String(open));
    if (entry.panel) entry.panel.setAttribute("aria-hidden", String(!open));
  };

  const closeAll = (except) => {
    items.forEach((entry) => {
      if (entry !== except) setState(entry, false);
    });
    if (header) {
      header.classList.toggle("has-mega-open", Boolean(except));
    }
  };

  const open = (entry) => {
    closeAll(entry);
    setState(entry, true);
  };

  const close = (entry) => {
    setState(entry, false);
    if (header && !items.some((i) => i.el.classList.contains("is-open"))) {
      header.classList.remove("has-mega-open");
    }
  };

  items.forEach((entry) => {
    if (!entry.toggle || !entry.panel) return;

    entry.panel.setAttribute("aria-hidden", "true");

    entry.toggle.addEventListener("click", (e) => {
      e.preventDefault();
      entry.el.classList.contains("is-open") ? close(entry) : open(entry);
    });

    entry.toggle.addEventListener("keydown", (e) => {
      if (e.key !== "ArrowDown") return;
      e.preventDefault();
      open(entry);
      const first = entry.panel.querySelector("a");
      if (first) first.focus();
    });

    // Наведення — тільки для мишки на десктопі, з невеликою затримкою,
    // щоб панель не блимала при проході курсора крізь пункт.
    entry.el.addEventListener("pointerenter", (e) => {
      if (e.pointerType !== "mouse" || !desktop.matches || !hoverable.matches)
        return;
      clearTimeout(hoverTimer);
      open(entry);
    });

    entry.el.addEventListener("pointerleave", (e) => {
      if (e.pointerType !== "mouse" || !desktop.matches || !hoverable.matches)
        return;
      clearTimeout(hoverTimer);
      hoverTimer = setTimeout(() => close(entry), 180);
    });

    // Пішли табом за межі пункту — закриваємо
    entry.el.addEventListener("focusout", (e) => {
      if (!entry.el.contains(e.relatedTarget)) close(entry);
    });

    // Клік по посиланню в панелі
    entry.panel.addEventListener("click", (e) => {
      if (e.target.closest("a")) close(entry);
    });
  });

  document.addEventListener("click", (e) => {
    if (!nav.contains(e.target)) closeAll();
  });

  document.addEventListener("keydown", (e) => {
    if (e.key !== "Escape") return;
    const opened = items.find((entry) => entry.el.classList.contains("is-open"));
    if (!opened) return;
    close(opened);
    if (opened.toggle) opened.toggle.focus();
  });

  desktop.addEventListener("change", () => closeAll());
});

/* ---------- Mobile submenu accordion ---------- */
document.addEventListener("DOMContentLoaded", function () {
  const toggles = Array.from(
    document.querySelectorAll("[data-submenu-toggle]")
  );
  if (!toggles.length) return;

  const panelOf = (toggle) =>
    document.getElementById(toggle.getAttribute("aria-controls"));

  const collapse = (toggle) => {
    const panel = panelOf(toggle);
    if (!panel) return;
    toggle.setAttribute("aria-expanded", "false");
    panel.classList.remove("is-open");
    panel.style.maxHeight = "0px";
  };

  const expand = (toggle) => {
    const panel = panelOf(toggle);
    if (!panel) return;
    toggle.setAttribute("aria-expanded", "true");
    panel.classList.add("is-open");
    panel.style.maxHeight = panel.scrollHeight + "px";
  };

  toggles.forEach((toggle) => {
    collapse(toggle);

    toggle.addEventListener("click", () => {
      const isOpen = toggle.getAttribute("aria-expanded") === "true";
      toggles.forEach(collapse);
      if (!isOpen) expand(toggle);
    });
  });

  // Висота вмісту змінюється разом із шириною екрана
  window.addEventListener("resize", () => {
    toggles.forEach((toggle) => {
      if (toggle.getAttribute("aria-expanded") !== "true") return;
      const panel = panelOf(toggle);
      if (panel) panel.style.maxHeight = panel.scrollHeight + "px";
    });
  });

  // Закрили бургер — згортаємо все, щоб наступне відкриття було «з нуля»
  const burger = document.getElementById("burgerBtn");
  const mobileNav = document.getElementById("mobileNav");
  if (burger && mobileNav) {
    burger.addEventListener("click", () => {
      if (!mobileNav.classList.contains("is-open")) toggles.forEach(collapse);
    });
  }
});

document.addEventListener("DOMContentLoaded", () => {
  const items = document.querySelectorAll(".fade-in");

  if (!("IntersectionObserver" in window)) {
    // fallback: показати все
    items.forEach((el) => el.classList.add("is-visible"));
    return;
  }

  const observer = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          entry.target.classList.add("is-animating");
          entry.target.addEventListener(
            "transitionend",
            (ev) => {
              ev.currentTarget.classList.remove("is-animating"); // 🔥 stacking context зникає
            },
            { once: true }
          );
          obs.unobserve(entry.target); // анімуємо один раз
        }
      });
    },
    {
      root: null,
      rootMargin: "0px 0px -10% 0px", // трохи раніше
    }
  );

  items.forEach((el) => observer.observe(el));

  const loadMore = document.querySelector(".blog__load");
  if (!loadMore) return;

  loadMore.addEventListener("click", async () => {
    const grid = document.querySelector(".blog__grid");
    let page = +grid.dataset.page;

    const res = await fetch(
      `/wp-admin/admin-ajax.php?action=blog_more&page=${page}`
    );
    const html = await res.text();

    grid.insertAdjacentHTML("beforeend", html);
    grid.dataset.page = page + 1;
  });
});
