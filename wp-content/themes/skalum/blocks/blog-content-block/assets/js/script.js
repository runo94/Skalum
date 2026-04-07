(function () {
  const SELECTOR_BLOCK = "[data-blog-content-block]";
  const SELECTOR_CONTENT = "[data-blog-content-block-content]";
  const SELECTOR_LIST = "[data-blog-content-block-list]";
  const ACTIVE_CLASS = "is-active";

  function slugify(text) {
    return text
      .toString()
      .trim()
      .toLowerCase()
      .replace(/<\/?[^>]+(>|$)/g, "")
      .replace(/&nbsp;/g, " ")
      .replace(/[^\p{L}\p{N}\s-]/gu, "")
      .replace(/\s+/g, "-")
      .replace(/-+/g, "-")
      .replace(/^-|-$/g, "");
  }

  function ensureUniqueId(baseId, usedIds) {
    let id = baseId || "section";
    let counter = 2;

    while (usedIds.has(id) || document.getElementById(id)) {
      id = `${baseId}-${counter}`;
      counter++;
    }

    usedIds.add(id);
    return id;
  }

  function getHeadings(content) {
    let headings = [...content.querySelectorAll("h2")].filter((heading) => {
      return heading.textContent.trim().length;
    });

    if (!headings.length) {
      headings = [...content.querySelectorAll("h3")].filter((heading) => {
        return heading.textContent.trim().length;
      });
    }

    return headings;
  }

  function setActiveItem(block, id) {
    const items = block.querySelectorAll(".blog-content-block__toc-item");
    const links = block.querySelectorAll(".blog-content-block__toc-link");

    items.forEach((item) => item.classList.remove(ACTIVE_CLASS));

    links.forEach((link) => {
      if (link.getAttribute("href") === `#${id}`) {
        const item = link.closest(".blog-content-block__toc-item");
        if (item) {
          item.classList.add(ACTIVE_CLASS);
        }
      }
    });
  }

  function setupActiveState(block, headings) {
    if (!headings.length) return;

    let currentActiveId = headings[0].id;

    const updateActiveOnScroll = () => {
      const offset = window.innerHeight * 0.28;
      let activeHeading = headings[0];

      headings.forEach((heading) => {
        const rect = heading.getBoundingClientRect();
        if (rect.top <= offset) {
          activeHeading = heading;
        }
      });

      if (activeHeading && activeHeading.id !== currentActiveId) {
        currentActiveId = activeHeading.id;
        setActiveItem(block, currentActiveId);
      }
    };

    setActiveItem(block, currentActiveId);
    updateActiveOnScroll();

    let ticking = false;

    const onScroll = () => {
      if (!ticking) {
        window.requestAnimationFrame(() => {
          updateActiveOnScroll();
          ticking = false;
        });
        ticking = true;
      }
    };

    window.addEventListener("scroll", onScroll, { passive: true });
    window.addEventListener("resize", onScroll);
  }

  function setupSmoothScroll(block) {
    const links = block.querySelectorAll(".blog-content-block__toc-link");

    links.forEach((link) => {
      link.addEventListener("click", (event) => {
        const href = link.getAttribute("href");
        if (!href || !href.startsWith("#")) return;

        const target = document.querySelector(href);
        if (!target) return;

        event.preventDefault();

        const targetTop = target.getBoundingClientRect().top + window.pageYOffset;
        const headerOffset = 120;
        const finalTop = targetTop - headerOffset;

        window.history.pushState(null, "", href);

        window.scrollTo({
          top: finalTop,
          behavior: "smooth",
        });

        setActiveItem(block, target.id);
      });
    });
  }

  function buildToc(block) {
    const content = block.querySelector(SELECTOR_CONTENT);
    const list = block.querySelector(SELECTOR_LIST);

    if (!content || !list) return;

    const headings = getHeadings(content);

    list.innerHTML = "";

    if (!headings.length) {
      block.classList.add("blog-content-block--empty");
      return;
    }

    block.classList.remove("blog-content-block--empty");

    const usedIds = new Set();

    headings.forEach((heading, index) => {
      let headingId = heading.id ? heading.id.trim() : "";

      if (!headingId) {
        headingId = slugify(heading.textContent) || `section-${index + 1}`;
      }

      headingId = ensureUniqueId(headingId, usedIds);
      heading.id = headingId;

      const item = document.createElement("li");
      item.className = "blog-content-block__toc-item";

      const link = document.createElement("a");
      link.className = "blog-content-block__toc-link";
      link.href = `#${headingId}`;

      const indexEl = document.createElement("span");
      indexEl.className = "blog-content-block__toc-index";
      indexEl.textContent = `${index + 1}.`;

      const textEl = document.createElement("span");
      textEl.className = "blog-content-block__toc-text";
      textEl.textContent = heading.textContent.trim();

      link.appendChild(indexEl);
      link.appendChild(textEl);
      item.appendChild(link);
      list.appendChild(item);
    });

    setupSmoothScroll(block);
    setupActiveState(block, headings);
  }

  function initBlock(block) {
    buildToc(block);
  }

  function initAll() {
    document.querySelectorAll(SELECTOR_BLOCK).forEach(initBlock);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAll);
  } else {
    initAll();
  }

  if (window.acf) {
    window.acf.addAction("render_block_preview/type=blog-content-block", function ($block) {
      const block = $block[0];
      if (block) {
        initBlock(block);
      }
    });
  }
})();