(function () {
  'use strict';

  var SWITCH_MS = 300;

  function setStageValue(stage, name, value) {
    if (value !== null && value !== '') {
      stage.style.setProperty(name, value);
    }
  }

  function initSelector(selector) {
    var stage = selector.querySelector('[data-interior-stage]');
    var bg = selector.querySelector('[data-interior-bg]');
    var slot = selector.querySelector('[data-interior-slot]');
    var title = selector.querySelector('[data-interior-title]');
    var colorInput = selector.querySelector('[data-interior-color]');
    var colorLabel = selector.querySelector('.artwork-interior-mockups__color');
    var choices = Array.prototype.slice.call(selector.querySelectorAll('[data-interior-choice]'));
    var switchTimer = null;

    if (!stage || !bg) {
      return;
    }

    choices.forEach(function (choice) {
      choice.addEventListener('click', function () {
        if (choice.classList.contains('is-active')) {
          return;
        }

        choices.forEach(function (item) {
          item.classList.remove('is-active');
          item.setAttribute('aria-pressed', 'false');
        });
        choice.classList.add('is-active');
        choice.setAttribute('aria-pressed', 'true');

        clearTimeout(switchTimer);
        stage.classList.add('is-switching');

        switchTimer = setTimeout(function () {
          setStageValue(stage, '--scene-width', choice.dataset.sceneWidth);
          setStageValue(stage, '--scene-height', choice.dataset.sceneHeight);
          setStageValue(stage, '--slot-x', choice.dataset.slotX);
          setStageValue(stage, '--slot-y', choice.dataset.slotY);
          setStageValue(stage, '--slot-width', choice.dataset.slotWidth);
          setStageValue(stage, '--slot-height', choice.dataset.slotHeight);
          setStageValue(stage, '--slot-align-x', choice.dataset.slotAlignX);
          setStageValue(stage, '--slot-align-y', choice.dataset.slotAlignY);
          setStageValue(stage, '--artwork-render-width', choice.dataset.artworkWidth);
          setStageValue(stage, '--artwork-render-height', choice.dataset.artworkHeight);
          setStageValue(stage, '--interior-bg', choice.dataset.bgColor);

          if (slot) {
            slot.classList.toggle('withFrontArt', choice.dataset.withFrontArt === '1');
          }

          bg.src = choice.dataset.bg || bg.src;

          if (title) {
            title.textContent = choice.dataset.title || '';
          }

          if (colorInput && choice.dataset.bgColor) {
            colorInput.value = choice.dataset.bgColor;
          }

          if (colorLabel) {
            colorLabel.hidden = choice.dataset.staticBg === '1';
          }

          stage.dispatchEvent(new CustomEvent('interiorChanged', { bubbles: true }));

          requestAnimationFrame(function () {
            stage.classList.remove('is-switching');
          });
        }, SWITCH_MS);
      });
    });

    if (colorInput) {
      colorInput.addEventListener('input', function () {
        setStageValue(stage, '--interior-bg', colorInput.value);
      });
    }
  }

  function init() {
    Array.prototype.slice.call(document.querySelectorAll('[data-interior-selector]')).forEach(initSelector);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
}());
