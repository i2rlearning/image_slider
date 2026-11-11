/* audio.js - YouTube Iframe API based audio player
   Features:
   - Playlist (multiple songs) with hard-coded artist per track
   - Play button disabled until title is available
   - Repeat toggle (repeat current track on/off) + persisted in localStorage
   - Volume slider + persisted in localStorage
   - Seamless auto-advance through playlist (wraps to start when repeat is off)
*/

(() => {
  // -------------------------
  // 1) Playlist
  // -------------------------
  const playlist = [
    {
      id: "kfCfIwIp8e8",
      artist: "Joshua Snodgrass",
    },
    // {
    //   id: "VIDEO_ID_2",
    //   artist: "Another Artist"
    // }
  ];

  let player = null;
  let currentIndex = 0;

  // Used to avoid multiple intervals at once
  let titlePollTimer = null;

  // -------------------------
  // 2) DOM Elements
  // -------------------------
  const playBtn = document.getElementById("play-pause-btn");
  const loopBtn = document.getElementById("loop-btn");
  const loopStatus = document.getElementById("loop-status");

  const titleEl = document.getElementById("song-title");
  const authorEl = document.getElementById("song-author");

  const volumeSlider = document.getElementById("volume-slider");
  const volumeDisplay = document.getElementById("volumeDisplay");

  // -------------------------
  // 3) Persisted Settings
  // -------------------------
  const STORAGE_KEYS = {
    repeatOne: "audio_repeatOne",
    volume: "audio_volume",
  };

  function saveRepeatOne(value) {
    try {
      localStorage.setItem(STORAGE_KEYS.repeatOne, value ? "1" : "0");
    } catch (e) {}
  }

  function loadRepeatOne() {
    try {
      return localStorage.getItem(STORAGE_KEYS.repeatOne) === "1";
    } catch (e) {
      return false;
    }
  }

  function saveVolume(value) {
    try {
      localStorage.setItem(STORAGE_KEYS.volume, String(value));
    } catch (e) {}
  }

  function loadVolume(defaultValue = 70) {
    try {
      const raw = localStorage.getItem(STORAGE_KEYS.volume);
      const n = Number(raw);
      return Number.isFinite(n) ? n : defaultValue;
    } catch (e) {
      return defaultValue;
    }
  }

  // Repeat mode (restored from storage)
  let repeatOne = loadRepeatOne();

  // -------------------------
  // 4) UI Helpers
  // -------------------------
  function clamp(n, min, max) {
    return Math.max(min, Math.min(max, n));
  }

  function setPlayEnabled(enabled) {
    playBtn.disabled = !enabled;
    playBtn.setAttribute("aria-disabled", String(!enabled));
  }

  function setTitleLoading() {
    titleEl.textContent = "Loading...";
    // keep author as-is to avoid flicker on load/track change
    setPlayEnabled(false);
    playBtn.textContent = "Play";
  }

  function setLoopUI() {
    if (!loopStatus) return;
    loopStatus.textContent = repeatOne ? "On" : "Off";
  }

  function setLoopActiveUI() {
    if (!loopBtn) return;
    loopBtn.classList.toggle("active", repeatOne);
  }

  function setVolumeUI(value) {
    const v = clamp(Number(value), 0, 100);
    if (volumeSlider) volumeSlider.value = String(v);
    if (volumeDisplay) volumeDisplay.textContent = String(v);
  }

  // -------------------------
  // 5) Title Loading
  // -------------------------
  function stopTitlePolling() {
    if (titlePollTimer) {
      clearInterval(titlePollTimer);
      titlePollTimer = null;
    }
  }

  function startTitlePolling() {
    stopTitlePolling();
    titlePollTimer = setInterval(() => {
      if (!player || !player.getVideoData) return;

      const data = player.getVideoData();
      const title = (data && data.title ? data.title : "").trim();

      if (title) {
        stopTitlePolling();
        titleEl.textContent = title;
        setPlayEnabled(true);
      }
    }, 150);
  }

  // -------------------------
  // 6) Playback Control
  // -------------------------
  function playCurrent() {
    if (!player) return;
    player.playVideo();
    playBtn.textContent = "Pause";
  }

  function pauseCurrent() {
    if (!player) return;
    player.pauseVideo();
    playBtn.textContent = "Play";
  }

  function loadIndex(index, autoplay) {
    if (!player) return;

    const safeIndex = ((index % playlist.length) + playlist.length) % playlist.length;
    currentIndex = safeIndex;

    setTitleLoading();

    const track = playlist[currentIndex];

    // Hard-coded artist
    if (authorEl) authorEl.textContent = track.artist || "";

    if (autoplay) {
      player.loadVideoById(track.id);
    } else {
      player.cueVideoById(track.id);
    }

    startTitlePolling();
  }

  function nextTrack(autoplay) {
    if (playlist.length <= 1) {
      loadIndex(0, autoplay);
      return;
    }
    loadIndex(currentIndex + 1, autoplay);
  }

  // -------------------------
  // 7) Event Handlers
  // -------------------------
  function onPlayerReady() {
    // Apply saved volume immediately so defaults never appear
    const baseDefault = volumeSlider ? Number(volumeSlider.value) : 70;
    const initialVolume = clamp(loadVolume(baseDefault), 0, 100);

    setVolumeUI(initialVolume);
    player.setVolume(initialVolume);

    // Apply saved loop state immediately so defaults never appear
    setLoopUI();
    setLoopActiveUI();

    // Load first track (paused) and wait for title
    loadIndex(currentIndex, false);
  }

  function onPlayerStateChange(event) {
    const state = event.data;

    if (state === YT.PlayerState.PLAYING) {
      playBtn.textContent = "Pause";
    }

    if (state === YT.PlayerState.PAUSED || state === YT.PlayerState.CUED) {
      playBtn.textContent = "Play";
    }

    if (state === YT.PlayerState.ENDED) {
      if (repeatOne) {
        loadIndex(currentIndex, true);
      } else {
        nextTrack(true);
      }
    }
  }

  // -------------------------
  // 8) Wire up UI controls
  // -------------------------
  // Set initial UI from saved values right away (before YouTube is ready)
  setTitleLoading();
  setLoopUI();
  setLoopActiveUI();

  const initialSavedVolume = clamp(
    loadVolume(volumeSlider ? Number(volumeSlider.value) : 70),
    0,
    100
  );
  setVolumeUI(initialSavedVolume);

  playBtn.addEventListener("click", () => {
    if (!player || playBtn.disabled) return;

    const state = player.getPlayerState();
    if (state === YT.PlayerState.PLAYING) {
      pauseCurrent();
    } else {
      playCurrent();
    }
  });

  if (loopBtn) {
    loopBtn.addEventListener("click", () => {
      repeatOne = !repeatOne;
      saveRepeatOne(repeatOne);

      setLoopUI();
      setLoopActiveUI();
    });
  }

  if (volumeSlider) {
    volumeSlider.addEventListener("input", (e) => {
      const v = clamp(Number(e.target.value), 0, 100);
      setVolumeUI(v);
      saveVolume(v);

      if (player && player.setVolume) player.setVolume(v);
    });
  }

  // -------------------------
  // 9) Load YouTube Iframe API + Create Player
  // -------------------------
  function injectYouTubeAPI() {
    if (document.querySelector('script[src="https://www.youtube.com/iframe_api"]')) return;
    const tag = document.createElement("script");
    tag.src = "https://www.youtube.com/iframe_api";
    document.body.appendChild(tag);
  }

  window.onYouTubeIframeAPIReady = function () {
    if (!playlist.length) {
      titleEl.textContent = "No tracks in playlist";
      setPlayEnabled(false);
      return;
    }

    player = new YT.Player("yt-player", {
      height: "0",
      width: "0",
      videoId: playlist[currentIndex].id,
      playerVars: {
        autoplay: 0,
        controls: 0,
        modestbranding: 1,
        rel: 0,
      },
      events: {
        onReady: onPlayerReady,
        onStateChange: onPlayerStateChange,
      },
    });
  };

  injectYouTubeAPI();
})();
