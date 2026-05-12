document.addEventListener("DOMContentLoaded", () => {
  // CTA carousel
  const cta = document.querySelector(".home-cta");

  if (cta) {
    const slides = Array.from(cta.querySelectorAll(".home-cta__slide"));
    const interval = Number(cta.dataset.carouselInterval || 3000);
    let activeIndex = slides.findIndex((slide) =>
      slide.classList.contains("is-active"),
    );

    if (slides.length > 1) {
      if (activeIndex < 0) {
        activeIndex = 0;
        slides[0].classList.add("is-active");
      }

      window.setInterval(() => {
        slides[activeIndex].classList.remove("is-active");
        activeIndex = (activeIndex + 1) % slides.length;
        slides[activeIndex].classList.add("is-active");
      }, interval);
    }
  }

  // Lazy-load videos marked with [data-lazy-video].
  // Sources use data-src instead of src so the browser skips network
  // requests until the element scrolls into the viewport.
  const lazyVideos = document.querySelectorAll("[data-lazy-video]");

  if (!lazyVideos.length) {
    return;
  }

  const videoObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) {
          return;
        }

        const video = entry.target;
        const frame = video.closest(".home-video-feature__frame");

        if (frame) {
          frame.classList.add("is-loading");
        }

        video.querySelectorAll("source[data-src]").forEach((source) => {
          source.src = source.dataset.src;
          source.removeAttribute("data-src");
        });

        video.load();
        video.addEventListener("canplay", () => {
          if (frame) {
            frame.classList.remove("is-loading");
          }
        }, { once: true });
        video.play().catch(() => {
          // Autoplay blocked by browser policy — video stays paused, poster shows.
        });

        videoObserver.unobserve(video);
      });
    },
    { rootMargin: "200px" },
  );

  lazyVideos.forEach((video) => videoObserver.observe(video));
});
