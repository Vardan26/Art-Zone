document.addEventListener("DOMContentLoaded", () => {
  const sliders = Array.from(
    document.querySelectorAll(".about-page__studio-slider"),
  );

  sliders.forEach((slider) => {
    const slides = Array.from(
      slider.querySelectorAll(".about-page__studio-slide"),
    );
    const dots = Array.from(slider.querySelectorAll(".about-page__studio-dot"));
    const interval = Number(slider.dataset.sliderInterval || 3000);
    let activeIndex = slides.findIndex((slide) =>
      slide.classList.contains("is-active"),
    );
    let timerId = null;

    if (slides.length <= 1) {
      return;
    }

    if (activeIndex < 0) {
      activeIndex = 0;
    }

    const setActiveSlide = (nextIndex) => {
      slides.forEach((slide, slideIndex) => {
        slide.classList.toggle("is-active", slideIndex === nextIndex);
      });

      dots.forEach((dot, dotIndex) => {
        dot.classList.toggle("is-active", dotIndex === nextIndex);
      });

      activeIndex = nextIndex;
    };

    dots.forEach((dot) => {
      dot.addEventListener("click", () => {
        setActiveSlide(Number(dot.dataset.slideIndex || 0));
      });
    });

    const startAutoplay = () => {
      if (timerId) {
        return;
      }

      timerId = window.setInterval(() => {
        setActiveSlide((activeIndex + 1) % slides.length);
      }, interval);
    };

    const stopAutoplay = () => {
      if (!timerId) {
        return;
      }

      window.clearInterval(timerId);
      timerId = null;
    };

    slider.addEventListener("mouseenter", startAutoplay);
    slider.addEventListener("mouseleave", stopAutoplay);
  });
});
