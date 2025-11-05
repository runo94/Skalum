(function ($) {
  "use strict";

  if (typeof $ === "undefined") {
    console.error("jQuery undefined");
    return;
  }

  $(document).ready(function () {
    $(".testimonials-block__slider").each(function () {
      const $slider = $(this);
      $slider.slick({
        centerMode: true,
        centerPadding: "240px",
        slidesToShow: 1,
        dots: true,
        arrows: false,
        responsive: [
          {
            breakpoint: 768,
            settings: {
              arrows: false,
              centerMode: true,
              centerPadding: "10px",
              slidesToShow: 1,
            },
          },
          {
            breakpoint: 480,
            settings: {
              arrows: false,
              centerMode: true,
              centerPadding: "10px",
              slidesToShow: 1,
            },
          },
        ],
      });
    });
  });
})(jQuery);
