document.addEventListener("DOMContentLoaded", () => {
  const sliders = Array.from(
    document.querySelectorAll(".media-slider-block__frame"),
  );

  sliders.forEach((slider) => {
    const slides = Array.from(
      slider.querySelectorAll(".media-slider-block__slide"),
    );
    const interval = Number(slider.dataset.sliderInterval || 3000);
    let activeIndex = slides.findIndex((slide) =>
      slide.classList.contains("is-active"),
    );

    if (slides.length <= 1) {
      return;
    }

    if (activeIndex < 0) {
      activeIndex = 0;
      slides[0].classList.add("is-active");
    }

    window.setInterval(() => {
      slides[activeIndex].classList.remove("is-active");
      activeIndex = (activeIndex + 1) % slides.length;
      slides[activeIndex].classList.add("is-active");
    }, interval);
  });
});
