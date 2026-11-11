/* ==========================================
   SLIDESHOW.JS - UPDATED (IMMEDIATE CATEGORY SWITCH)
   - Stops timer instantly on category change
   - Aborts any in-flight fetch to prevent race conditions
   - Restarts timer only after new category is rendered
   - Removes reliance on inline HTML onchange handler
   ========================================== */

let currentIndex = 0;
let images = [];
let intervalId = null;
let isPaused = false;
const slideInterval = 10000; // 10 seconds

let startX;
let isSwiping = false;

const slider = document.querySelector(".slider-container");

/* ==========================================
   NEW - LOADING / RACE CONTROL
   ========================================== */
let loadController = null;
let isLoadingCategory = false;

/* ==========================================
   STORAGE - AUTOPLAY CATEGORIES (SINGLE SOURCE OF TRUTH)
   ========================================== */
const AUTO_CATEGORIES_KEY = "autoplay-categories";

function saveAutoCategories(isChecked) {
  try {
    localStorage.setItem(AUTO_CATEGORIES_KEY, isChecked ? "1" : "0");
  } catch (e) {}
}

function getAutoCategories() {
  try {
    const v = localStorage.getItem(AUTO_CATEGORIES_KEY);
    if (v === null) return null; // not saved yet
    return v === "1" || v === "true"; // support any older values
  } catch (e) {
    return null;
  }
}

/* ==========================================
   UI INIT FOR AUTOPLAY (RUN ASAP ON DOM READY)
   ========================================== */
function applyAutoplayCategoriesUI() {
  const autoToggle = document.getElementById("autoplay-categories");
  if (!autoToggle) return;

  const saved = getAutoCategories();

  // Restore if saved, otherwise save default once (first-time visitor)
  if (saved !== null) {
    autoToggle.checked = saved;
  } else {
    saveAutoCategories(autoToggle.checked);
  }
}

function setupAutoplayCategoriesStorage() {
  const autoToggle = document.getElementById("autoplay-categories");
  if (!autoToggle) return;

  autoToggle.addEventListener("change", () => {
    saveAutoCategories(autoToggle.checked);
  });
}

/* ==========================================
   TIMER & CONTROL LOGIC
   ========================================== */
function stopSlideshow() {
  if (intervalId) {
    clearInterval(intervalId);
    intervalId = null;
  }
}

function startSlideshow() {
  stopSlideshow();
  intervalId = setInterval(() => {
    if (!isPaused && !isLoadingCategory) nextSlide();
  }, slideInterval);
}

function resetTimer() {
  // Only reset if we're not mid-load
  if (!isLoadingCategory) startSlideshow();
}

function pauseSlideshow() {
  isPaused = true;
}
function resumeSlideshow() {
  isPaused = false;
}

/* ==========================================
   IMAGE LOADING & RENDERING
   ========================================== */
async function loadImages(category) {
  try {
    // Stop timer and freeze navigation immediately
    stopSlideshow();
    isPaused = true;
    isLoadingCategory = true;

    // Cancel any previous fetch still running
    if (loadController) loadController.abort();
    loadController = new AbortController();

    const response = await fetch(
      `api/images.php?category=${encodeURIComponent(category)}`,
      {
        signal: loadController.signal,
        cache: "no-store",
      }
    );

    if (!response.ok) throw new Error(`HTTP ${response.status}`);

    const data = await response.json();

    images = Array.isArray(data) ? data : [];
    currentIndex = 0;

    renderSlides();

    // Resume cleanly for the new category
    isLoadingCategory = false;
    isPaused = false;
    startSlideshow();
  } catch (error) {
    // Ignore abort errors (user changed category again)
    if (error && error.name === "AbortError") return;

    console.error("Error loading images:", error);

    // Fail-safe: unfreeze and restart timer so UI isn't stuck
    isLoadingCategory = false;
    isPaused = false;
    startSlideshow();
  }
}

function renderSlides() {
  const slidesContainer = document.getElementById("slides");
  if (!slidesContainer) return;

  slidesContainer.innerHTML = "";

  images.forEach((imagePath, index) => {
    const slide = document.createElement("div");
    slide.className = `slide ${index === currentIndex ? "active" : ""}`;

    const img = document.createElement("img");
    img.src = imagePath;
    img.loading = "lazy";

    slide.appendChild(img);
    slidesContainer.appendChild(slide);
  });

  updateCurrentImageUrl();
}

