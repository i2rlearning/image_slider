<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Scriptural Image Slider</title>

  <style>
    /* --- your CSS unchanged, just moved into <head> --- */
    body { margin: 0; font-family: Arial, sans-serif; }
    .container { max-width: 1050px; margin: 0 auto; padding: 20px; }
    .slider-container { position: relative; width: 100%; height: 0; padding-bottom: 56.25%; overflow: hidden; }
    .slide { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; transition: opacity 0.5s ease-in-out; }
    .slide.active { opacity: 1; }
    .slide img { width: 100%; height: 95%; object-fit: contain; filter: drop-shadow(0.35rem 0.35rem 0.4rem rgba(0,0,0,0.5)); }
    .arrow { position: absolute; top: 50%; transform: translateY(-50%); width: 40px; height: 40px; background-color: rgba(0,0,0,0.5); color: #fff; font-size: 24px; display: flex; align-items: center; justify-content: center; cursor: pointer; border-radius: 50%; transition: background-color 0.3s; opacity: 0; z-index: 2; }
    .arrow:hover { background-color: blue; }
    .slider-container:hover .arrow { opacity: 1; }
    .arrow-prev { left: 10px; }
    .arrow-next { right: 10px; }
    @media (max-width: 768px){ .arrow { display: none; } }
    select { padding: 8px; margin-bottom: 20px; font-size: 16px; }
    button { height: auto; padding: 5px 10px; margin-left: 10px; font-size: 14px; }

    /* NAVBAR */
    .navbar { overflow: hidden; background:#333; display:flex; align-items:center; flex-wrap:wrap; padding:5px 10px; box-sizing:border-box; }
    .navbar a { font-size:16px; color:#fff; text-align:center; padding:10px 12px; text-decoration:none; flex-shrink:0; }
    .navbar a:hover, .dropdown:hover .dropbtn { background:blue; color:#fff; }
    .navbar a.active { background:#04AA6D; color:#fff; }
    .navbar .icon { display:none; color:#fff; padding:10px 12px; cursor:pointer; font-size:15px; }

    .dropdown { overflow:hidden; }
    .dropdown .dropbtn { font-size:16px; border:none; outline:none; color:#fff; padding:10px 12px; background:inherit; font-family:inherit; margin:0; cursor:pointer; flex-shrink:0; }
    .dropdown-content { display:none; position:absolute; background:#f9f9f9; min-width:160px; box-shadow:0 8px 16px rgba(0,0,0,0.2); z-index:1; }
    .dropdown-content select { display:block; width:100%; padding:10px 16px; border:none; background:#f9f9f9; color:#000; font-size:16px; cursor:pointer; appearance:none;
      background-image:url('data:image/svg+xml;utf8,<svg fill="%23333333" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>');
      background-repeat:no-repeat; background-position:right 8px center;
    }
    .dropdown-content select option { background:#fff; color:#000; }
    .dropdown-content select option:hover { background:#ddd; }
    .dropdown:hover .dropdown-content { display:block; }

    #current-image-url-container { flex-grow:1; text-align:right; padding-right:10px; display:flex; align-items:center; justify-content:flex-end; gap:10px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    #current-image-url { color:#fff; font-size:14px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .download-button { display:inline-block; font-size:14px; color:#fff; text-decoration:none; padding:5px 8px; border:1px solid #fff; border-radius:4px; background:transparent; transition:background-color .3s,color .3s; }
    .download-button:hover { background:#fff; color:#333; }

    @media screen and (max-width:600px){
      .navbar { flex-direction:column; align-items:flex-start; padding:5px; }
      .navbar a, .dropdown .dropbtn, #current-image-url-container { display:none; width:100%; text-align:left; padding:10px 15px; box-sizing:border-box; }
      .navbar .icon { display:block; order:-1; align-self:flex-end; padding:10px; }
      .navbar.responsive { position:relative; }
      .navbar.responsive .icon { position:absolute; top:5px; right:5px; display:block; text-align:right; }
      .navbar.responsive a, .navbar.responsive .dropdown, .navbar.responsive .dropdown-content, .navbar.responsive .dropdown .dropbtn, .navbar.responsive #current-image-url-container {
        display:block; width:100%; text-align:left;
      }
      .navbar.responsive .dropdown-content { position:static; width:100%; box-shadow:none; }
      .navbar.responsive .dropdown .dropbtn { background:#444; border-bottom:1px solid #555; }
      #current-image-url-container { justify-content:flex-start; padding-left:15px; text-align:left; flex-direction:column; align-items:flex-start; }
      #current-image-url { font-size:13px; }
      .download-button { margin-left:0; margin-top:5px; width:fit-content; }
    }
  </style>
</head>

<body>
  <div class="navbar" id="myTopnav">
    <a href="https://www.feedmysheepjerusalem.com/">Home</a>
    <div class="dropdown">
      <button class="dropbtn">Categories &#9660;</button>
      <div class="dropdown-content">
        <select id="category-select" onchange="categoryChanged()">
          <option value="butterflies">Butterflies</option>
          <option value="eagles">Eagles</option>
          <option value="flowers">Flowers</option>
          <option value="Jerusalem">Jerusalem</option>
          <option value="lions">Lions</option>
          <option value="miracle-photos">Miracle Photos</option>
          <option value="people">People</option>
          <option value="scenic">Scenic</option>
          <option value="sheep">Sheep</option>
          <option value="unusual">Unusual</option>
        </select>
      </div>
    </div>
    <div id="current-image-url-container">
      <span id="current-image-url"></span>
      <a href="#" target="_blank" id="image-link" class="download-button">Download Image</a>
    </div>
    <a href="javascript:void(0);" class="icon" onclick="myMenu()">&#9776;</a>
  </div>

  <div class="container">
    <div class="slider-container">
      <div id="slides"></div>
      <div class="arrow arrow-prev" onclick="prevSlide()">&#10094;</div>
      <div class="arrow arrow-next" onclick="nextSlide()">&#10095;</div>
    </div>
  </div>

  <script>
    let currentIndex = 0;
    let images = [];
    let intervalId = null;
    let isPaused = false;
    const slideInterval = 10000;
    let startX;
    let isSwiping = false;
    const slider = document.querySelector('.slider-container');

    async function loadImages(category) {
      try {
        const response = await fetch(`api/images.php?category=${category}`);
        const data = await response.json();
        images = data;
        currentIndex = 0;
        renderSlides();
        startSlideshow();
      } catch (error) {
        console.error('Error loading images:', error);
      }
    }

    function renderSlides() {
      const slidesContainer = document.getElementById('slides');
      slidesContainer.innerHTML = '';
      images.forEach((imagePath, index) => {
        const slide = document.createElement('div');
        slide.className = `slide ${index === currentIndex ? 'active' : ''}`;
        const img = document.createElement('img');
        img.src = imagePath;
        img.loading = 'lazy';
        slide.appendChild(img);
        slidesContainer.appendChild(slide);
      });
      updateCurrentImageUrl();
    }

    function updateSlides() {
      const slides = document.querySelectorAll('.slide');
      slides.forEach((slide, index) => {
        slide.className = `slide ${index === currentIndex ? 'active' : ''}`;
      });
      updateCurrentImageUrl();
    }

    function updateCurrentImageUrl() {
      const currentImageUrl = getCurrentImageUrl();
      const imageLink = document.getElementById('image-link');
      imageLink.href = currentImageUrl;
      const parts = imageLink.href.split('/');
      const currentImageUrlSpan = document.getElementById('current-image-url');
      currentImageUrlSpan.textContent = 'Current Image: ' + parts[parts.length - 1];
    }

    function getCurrentImageUrl() {
      if (images.length > 0 && currentIndex < images.length) return images[currentIndex];
      return '';
    }

    function nextSlide() {
      currentIndex = (currentIndex + 1) % images.length;
      updateSlides();
      resetTimer();
    }

    function prevSlide() {
      currentIndex = (currentIndex - 1 + images.length) % images.length;
      updateSlides();
      resetTimer();
    }

    function startSlideshow() {
      if (intervalId) clearInterval(intervalId);
      intervalId = setInterval(() => { if (!isPaused) nextSlide(); }, slideInterval);
    }

    function resetTimer() { clearInterval(intervalId); startSlideshow(); }
    function pauseSlideshow() { isPaused = true; }
    function resumeSlideshow() { isPaused = false; }
    function categoryChanged() { const sel = document.getElementById('category-select'); loadImages(sel.value); }

    slider.addEventListener('mouseover', pauseSlideshow);
    slider.addEventListener('mouseout', () => { if (!isSwiping) resumeSlideshow(); });
    slider.addEventListener('mousedown', (e) => { startX = e.clientX; isSwiping = true; pauseSlideshow(); });
    slider.addEventListener('mousemove', () => { if (!isSwiping) return; });
    slider.addEventListener('mouseup', (e) => {
      if (!isSwiping) return;
      isSwiping = false; resumeSlideshow();
      const diffX = startX - e.clientX;
      const swipeThreshold = 50;
      if (Math.abs(diffX) > swipeThreshold) handleSwipe(diffX > 0 ? 'left' : 'right');
    });

    window.addEventListener('resize', resetTimer);

    document.getElementById('category-select').addEventListener('change', (e) => {
      currentIndex = 0;
      loadImages(e.target.value);
    });

    loadImages('butterflies');
    document.getElementById('category-select').value = 'butterflies';

    function myMenu() {
      const x = document.getElementById('myTopnav');
      const icon = x.querySelector('.icon');
      if (x.className === 'navbar') {
        x.className += ' responsive';
        icon.innerHTML = '&#x2715;';
        icon.style.fontSize = '20px';
      } else {
        x.className = 'navbar';
        icon.innerHTML = '&#9776;';
        icon.style.fontSize = '15px';
      }
    }

    function isTouchDevice() { return 'ontouchstart' in window || navigator.maxTouchPoints > 0; }
    function handleSwipe(direction) { if (direction === 'left') nextSlide(); else if (direction === 'right') prevSlide(); }

    if (isTouchDevice()) {
      const imageViewer = document.getElementById('slides');
      let touchStartX;
      imageViewer.addEventListener('touchstart', (e) => { touchStartX = e.touches[0].clientX; });
      imageViewer.addEventListener('touchend', (e) => {
        const diffX = touchStartX - e.changedTouches[0].clientX;
        const swipeThreshold = 50;
        if (Math.abs(diffX) > swipeThreshold) handleSwipe(diffX > 0 ? 'left' : 'right');
      });
    }
  </script>
</body>
</html>
