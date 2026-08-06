/**
 * Подія form_submit у dataLayer для GTM / Google Ads.
 *
 * Слухаємо nfFormSubmitResponse — це відповідь сервера на AJAX-сабміт, а не
 * натискання кнопки. Ninja Forms віддає payload виду:
 *   { response: { data: {...}, errors: {...}, debug: {...} }, id: formId }
 * Подія летить тільки коли errors порожні — так само, як NF визначає успіх
 * (0 == _.size(response.errors) у їхньому front-end.js).
 */
(function () {
  if (typeof jQuery === 'undefined') return;

  /**
   * NF віддає errors або порожнім масивом (успіх), або об'єктом
   * { fields: {...}, form: [...], nonce: {...} } (помилка).
   */
  function hasErrors(errors) {
    if (!errors) return false;
    if (Array.isArray(errors)) return errors.length > 0;
    if (typeof errors !== 'object') return true;

    for (var key in errors) {
      if (!Object.prototype.hasOwnProperty.call(errors, key)) continue;
      var value = errors[key];
      if (Array.isArray(value) ? value.length : value) return true;
    }
    return false;
  }

  function getFormName(formId) {
    var forms = window.nfForms || [];
    for (var i = 0; i < forms.length; i++) {
      var form = forms[i];
      if (form && String(form.id) === String(formId)) {
        return (form.settings && form.settings.title) || form.title || '';
      }
    }
    return '';
  }

  jQuery(document).on('nfFormSubmitResponse', function (e, payload) {
    var res = (payload && payload.response) || {};
    var data = res.data || {};

    // помилки валідації / нонсу / спаму — це не відправка
    if (hasErrors(res.errors) || hasErrors(data.errors)) return;

    // сабмішн зупинено екшеном (halt) — обробка не дійшла до кінця
    if (hasErrors(data.halt)) return;

    // без form_id це не оброблений сабмішн
    var formId = (payload && payload.id) || data.form_id || '';
    if (!formId) return;

    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
      event: 'form_submit',
      form_id: 'nf-form-' + formId,
      form_name: getFormName(formId)
    });
  });
})();
