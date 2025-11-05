// cases-autoplay.js (stable autoplay, no hover stop)
document.addEventListener('DOMContentLoaded', () => {
  const DURATION = 50000;          // 5s
  const BACKUP_MARGIN = 120;      // запас, якщо transitionend не спрацює

  document.querySelectorAll('.cases-block').forEach(block => {
    const slider = block.querySelector('.cases-block__slider');
    if (!slider) return;

    const slides = Array.from(slider.querySelectorAll('.case-slide'));
    if (!slides.length) return;

    const itemsInFirstSlide = slides[0].querySelectorAll('.case-item');
    const total = Math.min(slides.length, itemsInFirstSlide.length);
    if (!total) return;

    let index = 0;
    let timeoutId = null;
    let rafId = null;

    // активний елемент таймера/слухач — щоб коректно відписуватись
    let currentTimerFill = null;
    let onEndRef = null;

    const clearActiveTimerListener = () => {
      if (currentTimerFill && onEndRef) {
        currentTimerFill.removeEventListener('transitionend', onEndRef);
      }
      currentTimerFill = null;
      onEndRef = null;
    };

    const resetAllTimers = () => {
      slider.querySelectorAll('.case-item__timer-fill').forEach(f => {
        f.style.transition = 'none';
        f.style.width = '0%';
        // форсуємо reflow
        // eslint-disable-next-line no-unused-expressions
        f.offsetHeight;
        f.style.transition = '';
      });
    };

    const armTimerForIndex = i => {
      // знайти смужку в активному слайді
      const activeSlide = slides[i];
      const timerFill = activeSlide?.querySelector(
        `.case-item[data-index="${i}"] .case-item__timer-fill`
      );
      if (!timerFill) return;

      // відписуємо старий слухач, якщо був
      clearTimeout(timeoutId);
      clearActiveTimerListener();

      // скидаємо і стартуємо перехід
      timerFill.style.transition = 'none';
      timerFill.style.width = '0%';
      // eslint-disable-next-line no-unused-expressions
      timerFill.offsetHeight; // reflow
      timerFill.style.transition = `width ${DURATION}ms linear`;

      onEndRef = (e) => {
        // інколи браузер шле кілька transitionend (наприклад, якщо є інші властивості),
        // фільтруємо тільки width або без перевірки — обидва варіанти ок.
        // if (e.propertyName && e.propertyName !== 'width') return;
        next();
      };
      timerFill.addEventListener('transitionend', onEndRef);
      currentTimerFill = timerFill;

      // бекап якщо transitionend не прийде
      timeoutId = setTimeout(next, DURATION + BACKUP_MARGIN);

      // запуск анімації
      rafId && cancelAnimationFrame(rafId);
      rafId = requestAnimationFrame(() => {
        timerFill.style.width = '100%';
      });
    };

    const setActive = i => {
      index = i;

      slides.forEach((s, idx) => s.classList.toggle('active', idx === i));
      slider.querySelectorAll('.case-item').forEach(el => {
        el.classList.toggle('active', Number(el.dataset.index || -1) === i);
      });

      resetAllTimers();
      armTimerForIndex(i);
    };

    const next = () => setActive((index + 1) % total);

    // клік по пункту
    slider.querySelectorAll('.case-item').forEach(el => {
      el.addEventListener('click', () => {
        const i = Number(el.dataset.index || 0);
        setActive(i);
      });
    });

    // коли вкладка ховається — просто скидаємо таймаути/слухач і фіксуємо поточний стан;
    // при поверненні — запускаємо цикл заново від поточного індексу
    const stop = () => {
      clearTimeout(timeoutId);
      clearActiveTimerListener();
    };
    const start = () => {
      // перезапускаємо таймер з 0% для поточного індексу
      armTimerForIndex(index);
    };
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) stop();
      else start();
    });

    // init
    setActive(0);

    // повага до reduce-motion
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      stop();
      resetAllTimers();
    }
  });
});
