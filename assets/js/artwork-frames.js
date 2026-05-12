(function () {
  "use strict";

  var DEFAULT_PCT = 5; // 5% of each wrapper's rendered width
  var FRAME_FADE_MS = 250;

  function initFrameSelector(selector) {
    var frameRoot = selector.closest("[data-frame-root]");
    var choices = Array.prototype.slice.call(
      selector.querySelectorAll("[data-frame-choice]"),
    );
    var thicknessInput = selector.querySelector("[data-frame-thickness]");
    var thicknessLabel = selector.querySelector("[data-frame-thickness-value]");

    if (!frameRoot) {
      return;
    }

    var frameWrappers = Array.prototype.slice.call(
      frameRoot.querySelectorAll(".artwork-frame"),
    );
    var currentPct = DEFAULT_PCT;
    var isFramed = false;

    /*
     * Convert the current percentage to pixels for each wrapper individually,
     * using clientWidth (content area, border-box excluded) as the base.
     * This keeps the frame visually proportional at every rendered size —
     * detail page and interior mockup stay consistent.
     */
    function updateThickness() {
      if (!isFramed) {
        return;
      }
      frameWrappers.forEach(function (wrapper) {
        var px = Math.round(wrapper.clientWidth * (currentPct / 100));
        console.log("px", px);
        console.log("currentPct", currentPct);
        wrapper.style.borderWidth = px + "px";
      });
    }

    function applyFrame(pngUrl, slice) {
      isFramed = !!pngUrl;

      frameWrappers.forEach(function (wrapper) {
        wrapper.classList.add("is-switching");
      });

      setTimeout(function () {
        frameWrappers.forEach(function (wrapper) {
          wrapper.classList.toggle("is-framed", isFramed);

          if (isFramed) {
            wrapper.style.borderImageSource = 'url("' + pngUrl + '")';
            /*
             * No "fill" keyword: the centre slice is discarded so only the 8
             * border pieces render. The artwork image in the content area shows
             * through naturally.
             */
            wrapper.style.borderImageSlice = slice;
          } else {
            wrapper.style.borderWidth = "0px";
            wrapper.style.borderImageSource = "";
            wrapper.style.borderImageSlice = "";
          }
        });

        if (thicknessInput) {
          thicknessInput.disabled = !isFramed;
        }

        if (isFramed) {
          updateThickness();
        }

        requestAnimationFrame(function () {
          frameWrappers.forEach(function (wrapper) {
            wrapper.classList.remove("is-switching");
          });
        });
      }, FRAME_FADE_MS);
    }

    choices.forEach(function (choice) {
      choice.addEventListener("click", function () {
        choices.forEach(function (item) {
          item.classList.remove("is-active");
          item.setAttribute("aria-pressed", "false");
        });

        choice.classList.add("is-active");
        choice.setAttribute("aria-pressed", "true");

        var thicknessPct = parseFloat(choice.dataset.frameThicknessPct);
        if (thicknessPct > 0) {
          if (thicknessInput) {
            /*
             * Clamp to the slider's own range so the thumb stays on track.
             */
            thicknessPct = Math.min(
              Math.max(thicknessPct, parseFloat(thicknessInput.min)),
              parseFloat(thicknessInput.max),
            );
            thicknessInput.value = thicknessPct;
            if (thicknessLabel) {
              thicknessLabel.textContent = thicknessPct + "%";
            }
          }
          currentPct = thicknessPct;
        }

        applyFrame(
          choice.dataset.framePngUrl || "",
          choice.dataset.frameSlice || "30",
        );
      });
    });

    if (thicknessInput) {
      thicknessInput.addEventListener("input", function () {
        currentPct = parseFloat(thicknessInput.value);
        if (thicknessLabel) {
          thicknessLabel.textContent = currentPct + "%";
        }
        updateThickness();
      });
    }

    /*
     * Recalculate thickness after an interior switch completes.
     * Two rAFs let the browser reflow with the new CSS var values
     * so clientWidth reflects the interior's actual rendered size.
     */
    document.addEventListener("interiorChanged", function () {
      requestAnimationFrame(function () {
        requestAnimationFrame(updateThickness);
      });
    });
  }

  function init() {
    Array.prototype.slice
      .call(document.querySelectorAll("[data-frame-selector]"))
      .forEach(initFrameSelector);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
