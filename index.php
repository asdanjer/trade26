<!DOCTYPE html>
<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}?>
<html>
<head>
    <title>Trade Offers</title>
<?php include 'navbar.php'; ?>

</head>
<body>
<div class=".container-fluid">
    <h1>Trade Offers</h1>


        <div class="col-md-10 content-area" style="padding-left: 20px>
            <div class="search-filter-section">
        <div class="search-filter-section">
            <form id="searchForm" class="search-form">
                <input type="text" class="form-control search-input" placeholder="Search Item" id="searchInput">
                <button class="btn btn-primary search-btn" type="submit">Filter</button>
            </form>
            <div class="currency-select">
                <label for="currencyType" class="form-label">Display Prices In:</label>
                <select id="currencyType" class="form-select">
                    <option value="diamonds">Diamonds</option>
                    <option value="diamond_blocks">Diamond Blocks</option>
                    <option value="lerokko_coins">Lerokko Coins</option>
                </select>
            </div>
        </div>
        </div>
        <div class="offers-table col-md-10" style="padding-left: 20px >
                <div class="table-responsive" id="offersTableContainer">
                </div>
		</div>

            <div class="map-container">
                <img src="sd.png" alt="Trade Map" class="map-image" id="mapImage">
                <div id="blinkingDot"></div>
            </div>

    </div>
</div>

<style>
/* Ensure the main content area does not cover the right 15% of the screen */
.content-area {
    max-width: calc(100% - 25%);
    float: left; /* Optional: Ensure it aligns to the left */
}

/* Adjust the map container to take up the right 15% of the screen */
.map-container {
    width: 25%;
    float: right; /* Ensure it aligns to the right */
}
html, body {
    margin: 0;
    padding: 0;
    min-height: 100vh; /* Ensures the body takes at least the full height of the viewport */
    width: 100%; /* Ensures the body takes the full width of the viewport */
}

/* Other styles remain the same */

body {
    font-family: 'Arial', sans-serif;
}

.navbar {
    background-color: #007bff;
    /* Add more navbar styling here */
}

.search-form {
    display: flex;
    gap: 10px;
}

.search-input {
    flex-grow: 1;
}

.search-btn {
    /* Add more button styling here */
}

.currency-select {
    max-width: 300px;
}

.offers-table .table {
    margin-top: 20px;
    /* Add more table styling here */
}

.map-container {
    position: fixed; /* Fix the position relative to the viewport */
    bottom: 5%; /* Adjust based on your navbar height and desired spacing */
    right: 20px; /* Adjust for spacing from the right edge of the viewport */
    width: 15%; /* Set the width to be 20% of the viewport width */
    height: auto; /* Adjust height as needed or set to auto */
    z-index: 1000; /* Ensure it's above other content */
}


.map-image {
    width: 100%;
    height: auto; /* Adjust based on your preference */
    object-fit: cover; /* Ensures the image covers the container without stretching */
}


#blinkingDot {
    position: absolute; /* Absolute positioning within .map-container */
    width: 20px;
    height: 20px;
    background-color: red;
    border-radius: 50%;
    /* Ensure the dot is visible by setting a high z-index */
    z-index: 10;
    /* Remove left and top styles if set elsewhere */
	animation: blink 1s infinite; /* Adjust duration as needed */

}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0; }
}

</style>
<script>
$(document).ready(function() {
    function initializeMap() {
        // Map-related JavaScript code here
        // For example:
        $('#minecraftMap').attr('src', 'sd.png'); // Ensure this path is correct

        $('.map-hover').hover(function() {
            // The hover functionality for map hover
        }, function() {
            $('.map-dot').remove(); // Remove the dot when not hovering
        });
    }

    function loadOffersTable(searchTerm) {
        var currencyType = $('#currencyType').val();
        $("#offersTableContainer").load(`search_offers.php?search_item=${encodeURIComponent(searchTerm)}&currency_type=${currencyType}`, function() {
            initializeMap(); // Call the function after the content is loaded
        });
    }
	// Update the currency and table when the currency type changes
	$('#currencyType').change(function() {
		loadOffersTable($('#searchInput').val());
});

    // Initial load of the offers table without any search term
    loadOffersTable('');

    // Trigger search in real-time as the user types in the search input
    $("#searchInput").on("keyup", function() {
        var searchItem = $(this).val();
        loadOffersTable(searchItem);
    });

    // Optional: If you want to keep the form submission functionality as a fallback
    $("#searchForm").submit(function(event) {
        event.preventDefault(); // Prevent the form from submitting via the browser
        var searchItem = $("#searchInput").val(); // Get the search term
        loadOffersTable(searchItem);
    });
});
function switchMap(location, latitude, longitude) {
    // Update the map image source based on the location
	console.log(location)
    var mapSrc = "sd.png"; // Default map image
    if (location === "shopping district") {
        mapSrc = "sd.png"; // Shopping district map image
    } else if (location === "end") {
        mapSrc = "end.png"; // End map image
    }
    $("#mapImage").attr("src", mapSrc);

    // Ensure the blinking dot exists in the map container
    if ($("#blinkingDot").length === 0) {
        $("#map-container").append('<div id="blinkingDot"></div>');
    }

    // Calculate dot position based on latitude and longitude
    var dotPosition = calculateDotPosition(mapSrc,latitude, longitude);

    // Position the blinking dot within the map container
    $("#blinkingDot").css({"left": dotPosition.x + "px", "top": dotPosition.y + "px"});
}





function calculateDotPosition(loc,latitude, longitude) {
	console.log(loc)
    // These constants represent the Minecraft coordinates corresponding to the map corners
			var mapTopLeft = {lat: 0, longi: 0}; 
			var mapBottomRight = {lat: 0, longi: 0}; 
    if (loc === "sd.png") {
			mapTopLeft = {lat: -1192, longi: 1657}; 
			mapBottomRight = {lat: -1061, longi: 1831}; 
    } else if (loc === "end.png") {
			mapTopLeft = {lat: 0, longi: 0}; 
			mapBottomRight = {lat: -2000, longi: 2000};		
    }

	console.log("input: ",latitude,longitude)
    // Calculate the relative position (0 to 1) within the map
    var relativeY = (longitude - mapTopLeft.longi) / (mapBottomRight.longi - mapTopLeft.longi);
    var relativeX = (latitude - mapTopLeft.lat) / (mapBottomRight.lat - mapTopLeft.lat);
	console.log("rel xy: ",relativeX, relativeY);
    // Convert to pixel position within the map container
    var mapWidth = $("#mapImage").width();
    var mapHeight = $("#mapImage").height();
	console.log("withhight: ", mapWidth, mapHeight);
    var x = relativeX * mapWidth;
    var y = relativeY * mapHeight;
	console.log("finalcords: ",x, y);
    return {x: x, y: y};
}

// Modify the existing document.ready function to include hover event handlers
$(document).ready(function() {
    // Existing code...

    // Hover event for table rows
$(document).on('mouseenter', 'tr', function() {
    var latitude = $(this).find('.map-hover').data('latitude');
    var longitude = $(this).find('.map-hover').data('longitude');
    var location = $(this).find('.map-hover').data('location'); // Added this line
    switchMap(location, latitude, longitude);
	console.log(latitude, longitude, location)
});


});

</script>
	
</body>
</html>
