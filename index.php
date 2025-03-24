<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">-->
    <title>Scriptural Image Slider</title>
    <style>
        .container {
            max-width: 1050px;
            margin: 0 auto;
            padding: 20px;
        }
        .slider-container {
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 56.25%;
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

/*        @media (max-width: 600px) {
        .slide img {
        width: 100%;
        height: 100%;
        object-fit: contain;
           }
        }
*/
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

        select {
            padding: 8px;
            margin-bottom: 20px;
            font-size: 16px;
        }
        .image-url {
            font-size: 18px;
            word-break: break-all;
            text-align: center; 
        }

        .download-button {
            display: inline-block;
        }

        button {
            height: 30px;
            margin-left: 15px;
        }

        /*******  MENU *******/

        @media screen and (max-width: 600px) {
          .navbar a, .dropdown .dropbtn {
            display: none;
          }
          .navbar a.icon {
            float: left;
            display: block;
          }
        }

        @media screen and (max-width: 600px) {
          .navbar.responsive {position: relative;}
          .navbar.responsive .icon {
            position: absolute;
            left: 0;
            bottom: 0;
          }
          .navbar.responsive a {
            float: none;
            display: block;
            text-align: center;
          }
          .navbar.responsive .dropdown {float: none;}
          .navbar.responsive .dropdown-content {position: relative;}
          .navbar.responsive .dropdown .dropbtn {
            display: block;
            width: 100%;
            text-align: center;
          }
        }
        .navbar {
            overflow: hidden;
            background-color: #333;
        }

        .navbar a {
            float: left;
            font-size: 16px;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
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
            display: none; /* Hide the hamburger menu icon by default */
        }

        .dropdown {
            float: left;
            overflow: hidden;
        }

        .dropdown .dropbtn {
            font-size: 16px;  
            border: none;
            outline: none;
            color: white;
            padding: 14px 16px;
            background-color: inherit;
            font-family: inherit;
            margin: 0;
        }


        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            z-index: 1;
        }

        .dropdown-content select {
            display: block;
            width: 100%;
            padding: 12px 16px;
            border: none;
            background-color: #f9f9f9;
            color: black;
            font-size: 16px;
            cursor: pointer;
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

        #current-image-url {
            color: white;
        }

        #current-image-url-container {
            text-align: right;
            padding-right: 15px;
            padding-top: 15px;
        }

    </style>
</head>
    <body>
        
    <div class="navbar" id="myTopnav">
      <a href="https://www.feedmysheepjerusalem.com/">Home</a>
      <div class="dropdown">
        <button class="dropbtn">Categories <em class="fa fa-caret-down"></em></button>
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
       <div class="image-url"><a href="#" target="_blank" id="image-link" class="download-button">Download Image</a></div>
       <div id="current-image-url-container"><span id="current-image-url"></span></div>
       <a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="myMenu()">&#9776;</a> 
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
            let startX, moveX;
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
                    //img.alt = `Slide ${index + 1}`;
                    img.loading = "lazy";
                    
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
                currentImageUrlSpan.textContent = `  Current Image: ` +  parts[parts.length-1];    
            }

            function getCurrentImageUrl() {
                if (images.length > 0 && currentIndex < images.length) {
                    return images[currentIndex];
                }
                return '';
            }

            function nextSlide() {
                if (!isPaused) {
                    currentIndex = (currentIndex + 1) % images.length;
                    updateSlides();
                    resetTimer();
                }
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
                    if (!isPaused) {
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

            // Mouse swipe logic for desktop
            slider.addEventListener('mouseover', () => {
                pauseSlideshow();
            });

            slider.addEventListener('mouseout', () => {
                resumeSlideshow();
            });

            slider.addEventListener('mousedown', (e) => {
                startX = e.clientX;
                isSwiping = true;
                pauseSlideshow();
            });

            slider.addEventListener('mousemove', (e) => {
                if (!isSwiping) return;
                moveX = e.clientX;
            });

            slider.addEventListener('mouseup', () => {
                if (!isSwiping) return;

                isSwiping = false;
                resumeSlideshow();
            });

            // Ensure smooth behavior across all screen sizes with responsive CSS
            window.addEventListener('resize', () => {
                resetTimer();
            });

            // Initialize
            document.getElementById('category-select').addEventListener('change', (e) => {
                currentIndex = 0;
                loadImages(e.target.value);
            });

            // Load initial category
            loadImages('butterflies');
            document.getElementById('category-select').value = 'butterflies';

            // Menu 
            function myMenu() {
                var x = document.getElementById("myTopnav");
                if (x.className === "navbar") {
                    x.className += " responsive";
                } else {
                    x.className = "navbar";
                }
            }

            // Is touchdevice - swipe image
            function isTouchDevice() {
                return 'ontouchstart' in window || navigator.maxTouchPoints > 0;
            }

            function handleSwipe(direction) {
                if (direction === 'left') {
                    // Implement the logic for swiping left
                    nextSlide();
                } else if (direction === 'right') {
                    // Implement the logic for swiping right
                    prevSlide();
                }
            }

            if (isTouchDevice()) {
                const imageViewer = document.getElementById('slides');
                let startX;

                imageViewer.addEventListener('touchstart', (event) => {
                    startX = event.touches[0].clientX;
                });

                imageViewer.addEventListener('touchend', (event) => {
                    const endX = event.changedTouches[0].clientX;
                    const diffX = startX - endX;
                    if (Math.abs(diffX) > 50) {
                        handleSwipe(diffX > 0 ? 'left' : 'right');
                    }
                });
            }
        </script>
    </body>
</html>