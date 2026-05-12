document.addEventListener("DOMContentLoaded", () => {
  const loader = document.getElementById("az-page-loader");

  if (!loader) {
    return;
  }

  loader.classList.add("is-done");
  loader.addEventListener("transitionend", () => loader.remove(), { once: true });
});

document.addEventListener("DOMContentLoaded", () => {
  const siteHeader = document.querySelector(".site-header--home-overlay");
  const header = document.querySelector(".site-header");
  const menuToggle = document.querySelector("[data-menu-toggle]");
  const headerNav = document.querySelector("#site-header-navigation");

  const syncBodyOverflow = () => {
    const body = document.body;
    const isLocked =
      body.dataset.menuLock === "true" || body.dataset.lightboxLock === "true";

    body.style.overflow = isLocked ? "hidden" : "";
  };

  if (siteHeader) {
    let isTicking = false;
    const syncHeaderState = () => {
      siteHeader.classList.toggle("is-scrolled", window.scrollY > 120);
      isTicking = false;
    };

    syncHeaderState();

    window.addEventListener(
      "scroll",
      () => {
        if (isTicking) {
          return;
        }

        isTicking = true;
        window.requestAnimationFrame(syncHeaderState);
      },
      { passive: true },
    );
  }

  if (!header || !menuToggle || !headerNav) {
    return;
  }

  const setMenuState = (isOpen) => {
    const alreadyOpen = header.classList.contains("is-menu-open");

    if (alreadyOpen === isOpen) {
      return;
    }

    header.classList.toggle("is-menu-open", isOpen);
    menuToggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
    menuToggle.setAttribute("aria-label", isOpen ? "Close menu" : "Open menu");
    headerNav.setAttribute("aria-hidden", isOpen ? "false" : "true");
    document.body.dataset.menuLock = isOpen ? "true" : "false";
    syncBodyOverflow();
  };

  menuToggle.addEventListener("click", () => {
    setMenuState(!header.classList.contains("is-menu-open"));
  });

  headerNav.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => setMenuState(false));
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      setMenuState(false);
    }
  });

  document.addEventListener("click", (event) => {
    if (!header.classList.contains("is-menu-open")) {
      return;
    }

    if (header.contains(event.target)) {
      return;
    }

    setMenuState(false);
  });

  const desktopQuery = window.matchMedia("(min-width: 641px)");
  const closeMenuOnDesktop = (event) => {
    if (event.matches) {
      setMenuState(false);
    }
  };

  if (typeof desktopQuery.addEventListener === "function") {
    desktopQuery.addEventListener("change", closeMenuOnDesktop);
  } else if (typeof desktopQuery.addListener === "function") {
    desktopQuery.addListener(closeMenuOnDesktop);
  }
});
