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
          ? '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" fill="currentColor" width="1em" height="1em" aria-hidden="true"><path d="M533.6 32.5C598.5 85.2 640 165.8 640 256s-41.5 170.7-106.4 223.5c-10.3 8.4-25.4 6.8-33.8-3.5s-6.8-25.4 3.5-33.8C557.5 398.2 592 331.2 592 256s-34.5-142.2-88.7-186.3c-10.3-8.4-11.8-23.5-3.5-33.8s23.5-11.8 33.8-3.5zM473.1 107c43.2 35.2 70.9 88.9 70.9 149s-27.7 113.8-70.9 149c-10.3 8.4-25.4 6.8-33.8-3.5s-6.8-25.4 3.5-33.8C475.3 341.3 496 301.1 496 256s-20.7-85.3-53.2-111.8c-10.3-8.4-11.8-23.5-3.5-33.8s23.5-11.8 33.8-3.5zm-60.5 74.5C434.1 199.1 448 225.9 448 256s-13.9 56.9-35.4 74.5c-10.3 8.4-25.4 6.8-33.8-3.5s-6.8-25.4 3.5-33.8C393.1 284.4 400 271 400 256s-6.9-28.4-17.7-37.3c-10.3-8.4-11.8-23.5-3.5-33.8s23.5-11.8 33.8-3.5zM301.1 34.8C312.6 40 320 51.4 320 64l0 384c0 12.6-7.4 24-18.9 29.2s-25 3.1-34.4-5.3L131.8 352 64 352c-35.3 0-64-28.7-64-64l0-64c0-35.3 28.7-64 64-64l67.8 0L266.7 40.1c9.4-8.4 22.9-10.4 34.4-5.3z"/></svg>'
          : '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" fill="currentColor" width="1em" height="1em" aria-hidden="true"><path d="M301.1 34.8C312.6 40 320 51.4 320 64l0 384c0 12.6-7.4 24-18.9 29.2s-25 3.1-34.4-5.3L131.8 352 64 352c-35.3 0-64-28.7-64-64l0-64c0-35.3 28.7-64 64-64l67.8 0L266.7 40.1c9.4-8.4 22.9-10.4 34.4-5.3zM425 167l55 55 55-55c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-55 55 55 55c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-55-55-55 55c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l55-55-55-55c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0z"/></svg>';
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
