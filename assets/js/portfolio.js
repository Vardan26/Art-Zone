document.addEventListener("DOMContentLoaded", () => {
  const page = document.querySelector(".gallery-page");

  if (!page) {
    return;
  }

  // cards grows as JS-built cards are appended from pendingItems.
  const cards = Array.from(page.querySelectorAll(".gallery-card"));
  const grid = page.querySelector(".gallery-grid");
  const filterToggle = page.querySelector("[data-filter-toggle]");
  const filterPanel = page.querySelector(".gallery-filters");
  const filters = Array.from(page.querySelectorAll(".gallery-filters__item"));
  const sentinel = page.querySelector(".gallery-page__sentinel");
  const count = page.querySelector(".gallery-page__count");
  const lightbox = page.querySelector(".gallery-lightbox");
  const lightboxImage = lightbox?.querySelector(".gallery-lightbox__image");
  const lightboxTitle = lightbox?.querySelector(".gallery-lightbox__title");
  const lightboxMeta = lightbox?.querySelector(".gallery-lightbox__meta");
  const lightboxLink = lightbox?.querySelector(".gallery-lightbox__link");
  const lightboxRooms = lightbox?.querySelector(".gallery-lightbox__rooms");
  const lightboxMedia = lightbox?.querySelector(".gallery-lightbox__media");
  const lightboxCloseButtons = Array.from(
    page.querySelectorAll("[data-lightbox-close]"),
  );
  const lightboxPrev = page.querySelector("[data-lightbox-prev]");
  const lightboxNext = page.querySelector("[data-lightbox-next]");
  const interiorTemplates = Array.isArray(window.artZoneInteriorTemplates)
    ? window.artZoneInteriorTemplates
    : [];
  const initialVisible = Number(page.dataset.initialVisible || 20);
  const step = Number(page.dataset.loadStep || 10);
  const countTemplate =
    page.dataset.countTemplate || "Showing %1$s of %2$s works";
  const sizeClasses = [
    "gallery-card--small",
    "gallery-card--wide",
    "gallery-card--tall",
    "gallery-card--big",
  ];

  // Inject filter overlay and sentinel spinner elements.
  const gridLoader = document.createElement("div");
  gridLoader.className = "gallery-grid__loader";
  gridLoader.setAttribute("aria-hidden", "true");
  if (grid) {
    grid.appendChild(gridLoader);
  }

  const sentinelLoader = document.createElement("div");
  sentinelLoader.className = "gallery-page__sentinel-loader";
  sentinelLoader.setAttribute("aria-hidden", "true");
  sentinelLoader.hidden = true;
  if (sentinel && sentinel.parentNode) {
    sentinel.parentNode.insertBefore(sentinelLoader, sentinel);
  }

  let activeTypeFilter = "all";
  let activeCategoryFilter = "all";
  let activeMaterialFilter = "all";
  let activeMediumFilter = "all";
  let visibleLimit = initialVisible;
  let observer = null;
  let lightboxCards = [];
  let activeLightboxIndex = -1;
  let renderFrame = null;

  // Items rendered by PHP stop at 20. The rest live here as plain objects and
  // are materialized into DOM cards on demand (scroll or filter).
  const pendingItems = Array.isArray(window.artZoneGalleryData)
    ? window.artZoneGalleryData.slice()
    : [];

  // Minimal HTML escaping for building card innerHTML from untrusted JSON data.
  const escHtml = (str) =>
    String(str ?? "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");

  // Build a gallery-card <article> element from a plain JSON item object.
  // Mirrors the structure produced by the PHP template for initial cards.
  const buildCard = (item) => {
    const article = document.createElement("article");
    article.className = `gallery-card gallery-card--${item.size || "small"}`;

    article.dataset.types = (item.typeSlugs || []).join(" ");
    article.dataset.categories = (item.categorySlugs || []).join(" ");
    article.dataset.materials = (item.materialSlugs || []).join(" ");
    article.dataset.mediums = (item.mediumSlugs || []).join(" ");
    article.dataset.title = item.title || "";
    article.dataset.image = item.imageLightbox || item.image || "";
    article.dataset.imagePreview = item.image || "";
    article.dataset.permalink = item.permalink || "";
    article.dataset.meta = item.meta || "";
    article.dataset.artworkWidth = String(item.artworkWidth || 0);
    article.dataset.artworkHeight = String(item.artworkHeight || 0);

    article._artZoneFilters = {
      types: item.typeSlugs || [],
      categories: item.categorySlugs || [],
      materials: item.materialSlugs || [],
      mediums: item.mediumSlugs || [],
    };

    article.innerHTML = `<a class="gallery-card__image-wrap" href="${escHtml(item.permalink)}">
        <img
          class="gallery-card__image"
          src="${escHtml(item.image)}"
          alt="${escHtml(item.title)}"
          loading="lazy"
          decoding="async"
        >
        <div class="gallery-card__overlay">
          <p class="gallery-card__overlay-title">${escHtml(item.title)}</p>
          <p class="gallery-card__overlay-meta">${escHtml(item.meta)}</p>
        </div>
      </a>`;

    return article;
  };

  // Append an array of plain JSON items as real DOM cards, then register them
  // with the cards array, size calculator, and lightbox click handler.
  const materializeItems = (items) => {
    if (!items.length || !grid) {
      return;
    }

    const fragment = document.createDocumentFragment();
    const newCards = items.map(buildCard);

    newCards.forEach((card) => fragment.appendChild(card));
    grid.appendChild(fragment);

    newCards.forEach((card) => {
      cards.push(card);
      applyGalleryCardSize(card);

      const trigger = card.querySelector(".gallery-card__image-wrap");

      if (!trigger) {
        return;
      }

      trigger.addEventListener("click", (event) => {
        if (
          event.defaultPrevented ||
          event.metaKey ||
          event.ctrlKey ||
          event.shiftKey ||
          event.altKey
        ) {
          return;
        }

        event.preventDefault();
        openLightbox(card);
      });
    });
  };

  const syncBodyOverflow = () => {
    const body = document.body;
    const isLocked =
      body.dataset.menuLock === "true" || body.dataset.lightboxLock === "true";

    body.style.overflow = isLocked ? "hidden" : "";
  };

  cards.forEach((card) => {
    card._artZoneFilters = {
      types: (card.dataset.types || "").split(" ").filter(Boolean),
      categories: (card.dataset.categories || "").split(" ").filter(Boolean),
      materials: (card.dataset.materials || "").split(" ").filter(Boolean),
      mediums: (card.dataset.mediums || "").split(" ").filter(Boolean),
    };
  });

  if (filterToggle && filterPanel) {
    filterToggle.addEventListener("click", () => {
      const isOpen = filterToggle.getAttribute("aria-expanded") === "true";
      const shouldOpen = !isOpen;

      filterPanel.hidden = !shouldOpen;
      filterToggle.setAttribute("aria-expanded", shouldOpen ? "true" : "false");
      filterToggle.classList.toggle("is-active", shouldOpen);
    });
  }

  const getGalleryCardSize = (width, height, unit = "px") => {
    if (!width || !height) {
      return "small";
    }

    const aspectRatio = width / height;

    if (aspectRatio > 1.5) {
      return "wide";
    }

    if (aspectRatio < 0.8) {
      return "tall";
    }

    if (unit === "cm" && Math.max(width, height) > 200) {
      return "big";
    }

    if (unit === "px" && width > 3500 && height > 3500) {
      return "big";
    }

    return "small";
  };

  const scheduleRender = () => {
    if (renderFrame !== null) {
      return;
    }

    renderFrame = window.requestAnimationFrame(() => {
      renderFrame = null;
      render();
    });
  };

  const applyGalleryCardSize = (card) => {
    const image = card.querySelector(".gallery-card__image");

    if (!image) {
      return;
    }

    const updateSize = () => {
      const artworkWidth = Number(card.dataset.artworkWidth || 0);
      const artworkHeight = Number(card.dataset.artworkHeight || 0);
      const hasRealArtworkSize = artworkWidth > 0 && artworkHeight > 0;
      const size = hasRealArtworkSize
        ? getGalleryCardSize(artworkWidth, artworkHeight, "cm")
        : getGalleryCardSize(image.naturalWidth, image.naturalHeight, "px");
      card.classList.remove(...sizeClasses);
      card.classList.add(`gallery-card--${size}`);
      card.classList.remove("is-loading");
      card.classList.add("is-loaded");
      scheduleRender();
    };

    if (image.complete) {
      updateSize();
      return;
    }

    card.classList.add("is-loading");
    image.addEventListener("load", updateSize, { once: true });
    image.addEventListener(
      "error",
      () => {
        card.classList.remove("is-loading");
        card.classList.add("is-loaded");
      },
      { once: true },
    );
  };

  // Works on a plain filter-data object — used for both DOM cards and pending JSON items.
  const itemHasFilterValue = (filterData, group, value) => {
    if (value === "all") {
      return true;
    }

    const filterSet =
      group === "type"
        ? filterData.types
        : group === "material"
          ? filterData.materials
          : group === "medium"
            ? filterData.mediums
            : filterData.categories;

    return filterSet.includes(value);
  };

  // Convenience wrapper for DOM cards (which carry _artZoneFilters).
  const cardHasFilterValue = (card, group, value) =>
    itemHasFilterValue(card._artZoneFilters, group, value);

  const cardMatchesOtherActiveFilters = (card, group) =>
    (group === "category" ||
      cardHasFilterValue(card, "category", activeCategoryFilter)) &&
    (group === "type" || cardHasFilterValue(card, "type", activeTypeFilter)) &&
    (group === "material" ||
      cardHasFilterValue(card, "material", activeMaterialFilter)) &&
    (group === "medium" ||
      cardHasFilterValue(card, "medium", activeMediumFilter));

  // Combined filter data for every item: materialized DOM cards + still-pending JSON items.
  // Used by syncFilterAvailability so filter buttons reflect the full dataset.
  const getAllItemFilters = () => [
    ...cards.map((c) => c._artZoneFilters),
    ...pendingItems.map((item) => ({
      types: item.typeSlugs || [],
      categories: item.categorySlugs || [],
      materials: item.materialSlugs || [],
      mediums: item.mediumSlugs || [],
    })),
  ];

  const syncFilterAvailability = () => {
    const allFilters = getAllItemFilters();

    filters.forEach((filter) => {
      const group = filter.dataset.filterGroup || "category";
      const value = filter.dataset.filterValue || "all";

      if (value === "all" || filter.classList.contains("is-active")) {
        filter.hidden = false;
        return;
      }

      const hasMatchingItems = allFilters.some((filterData) => {
        const matchesOthers =
          (group === "category" ||
            itemHasFilterValue(filterData, "category", activeCategoryFilter)) &&
          (group === "type" ||
            itemHasFilterValue(filterData, "type", activeTypeFilter)) &&
          (group === "material" ||
            itemHasFilterValue(filterData, "material", activeMaterialFilter)) &&
          (group === "medium" ||
            itemHasFilterValue(filterData, "medium", activeMediumFilter));

        if (!matchesOthers) {
          return false;
        }

        return itemHasFilterValue(filterData, group, value);
      });

      filter.hidden = !hasMatchingItems;
    });

    ["type", "category", "material", "medium"].forEach((group) => {
      const groupFilters = filters.filter(
        (filter) => (filter.dataset.filterGroup || "category") === group,
      );
      const realFilters = groupFilters.filter(
        (filter) => (filter.dataset.filterValue || "all") !== "all",
      );
      const visibleRealFilters = realFilters.filter((filter) => !filter.hidden);
      const allFilter = groupFilters.find(
        (filter) => (filter.dataset.filterValue || "all") === "all",
      );
      const groupElement = groupFilters[0]?.closest(".gallery-filters__group");

      if (allFilter) {
        allFilter.hidden = visibleRealFilters.length === 0;
      }

      if (groupElement) {
        groupElement.hidden = groupFilters.every((filter) => filter.hidden);
      }
    });
  };

  // Total matching items across DOM cards AND unrendered pending items.
  // Used by render() so the "X of Y" count is accurate before all items are in the DOM.
  const totalFilteredCount = () => {
    const domCount = filteredCards().length;
    const pendingCount = pendingItems.filter((item) => {
      const f = {
        types: item.typeSlugs || [],
        categories: item.categorySlugs || [],
        materials: item.materialSlugs || [],
        mediums: item.mediumSlugs || [],
      };
      return (
        itemHasFilterValue(f, "type", activeTypeFilter) &&
        itemHasFilterValue(f, "category", activeCategoryFilter) &&
        itemHasFilterValue(f, "material", activeMaterialFilter) &&
        itemHasFilterValue(f, "medium", activeMediumFilter)
      );
    }).length;

    return domCount + pendingCount;
  };

  const filteredCards = () =>
    cards.filter(
      (card) =>
        cardHasFilterValue(card, "type", activeTypeFilter) &&
        cardHasFilterValue(card, "category", activeCategoryFilter) &&
        cardHasFilterValue(card, "material", activeMaterialFilter) &&
        cardHasFilterValue(card, "medium", activeMediumFilter),
    );

  const getVisibleCards = () => cards.filter((card) => !card.hidden);

  const getColumnCount = () => {
    if (!grid) {
      return 1;
    }

    return Math.max(
      1,
      window
        .getComputedStyle(grid)
        .gridTemplateColumns.split(" ")
        .filter(Boolean).length,
    );
  };

  const getCardSpan = (card) => {
    if (card.classList.contains("gallery-card--big")) {
      return { colSpan: 2, rowSpan: 2 };
    }

    if (card.classList.contains("gallery-card--wide")) {
      return { colSpan: 2, rowSpan: 1 };
    }

    if (card.classList.contains("gallery-card--tall")) {
      return { colSpan: 1, rowSpan: 2 };
    }

    return { colSpan: 1, rowSpan: 1 };
  };

  const ensureRow = (occupancy, rowIndex, columns) => {
    while (occupancy.length <= rowIndex) {
      occupancy.push(new Array(columns).fill(false));
    }
  };

  const canPlaceCard = (occupancy, rowIndex, columnIndex, columns, card) => {
    const { colSpan, rowSpan } = getCardSpan(card);

    if (columnIndex + colSpan > columns) {
      return false;
    }

    for (let rowOffset = 0; rowOffset < rowSpan; rowOffset += 1) {
      ensureRow(occupancy, rowIndex + rowOffset, columns);

      for (let columnOffset = 0; columnOffset < colSpan; columnOffset += 1) {
        if (occupancy[rowIndex + rowOffset][columnIndex + columnOffset]) {
          return false;
        }
      }
    }

    return true;
  };

  const placeCard = (occupancy, rowIndex, columnIndex, columns, card) => {
    const { colSpan, rowSpan } = getCardSpan(card);

    for (let rowOffset = 0; rowOffset < rowSpan; rowOffset += 1) {
      ensureRow(occupancy, rowIndex + rowOffset, columns);

      for (let columnOffset = 0; columnOffset < colSpan; columnOffset += 1) {
        occupancy[rowIndex + rowOffset][columnIndex + columnOffset] = true;
      }
    }
  };

  const countGridHoles = (visibleCards, columns) => {
    const occupancy = [];

    visibleCards.forEach((card) => {
      let placed = false;
      let rowIndex = 0;

      while (!placed) {
        ensureRow(occupancy, rowIndex, columns);

        for (let columnIndex = 0; columnIndex < columns; columnIndex += 1) {
          if (canPlaceCard(occupancy, rowIndex, columnIndex, columns, card)) {
            placeCard(occupancy, rowIndex, columnIndex, columns, card);
            placed = true;
            break;
          }
        }

        rowIndex += 1;
      }
    });

    return occupancy.reduce(
      (total, row) => total + row.filter((cell) => !cell).length,
      0,
    );
  };

  const getExpandedVisibleCards = (matches) => {
    const baseCount = Math.min(visibleLimit, matches.length);
    const baseCards = matches.slice(0, baseCount);
    const holeCount = countGridHoles(baseCards, getColumnCount());
    const extraCount = Math.min(holeCount, matches.length - baseCount);

    return matches.slice(0, baseCount + extraCount);
  };

  const disconnectObserver = () => {
    if (!observer) {
      return;
    }

    observer.disconnect();
    observer = null;
  };

  const setupInfiniteScroll = () => {
    disconnectObserver();

    if (!sentinel || !("IntersectionObserver" in window)) {
      return;
    }

    observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) {
            return;
          }

          const matches = filteredCards();

          // DOM cards exhausted but unrendered items remain — materialize next batch.
          if (visibleLimit >= matches.length && pendingItems.length > 0) {
            sentinelLoader.hidden = false;
            materializeItems(pendingItems.splice(0, step));
            render();
            sentinelLoader.hidden = true;
            return;
          }

          // Nothing left anywhere.
          if (visibleLimit >= matches.length) {
            disconnectObserver();
            return;
          }

          sentinelLoader.hidden = false;
          visibleLimit = Math.min(visibleLimit + step, matches.length);
          render();
          sentinelLoader.hidden = true;
        });
      },
      {
        rootMargin: "0px 0px 320px 0px",
        threshold: 0.01,
      },
    );

    observer.observe(sentinel);
  };

  function render() {
    const matches = filteredCards();
    const visibleCards = getExpandedVisibleCards(matches);
    const visibleCardsSet = new Set(visibleCards);
    const visibleCount = visibleCards.length;

    syncFilterAvailability();

    cards.forEach((card) => {
      card.hidden = !visibleCardsSet.has(card);
    });

    if (count) {
      count.textContent = countTemplate
        .replace("%1$s", String(visibleCount))
        .replace("%2$s", String(totalFilteredCount()));
    }

    setupInfiniteScroll();
  }

  const getRoomOrientation = (w, h) =>
    w > h ? "landscape" : h > w ? "portrait" : "square";

  const getRoomSizeType = (w, h) => {
    const max = Math.max(w, h);
    if (max <= 40) return "xs";
    if (max <= 70) return "sm";
    if (max <= 110) return "md";
    if (max <= 150) return "lg";
    return "xl";
  };

  const getMatchingRooms = (widthCm, heightCm, templates, limit) => {
    if (!widthCm || !heightCm || !templates.length) {
      return templates.slice(0, limit);
    }

    const orientation = getRoomOrientation(widthCm, heightCm);
    const sizeType = getRoomSizeType(widthCm, heightCm);
    const tolerance = 1.05;

    const scored = templates
      .filter((tpl) => {
        const sup = tpl.supports || {};
        if (!(sup.orientations || []).includes(orientation)) return false;
        if (!(sup.sizeTypes || []).includes(sizeType)) return false;
        const s = tpl.slot || {};
        return widthCm <= (s.maxW || 0) * tolerance &&
               heightCm <= (s.maxH || 0) * tolerance;
      })
      .map((tpl) => ({
        ...tpl,
        _score:
          (tpl.sortOrder || 100) * 10 +
          ((tpl.supports?.sizeTypes?.length || 1) <= 1 ? 0 : 1),
      }))
      .sort((a, b) => a._score - b._score);

    return (scored.length > 0 ? scored : templates).slice(0, limit);
  };

  const setLightboxState = (isOpen) => {
    if (!lightbox) {
      return;
    }

    lightbox.hidden = !isOpen;
    lightbox.setAttribute("aria-hidden", isOpen ? "false" : "true");
    document.body.dataset.lightboxLock = isOpen ? "true" : "false";
    syncBodyOverflow();
  };

  const renderLightbox = () => {
    if (
      !lightbox ||
      !lightboxCards.length ||
      activeLightboxIndex < 0 ||
      activeLightboxIndex >= lightboxCards.length ||
      !lightboxImage ||
      !lightboxTitle ||
      !lightboxMeta ||
      !lightboxLink
    ) {
      return;
    }

    const card = lightboxCards[activeLightboxIndex];
    const previewSrc = card.dataset.imagePreview || card.dataset.image || "";
    const fullSrc = card.dataset.image || "";

    lightboxImage.style.transition = "";
    lightboxImage.style.transform = "";
    lightboxImage.style.transformOrigin = "";

    lightboxImage.src = previewSrc;
    lightboxImage.alt = card.dataset.title || "";
    lightboxTitle.textContent = card.dataset.title || "";
    lightboxMeta.textContent = card.dataset.meta || "";
    lightboxLink.href = card.dataset.permalink || "#";

    if (lightboxRooms) {
      const artW = Number(card.dataset.artworkWidth || 0);
      const artH = Number(card.dataset.artworkHeight || 0);
      const rooms = getMatchingRooms(artW, artH, interiorTemplates, 2);

      if (rooms.length > 0) {
        const permalink = card.dataset.permalink || "#";
        const artSrc = card.dataset.imagePreview || card.dataset.image || "";
        const artAlt = card.dataset.title || "";

        lightboxRooms.innerHTML = rooms
          .map((tpl) => {
            const s = tpl.slot || {};
            const slotW = s.w || 50;
            const slotH = s.h || 50;
            const renderW =
              tpl.sceneRealW > 0 && artW > 0
                ? (artW / tpl.sceneRealW) * 100
                : 0;
            const renderH =
              tpl.sceneRealH > 0 && artH > 0
                ? (artH / tpl.sceneRealH) * 100
                : 0;
            const artWPct =
              renderW > 0 ? (renderW / slotW) * 100 : 50;
            const artHPct =
              renderH > 0 ? (renderH / slotH) * 100 : 50;

            return (
              `<a class="lightbox-room" href="${escHtml(permalink)}" tabindex="-1">` +
              `<img class="lightbox-room__bg" src="${escHtml(tpl.thumbUrl)}" alt="" loading="lazy" decoding="async">` +
              `<div class="lightbox-room__slot" style="left:${s.x || 0}%;top:${s.y || 0}%;width:${slotW}%;height:${slotH}%;justify-content:${s.alignX || "center"};align-items:${s.alignY || "center"}">` +
              `<img class="lightbox-room__art" src="${escHtml(artSrc)}" alt="${escHtml(artAlt)}" decoding="async" style="width:${artWPct}%;height:${artHPct}%">` +
              `</div>` +
              `<span class="lightbox-room__label">In Interior</span>` +
              `</a>`
            );
          })
          .join("");
        lightboxRooms.hidden = false;
      } else {
        lightboxRooms.innerHTML = "";
        lightboxRooms.hidden = true;
      }
    }

    if (fullSrc && fullSrc !== previewSrc) {
      const loader = new window.Image();

      loader.onload = () => {
        if (
          activeLightboxIndex >= 0 &&
          lightboxCards[activeLightboxIndex] === card
        ) {
          lightboxImage.src = fullSrc;
        }
      };

      loader.src = fullSrc;
    }
  };

  const openLightbox = (card) => {
    if (!lightbox) {
      return;
    }

    lightboxCards = getVisibleCards();
    activeLightboxIndex = lightboxCards.indexOf(card);

    if (activeLightboxIndex < 0) {
      return;
    }

    renderLightbox();
    setLightboxState(true);
  };

  const closeLightbox = () => {
    if (!lightbox) {
      return;
    }

    setLightboxState(false);
    activeLightboxIndex = -1;
  };

  const navigateLightbox = (direction) => {
    if (!lightboxCards.length) {
      return;
    }

    activeLightboxIndex =
      (activeLightboxIndex + direction + lightboxCards.length) %
      lightboxCards.length;
    renderLightbox();
  };

  filters.forEach((filter) => {
    filter.addEventListener("click", () => {
      const group = filter.dataset.filterGroup || "category";
      const value = filter.dataset.filterValue || "all";

      if (group === "type") {
        activeTypeFilter = value;
      } else if (group === "material") {
        activeMaterialFilter = value;
      } else if (group === "medium") {
        activeMediumFilter = value;
      } else {
        activeCategoryFilter = value;
      }

      visibleLimit = initialVisible;

      filters
        .filter((item) => (item.dataset.filterGroup || "category") === group)
        .forEach((item) => item.classList.remove("is-active"));
      filter.classList.add("is-active");

      // Materialize all unrendered items before applying a specific filter so
      // the filter result is accurate across the full dataset, not just the
      // first 20 PHP-rendered cards. Defer behind a double-rAF so the browser
      // can paint the spinner overlay before the synchronous DOM work begins.
      if (value !== "all" && pendingItems.length > 0 && grid) {
        grid.classList.add("is-filtering");
        window.requestAnimationFrame(() => {
          window.requestAnimationFrame(() => {
            materializeItems(pendingItems.splice(0, pendingItems.length));
            render();
            grid.classList.remove("is-filtering");
          });
        });
      } else {
        render();
      }
    });
  });

  cards.forEach((card) => {
    applyGalleryCardSize(card);

    const trigger = card.querySelector(".gallery-card__image-wrap");

    if (!trigger) {
      return;
    }

    trigger.addEventListener("click", (event) => {
      if (
        event.defaultPrevented ||
        event.metaKey ||
        event.ctrlKey ||
        event.shiftKey ||
        event.altKey
      ) {
        return;
      }

      event.preventDefault();
      openLightbox(card);
    });
  });

  lightboxCloseButtons.forEach((button) => {
    button.addEventListener("click", closeLightbox);
  });

  lightboxPrev?.addEventListener("click", () => {
    navigateLightbox(-1);
  });

  lightboxNext?.addEventListener("click", () => {
    navigateLightbox(1);
  });

  document.addEventListener("keydown", (event) => {
    if (!lightbox || lightbox.hidden) {
      return;
    }

    if (event.key === "Escape") {
      closeLightbox();
      return;
    }

    if (event.key === "ArrowLeft") {
      navigateLightbox(-1);
    }

    if (event.key === "ArrowRight") {
      navigateLightbox(1);
    }
  });

  if (lightboxMedia && lightboxImage) {
    lightboxMedia.addEventListener("mousemove", (e) => {
      const rect = lightboxMedia.getBoundingClientRect();
      const x = ((e.clientX - rect.left) / rect.width) * 100;
      const y = ((e.clientY - rect.top) / rect.height) * 100;
      lightboxImage.style.transformOrigin = `${x}% ${y}%`;
      lightboxImage.style.transform = "scale(2.2)";
      lightboxImage.style.transition = "none";
    });

    lightboxMedia.addEventListener("mouseleave", () => {
      lightboxImage.style.transition = "transform 320ms ease";
      lightboxImage.style.transform = "";
      lightboxImage.style.transformOrigin = "";
    });
  }

  let resizeTimer = null;
  window.addEventListener("resize", () => {
    window.clearTimeout(resizeTimer);
    resizeTimer = window.setTimeout(() => {
      render();
    }, 120);
  });

  render();
});