function updateSlides() {
  const slides = document.querySelectorAll(".slide");
  slides.forEach((slide, index) => {
    slide.className = `slide ${index === currentIndex ? "active" : ""}`;
  });
  updateCurrentImageUrl();
}

function updateCurrentImageUrl() {
  const currentImageUrl = getCurrentImageUrl();
  const imageLink = document.getElementById("image-link");
  if (!imageLink) return;

  imageLink.href = currentImageUrl;

  const parts = imageLink.href.split("/");
  const currentImageUrlSpan = document.getElementById("current-image-url");
  if (currentImageUrlSpan) {
    currentImageUrlSpan.textContent =
      "Current Image: " + parts[parts.length - 1];
  }
}

function getCurrentImageUrl() {
  return images.length > 0 && currentIndex < images.length
    ? images[currentIndex]
    : "";
}

/* ==========================================
   SLIDESHOW NAVIGATION LOGIC
   ========================================== */
function nextSlide() {
  if (images.length === 0) return;

  const isLastImage = currentIndex === images.length - 1;
  const autoEl = document.getElementById("autoplay-categories");
  const autoNextCategory = autoEl ? autoEl.checked : false;

  if (isLastImage && autoNextCategory) {
    playNextCategory();
  } else {
    currentIndex = (currentIndex + 1) % images.length;
    updateSlides();
    resetTimer();
  }
}

function prevSlide() {
  if (images.length === 0) return;

  currentIndex = (currentIndex - 1 + images.length) % images.length;
  updateSlides();
  resetTimer();
}

function playNextCategory() {
  const selector = document.getElementById("category-select");
  if (!selector) return;

  if (selector.selectedIndex < selector.options.length - 1) {
    selector.selectedIndex = selector.selectedIndex + 1;
  } else {
    selector.selectedIndex = 0; // Loop back to first category
  }

  // Trigger the change event so the listener handles the load
  selector.dispatchEvent(new Event("change"));
}

/* ==========================================
   INTERACTION (HOVER, SWIPE, TOUCH)
   ========================================== */
if (slider) {
  // Hover pauses the show
  slider.addEventListener("mouseover", () => pauseSlideshow());
  slider.addEventListener("mouseout", () => {
    if (!isSwiping) resumeSlideshow();
  });

  // Mouse swipe support
  slider.addEventListener("mousedown", (e) => {
    startX = e.clientX;
    isSwiping = true;
    pauseSlideshow();
  });

  slider.addEventListener("mouseup", (e) => {
    if (!isSwiping) return;

    isSwiping = false;
    resumeSlideshow();

    const diffX = startX - e.clientX;
    if (Math.abs(diffX) > 50) {
      diffX > 0 ? nextSlide() : prevSlide();
    }
  });

  // Touch support for mobile (with pause/resume)
  slider.addEventListener("touchstart", (e) => {
    startX = e.touches[0].clientX;
    isSwiping = true;
    pauseSlideshow();
  });

  slider.addEventListener("touchend", (e) => {
    isSwiping = false;

    const diffX = startX - e.changedTouches[0].clientX;
    if (Math.abs(diffX) > 50) {
      diffX > 0 ? nextSlide() : prevSlide();
    }

    resumeSlideshow();
  });
}

/* ==========================================
   NAVBAR
   ========================================== */
function myMenu() {
  var x = document.getElementById("myTopnav");
  var icon = x ? x.querySelector(".icon") : null;
  if (!x || !icon) return;

  if (x.className === "navbar") {
    x.className += " responsive";
    icon.innerHTML = "&#x2715;"; // X
  } else {
    x.className = "navbar";
    icon.innerHTML = "&#9776;"; // Hamburger
  }
}

/* ==========================================
   INITIALIZATION
   ========================================== */
function initSlideshowUIAndEvents() {
  const categorySelect = document.getElementById("category-select");

  if (categorySelect) {
    // One clean listener for all category changes
    categorySelect.addEventListener("change", (e) => {
      loadImages(e.target.value);
    });

    // Set default and trigger initial load
    categorySelect.value = "butterflies";
    loadImages("butterflies");
  }
}

/* ==========================================
   DOM READY
   ========================================== */
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", () => {
    applyAutoplayCategoriesUI();
    setupAutoplayCategoriesStorage();
    initSlideshowUIAndEvents();
  });
} else {
  applyAutoplayCategoriesUI();
  setupAutoplayCategoriesStorage();
  initSlideshowUIAndEvents();
}
