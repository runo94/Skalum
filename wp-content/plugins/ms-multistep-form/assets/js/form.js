(function($) {
  $(function() {
    const $forms = $('.ms-form');
    if (!$forms.length) return;

    $forms.each(function() {
      const $form = $(this);
      const $steps = $form.find('.ms-step');
      const $btnPrev = $form.find('.ms-btn-prev');
      const $btnNext = $form.find('.ms-btn-next');
      const $btnSubmit = $form.find('.ms-btn-submit');

      const $progress = $form.find('[data-ms-progress]');
      const $progressSteps = $progress.find('.ms-progress__step');
      const $progressFill = $progress.find('.ms-progress__bar-fill');

      const total = $steps.length;
      let current = 0;

      function showStep(idx) {
        $steps.hide().eq(idx).show();

        if (idx === 0) {
          $btnPrev.hide();
        } else {
          $btnPrev.show();
        }

        if (idx === total - 1) {
          $btnNext.hide();
          $btnSubmit.show();
        } else {
          $btnNext.show();
          $btnSubmit.hide();
        }

        updateProgress(idx);
      }

      function updateProgress(idx) {
        // Активний step в «точках»
        $progressSteps.removeClass('is-active is-complete');
        $progressSteps.each(function(i) {
          if (i < idx) {
            $(this).addClass('is-complete');
          } else if (i === idx) {
            $(this).addClass('is-active');
          }
        });

        // Заповнення лінії
        const percent = total > 1 ? ((idx) / (total - 1)) * 100 : 100;
        $progressFill.css('width', percent + '%');
      }

      function validateStep(idx) {
        let valid = true;
        const $step = $steps.eq(idx);
        $step.find('[required]').each(function() {
          const $field = $(this);
          if (!$field.val()) {
            valid = false;
            $field.addClass('ms-input--error');
          } else {
            $field.removeClass('ms-input--error');
          }
        });
        return valid;
      }

      $btnNext.on('click', function(e) {
        e.preventDefault();
        if (!validateStep(current)) return;
        if (current < total - 1) {
          current++;
          showStep(current);
        }
      });

      $btnPrev.on('click', function(e) {
        e.preventDefault();
        if (current > 0) {
          current--;
          showStep(current);
        }
      });

      // Клік по елементу прогрес-бару (опційно)
      $progressSteps.on('click', function() {
        const target = parseInt($(this).attr('data-step'), 10);
        if (Number.isNaN(target)) return;

        // Можна дозволити «стрибати» тільки назад
        if (target <= current) {
          current = target;
          showStep(current);
        }
      });

      // Активний стан опцій (radio/checkbox)
      $form.on('change', '.ms-option__input', function() {
        const $input = $(this);
        const type = $input.attr('type');
        const $wrapper = $input.closest('.ms-options');

        if (type === 'radio') {
          $wrapper.find('.ms-option').removeClass('is-active');
          $input.closest('.ms-option').addClass('is-active');
        } else if (type === 'checkbox') {
          $input.closest('.ms-option').toggleClass('is-active', $input.is(':checked'));
        }
      });

      // Старт
      showStep(current);
    });
  });
})(jQuery);
