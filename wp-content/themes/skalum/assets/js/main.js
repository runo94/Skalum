document.addEventListener("DOMContentLoaded", () => {
  console.log("Skalum theme loaded");
});

document.addEventListener("DOMContentLoaded", function () {
  const btn = document.getElementById("burgerBtn");
  const panel = document.getElementById("mobileNav");

  if (!btn || !panel) return;

  const open = () => {
    btn.classList.add("is-open");
    panel.classList.add("is-open");
    panel.hidden = false;
    btn.setAttribute("aria-expanded", "true");
    document.body.classList.add("nav-open");
  };
  const close = () => {
    btn.classList.remove("is-open");
    panel.classList.remove("is-open");
    btn.setAttribute("aria-expanded", "false");
    document.body.classList.remove("nav-open");

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

  const mq = window.matchMedia("(min-width: 881px)");
  mq.addEventListener("change", () => {
    if (mq.matches) close();
  });
});
