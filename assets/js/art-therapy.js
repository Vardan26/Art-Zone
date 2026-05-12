document.addEventListener("DOMContentLoaded", () => {
  const artTherapyAudio = document.querySelector(".art-therapy-page__audio");
  const artTherapyAudioToggle = document.querySelector("[data-audio-toggle]");

  if (!artTherapyAudio || !artTherapyAudioToggle) {
    return;
  }

  const audioIcon = artTherapyAudioToggle.querySelector(
    ".art-therapy-page__audio-toggle-icon",
  );
  let audioUnlocked = false;
  const unlockEvents = ["pointerdown", "touchstart", "keydown", "wheel", "scroll"];

  const syncAudioToggle = () => {
    const isPaused = artTherapyAudio.paused;
    const isMuted = artTherapyAudio.muted;

    if (audioIcon) {
      audioIcon.innerHTML =
        !isPaused && !isMuted
          ? '<i class="fa-solid fa-volume-high"></i>'
          : '<i class="fa-solid fa-volume-xmark"></i>';
    }

    let label = "Enable audio";
    let pressed = "false";

    if (isPaused) {
      label = "Play audio";
      pressed = "false";
    } else if (isMuted) {
      label = "Enable audio";
      pressed = "false";
    } else {
      label = "Pause audio";
      pressed = "true";
    }

    artTherapyAudioToggle.setAttribute("aria-label", label);
    artTherapyAudioToggle.setAttribute("aria-pressed", pressed);
  };

  const removeUnlockListeners = () => {
    unlockEvents.forEach((eventName) => {
      document.removeEventListener(eventName, unlockAudio);
    });
  };

  const enableAudio = () => {
    artTherapyAudio.muted = false;
    const playPromise = artTherapyAudio.play();

    if (playPromise && typeof playPromise.catch === "function") {
      playPromise
        .then(() => {
          audioUnlocked = true;
          removeUnlockListeners();
          syncAudioToggle();
        })
        .catch(() => {
          artTherapyAudio.muted = true;
          syncAudioToggle();
        });

      return;
    }

    audioUnlocked = true;
    removeUnlockListeners();
    syncAudioToggle();
  };

  function unlockAudio() {
    if (audioUnlocked || !artTherapyAudio.muted) {
      removeUnlockListeners();
      return;
    }

    enableAudio();
  };

  artTherapyAudio.muted = true;
  syncAudioToggle();

  const autoplayPromise = artTherapyAudio.play();

  if (autoplayPromise && typeof autoplayPromise.catch === "function") {
    autoplayPromise.catch(() => {
      artTherapyAudio.muted = true;
      syncAudioToggle();
      artTherapyAudio.play().catch(() => {});
    });
  }

  unlockEvents.forEach((eventName) => {
    document.addEventListener(eventName, unlockAudio, {
      passive: true,
    });
  });

  artTherapyAudioToggle.addEventListener("click", () => {
    if (artTherapyAudio.paused) {
      if (audioUnlocked) {
        artTherapyAudio.play().then(() => {
          syncAudioToggle();
        }).catch(() => {
          syncAudioToggle();
        });
      } else {
        enableAudio();
      }
    } else if (artTherapyAudio.muted) {
      enableAudio();
    } else {
      artTherapyAudio.pause();
      syncAudioToggle();
    }
  });

  artTherapyAudio.addEventListener("play", syncAudioToggle);
  artTherapyAudio.addEventListener("pause", syncAudioToggle);
});
