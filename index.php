<!DOCTYPE html>
<html lang="en">
<head>
    <title>Image Slider</title>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="css/slideshow.css" rel="stylesheet">
    <link href="css/audio.css" rel="stylesheet">
</head>
<body>
    <div class="navbar" id="myTopnav">
        <label class="labelSlideshowControls">Slideshow:</label>
        <a href="#">Home</a>

        <div class="category-wrapper">
            <span class="category-label">Category:</span>
            <select id="category-select">
                <option value="butterflies" selected>Butterflies</option>  <!-- value = remote folder & Text is categeory displayed in dropdown -->
                <option value="birds">Birds</option>
                <option value="flowers">Flowers</option>
                <option value="holiness_glory_everlasting">Holiness-Glory-Everlasting</option>
                <option value="Jerusalem">Jerusalem</option>            
                <option value="lions">Lions</option>
                <option value="miracle-photos">Miracle Photos</option>
                <option value="people">People</option>
                <option value="scenic">Scenic</option>
                <option value="sheep">Sheep and Other Animals</option>
                <option value="shofars and trumpets">Shofars and Silver Trumpets</option>
                <option value="unusual">Unusual</option>                    
            </select>
        </div>

        <div class="autoplay-container">
            <label class="switch">
                <input type="checkbox" id="autoplay-categories">
                <span class="slider-switch"></span>
            </label>
            <span>Autoplay All Categories</span>
        </div>

        <div id="current-image-url-container">
            <!-- <span id="current-image-url"></span> -->
            <a href="#" target="_blank" id="image-link" class="download-button">Download Current Image</a>
        </div>

        <div id="custom-audio-player">
        <label class="labelAudioControls">Audio:</label>

        <div id="player-info">
            <div id="song-title">Loading...</div>
            <div id="song-author"></div>
        </div>

        <div id="player-controls">
            <button id="play-pause-btn" class="text-btn" disabled aria-disabled="true">Play</button>

        <div class="control-row">
          <label>Repeat (<span id="loop-status">Off</span>):</label>
          <button id="loop-btn" aria-label="Repeat">
            <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
              <path fill="currentColor" d="M17 1l4 4-4 4V6H7a4 4 0 0 0 0 8h1v2H7a6 6 0 0 1 0-12h10V1zm-10 22l-4-4 4-4v3h10a4 4 0 0 0 0-8h-1V8h1a6 6 0 0 1 0 12H7v3z"/>  <!-- Loop button graphics -->
            </svg>
          </button>
        </div>

        <div class="control-row volume-row">
          <label>Volume (<span id="volumeDisplay"></span>):</label>
          <input
            type="range"
            id="volume-slider"
            min="0"
            max="100"
            step="1"
            value="70"
          >
        </div>
      </div>
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
    
    <div id="yt-player"></div>

    <script src='js/slideshow.js'></script>
    <script src='js/audio.js'></script>
</body>
</html>
