<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scriptural Image Slider</title>

    <style>
        body {
            margin: 0; /* Remove default body margin */
            font-family: Arial, sans-serif; /* Add a fallback font */
        }
        .container {
            max-width: 1050px;
            margin: 0 auto;
            padding: 20px;
        }
        .slider-container {
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            overflow: hidden;
            /* border-radius: 8px; */
        }
        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }
        .slide.active {
            opacity: 1;
        }

        .slide img {
            width: 100%;
            height: 95%;
            object-fit: contain;
            filter: drop-shadow(0.35rem 0.35rem 0.4rem rgba(0, 0, 0, 0.5));
        }

        /* Removed the commented-out media query for .slide img */

        .arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 50%;
            transition: background-color 0.3s;
            opacity: 0;
            z-index: 2; /* Ensure arrows are above slides */
        }

        .arrow:hover {
            background-color: blue;
        }

        .slider-container:hover .arrow {
            opacity: 1;
        }

        .arrow-prev {
            left: 10px;
        }
        
        .arrow-next {
            right: 10px;
        }

        @media (max-width: 768px) {
            .arrow {
                display: none;
            }
        }

        /* Styles for general select element, outside navbar dropdown */
        select {
            padding: 8px;
            margin-bottom: 20px; /* This margin is for the select element itself, not in the navbar */
            font-size: 16px;
        }

        /* General button styling, if not part of download-button */
        button {
            height: auto; /* Let content dictate height */
            padding: 5px 10px; /* Adjusted padding */
            margin-left: 10px; /* Adjusted margin */
            font-size: 14px;
        }

        /******* NAVBAR & MENU STYLES *******/

        .navbar {
            overflow: hidden;
            background-color: #333;
            display: flex; /* Use flexbox for better alignment */
            align-items: center; /* Vertically align items */
            flex-wrap: wrap; /* Allow items to wrap on smaller screens */
            padding: 5px 10px; /* Reduced padding for the whole navbar */
            box-sizing: border-box; /* Include padding in element's total width and height */
        }

        .navbar a {
            /* Removed 'float: left;' comment */
            font-size: 16px;
            color: white;
            text-align: center;
            padding: 10px 12px; /* Reduced padding for links */
            text-decoration: none;
            flex-shrink: 0; /* Prevent items from shrinking if space is tight */
        }

        .navbar a:hover, .dropdown:hover .dropbtn {
            background-color: blue;
            color: white;
        }

        .navbar a.active {
            background-color: #04AA6D;
            color: white;
        }

        .navbar .icon {
            display: none; /* Hide the hamburger menu icon by default on large screens */
            color: white;
            padding: 10px 12px;
            cursor: pointer;
            font-size: 15px; /* Original size for hamburger icon */
            /* Removed 'transition: transform 0.3s ease;' as JS handles content directly */
        }

        .dropdown {
            /* Removed 'float: left;' comment */
            overflow: hidden;
        }

        .dropdown .dropbtn {
            font-size: 16px;
            border: none;
            outline: none;
            color: white;
            padding: 10px 12px; /* Reduced padding for dropdown button */
            background-color: inherit;
            font-family: inherit;
            margin: 0;
            cursor: pointer;
            flex-shrink: 0;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown-content select {
            display: block;
            width: 100%;
            padding: 10px 16px; /* Adjusted padding */
            border: none;
            background-color: #f9f9f9;
            color: black;
            font-size: 16px;
            cursor: pointer;
            -webkit-appearance: none; /* Remove default select styling */
            -moz-appearance: none;
            appearance: none;
            /* Custom arrow for select dropdown */
            background-image: url('data:image/svg+xml;utf8,<svg fill="%23333333" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>');
            background-repeat: no-repeat;
            background-position: right 8px center;
        }

        .dropdown-content select option {
            background-color: white;
            color: black;
        }

        .dropdown-content select option:hover {
            background-color: #ddd;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        /* Styles for the current image URL and download button container */
        #current-image-url-container {
            flex-grow: 1; /* Allows it to take up available space */
            text-align: right; /* Default align to right for larger screens */
            padding-right: 10px; /* Reduced padding */
            padding-top: 0; /* Remove top padding */
            display: flex; /* Use flexbox to align text and download button */
            align-items: center;
            justify-content: flex-end; /* Align to the right */
            gap: 10px; /* Space between elements */
            white-space: nowrap; /* Prevent wrapping of current image text */
            overflow: hidden; /* Hide overflow */
            text-overflow: ellipsis; /* Add ellipsis for long text */
        }

        #current-image-url {
            color: white;
            font-size: 14px; /* Reduced font size */
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .download-button {
            display: inline-block;
            font-size: 14px; /* Reduced font size for the button/link */
            color: white;
            text-decoration: none;
            padding: 5px 8px; /* Reduced padding */
            border: 1px solid white;
            border-radius: 4px;
            background-color: transparent;
            transition: background-color 0.3s, color 0.3s;
        }

        .download-button:hover {
            background-color: white;
            color: #333;
        }

        /* Responsive styles for screens up to 600px wide */
        @media screen and (max-width: 600px) {
            .navbar {
                flex-direction: column; /* Stack items vertically */
                align-items: flex-start; /* Align items to the left */
                padding: 5px;
            }

            /* Hide nav links, dropdown button, and image info by default */
            .navbar a,
            .dropdown .dropbtn,
            #current-image-url-container { /* This targets the container holding the info */
                display: none;
                width: 100%; /* Take full width when shown */
                text-align: left; /* Align text to left */
                padding: 10px 15px; /* Adjust padding for stacked items */
                box-sizing: border-box;
            }

            .navbar .icon {
                display: block; /* Show the hamburger menu icon */
                order: -1; /* Place it at the beginning of the flex items */
                align-self: flex-end; /* Align icon to the right within the flex container */
                padding: 10px;
                /* Font size for the 'X' will be handled by JavaScript */
            }

            /* Styles when the responsive class is active (menu is open) */
            .navbar.responsive {
                position: relative; /* Needed for absolute positioning of the icon */
            }
            
            .navbar.responsive .icon {
                position: absolute; /* Position the icon relative to the navbar */
                top: 5px;
                right: 5px;
                display: block; /* Ensure it stays block */
                float: none; /* Remove float */
                text-align: right; /* Keep the icon on the right */
                align-self: flex-end; /* This might be redundant with absolute positioning but harmless */
            }
            .navbar.responsive a,
            .navbar.responsive .dropdown,
            .navbar.responsive .dropdown-content,
            .navbar.responsive .dropdown .dropbtn,
            .navbar.responsive #current-image-url-container {
                display: block; /* Show all items when responsive class is active */
                width: 100%;
                text-align: left;
            }

            .navbar.responsive .dropdown-content {
                position: static; /* Remove absolute positioning for stacked dropdown */
                width: 100%;
                box-shadow: none; /* Remove shadow if it looks odd when stacked */
            }

            .navbar.responsive .dropdown .dropbtn {
                background-color: #444; /* Slightly different background for dropdown button */
                border-bottom: 1px solid #555;
            }

            #current-image-url-container {
                justify-content: flex-start; /* Align text to left when stacked */
                padding-left: 15px; /* Add left padding */
                text-align: left; /* Ensure text alignment is left */
                flex-direction: column; /* Stack image URL and download button */
                align-items: flex-start; /* Align stacked items to the left */
            }
            #current-image-url {
                font-size: 13px; /* Even smaller font for current image URL */
            }
            .download-button {
                margin-left: 0; /* Remove left margin when stacked */
                margin-top: 5px; /* Add some top margin */
                width: fit-content; /* Adjust width to content */
            }
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
            <a href="#" target="_blank" id="image-link" class="download-button"><center>Download Image</center></a>
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
            const slideInterval = 10000; // 10 seconds
            let startX; // Removed unused 'moveX'
            let isSwiping = false;
            const slider = document.querySelector('.slider-container');

            async function loadImages(category) {
                try {
                    const response = await fetch(`api/images.php?category=${category}`);
                    const data = await response.json();
                    images = data;
                    currentIndex = 0; // Reset index on new category
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
                    img.loading = "lazy"; // Good for performance
                    
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
                var parts = imageLink.href.split("/");
                const currentImageUrlSpan = document.getElementById('current-image-url');
                currentImageUrlSpan.textContent = `Current Image: ` + parts[parts.length-1];    
            }

            function getCurrentImageUrl() {
                if (images.length > 0 && currentIndex < images.length) {
                    return images[currentIndex];
                }
                return '';
            }

            function nextSlide() {
                // The 'if (!isPaused)' check here might be redundant if the slideshow
                // interval itself is paused. However, if 'isPaused' is used for
                // manual controls to temporarily disable them, it's fine.
                // For a typical slideshow, manual controls should always work.
                // I'll leave it as is for now as it doesn't cause harm.
                // if (!isPaused) {
                    currentIndex = (currentIndex + 1) % images.length;
                    updateSlides();
                    resetTimer();
                // }
            }

            function prevSlide() {
                currentIndex = (currentIndex - 1 + images.length) % images.length;
                updateSlides();
                resetTimer();
            }

            function startSlideshow() {
                if (intervalId) {
                    clearInterval(intervalId);
                }
                
                intervalId = setInterval(() => {
                    if (!isPaused) { // Only advance if not paused
                        nextSlide();
                    }
                }, slideInterval);
            }

            function resetTimer() {
                clearInterval(intervalId);
                startSlideshow();
            }

            function pauseSlideshow() {
                isPaused = true;
            }

            function resumeSlideshow() {
                isPaused = false;
            }

            function categoryChanged() {
                const categorySelect = document.getElementById('category-select');
                loadImages(categorySelect.value);
            }

            // Mouse hover logic to pause/resume slideshow
            slider.addEventListener('mouseover', () => {
                pauseSlideshow();
            });

            slider.addEventListener('mouseout', () => {
                // Ensure mouseup has completed before resuming slideshow
                // This prevents the slideshow from immediately restarting if mouseup happens outside the slider
                if (!isSwiping) { 
                    resumeSlideshow();
                }
            });

            // Mouse swipe logic for desktop
            slider.addEventListener('mousedown', (e) => {
                startX = e.clientX;
                isSwiping = true;
                pauseSlideshow(); // Pause slideshow while dragging
            });

            // mousemove is just to track the drag state, no need to store moveX
            slider.addEventListener('mousemove', (e) => {
                if (!isSwiping) return;
                // You could add visual feedback here if desired
            });

            slider.addEventListener('mouseup', (e) => {
                if (!isSwiping) return; // Only process if a drag started
                
                isSwiping = false; // Reset swiping flag immediately
                resumeSlideshow(); // Resume slideshow after the interaction

                const endX = e.clientX;
                const diffX = startX - endX;
                const swipeThreshold = 50; // pixels to count as a swipe

                if (Math.abs(diffX) > swipeThreshold) {
                    handleSwipe(diffX > 0 ? 'left' : 'right');
                }
            });

            // Ensure smooth behavior across all screen sizes with responsive CSS
            window.addEventListener('resize', () => {
                resetTimer(); // Re-evaluate timer behavior on resize
            });

            // Initialize category select change listener
            // (Removed redundant categoryChanged() call as loadImages is called below)
            document.getElementById('category-select').addEventListener('change', (e) => {
                currentIndex = 0; // Reset current index when category changes
                loadImages(e.target.value);
            });

            // Load initial category on page load
            loadImages('butterflies');
            document.getElementById('category-select').value = 'butterflies';

            // Menu function (moved here for better organization within the script block)
            function myMenu() {
                var x = document.getElementById("myTopnav");
                var icon = x.querySelector(".icon"); // This line is crucial for getting the icon element

                if (x.className === "navbar") {
                    x.className += " responsive";
                    icon.innerHTML = "&#x2715;"; // Change to 'X' character
                    icon.style.fontSize = "20px"; // Adjust size for 'X' if needed
                } else {
                    x.className = "navbar";
                    icon.innerHTML = "&#9776;"; // Change back to hamburger icon
                    icon.style.fontSize = "15px"; // Reset size for hamburger
                }
            }

            // Is touchdevice - swipe image (Good separate function)
            function isTouchDevice() {
                return 'ontouchstart' in window || navigator.maxTouchPoints > 0;
            }

            function handleSwipe(direction) {
                if (direction === 'left') {
                    nextSlide();
                } else if (direction === 'right') {
                    prevSlide();
                }
            }

            if (isTouchDevice()) {
                const imageViewer = document.getElementById('slides');
                let touchStartX; // Renamed to avoid conflict with mouse startX

                imageViewer.addEventListener('touchstart', (event) => {
                    touchStartX = event.touches[0].clientX;
                    // Optional: Pause slideshow on touch start if desired,
                    // but the mouseover/mouseout already handles hover for non-touch.
                    // For touch, a tap/swipe might pause briefly.
                });

                imageViewer.addEventListener('touchend', (event) => {
                    const touchEndX = event.changedTouches[0].clientX;
                    const diffX = touchStartX - touchEndX;
                    const swipeThreshold = 50; // Use a constant for consistency

                    if (Math.abs(diffX) > swipeThreshold) {
                        handleSwipe(diffX > 0 ? 'left' : 'right');
                    }
                    // Optional: Resume slideshow after touch interaction if it was paused
                });
            }
        </script>
</body>
</html>
