<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}?>
<?php
include 'config.php';
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$currencyType = isset($_GET['currency_type']) ? $_GET['currency_type'] : 'diamonds';
$searchTerm = isset($_GET['search_item']) ? '%' . $_GET['search_item'] . '%' : '%';
$stmt = $conn->prepare("SELECT offer.offer_id, offer.item, offer.item_description, offer.type, offer.quantity, offer.price, offer.in_stock, shop.address, shop.location, ST_X(shop.map_location) AS latitude, ST_Y(shop.map_location) AS longitude, owner.name AS seller FROM offer JOIN shop ON offer.shop_id = shop.shop_id JOIN owner ON shop.owner_id = owner.owner_id WHERE offer.item LIKE ?");
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
$conversionRates = [
    'diamonds' => 1, 
    'diamond_blocks' => 1/9, 
    'lerokko_coins' => 10, 
];
echo "<table class='table table-striped'><thead><tr><th>Item</th><th>Description</th><th>Quantity</th><th>Price</th><th>Address</th><th>Location</th><th>Seller</th><th>In Stock</th></tr></thead><tbody>";

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $stockStatus = $row["in_stock"] ? 'checked' : '';
        $toggleClass = $row["in_stock"] ? 'btn-success' : 'btn-danger';
		$convertedPrice = rtrim(rtrim(number_format($row["price"] * $conversionRates[$currencyType],1), "0"), ".");

        echo "<tr>
                <td>".htmlspecialchars($row["item"])."</td>
                <td title='".htmlspecialchars($row["item_description"])."'>".mb_strimwidth(htmlspecialchars($row["item_description"]), 0, 30, "...")."</td>
                <td>".htmlspecialchars($row["quantity"])." ".htmlspecialchars(explode(" ", $row["type"])[0])."</td>
                <td>".htmlspecialchars($convertedPrice)."</td>
                <td class='map-hover' data-map-location='".htmlspecialchars($row["latitude"]).",".htmlspecialchars($row["longitude"])."' data-location='".htmlspecialchars($row["location"])."'>".htmlspecialchars($row["address"])."</td>
                <td class='map-hover' data-map-location='".htmlspecialchars($row["latitude"]).",".htmlspecialchars($row["longitude"])."' data-location='".htmlspecialchars($row["location"])."'>".htmlspecialchars($row["location"])."</td>
                <td>".htmlspecialchars($row["seller"])."</td>
                <td>
                    <label class='switch'>
                        <input type='checkbox' data-offer-id='".$row['offer_id']."' class='stock-toggle' $stockStatus>
                        <span class='slider round'></span>
                    </label>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='8'>No results found.</td></tr>";
}

echo "</tbody></table>";
$conn->close();
?>
<!-- Add a dedicated container for the map next to the table -->
<div id="mapContainer" style="width: 500px; height: 500px; float: right;">
    <img id="minecraftMap" src="" style="width: 100%; height: auto; display: none;">
    <!-- This image element will be used to display the map -->
</div>

<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
.tooltip-map {
    position: relative; /* Container relative to position dot absolutely inside */
    display: inline-block; /* Adjust as needed */
    /* Other styles */
}

.tooltip-map img {
  display: block;
  width: 100%; /* Make the image fully occupy the tooltip */
  height: auto; /* Maintain aspect ratio */
}


</style>

<script>
$(document).ready(function(){
    $('.stock-toggle').change(function(){
        var offerId = $(this).data('offer-id');
        var inStock = $(this).is(':checked') ? 1 : 0;
        
        $.ajax({
            url: 'update_stock_status.php',
            type: 'POST',
            data: { 'offer_id': offerId, 'in_stock': inStock },
            success: function(response){
                // You can add some notification to the user here
                console.log(response); // For debugging purposes
            },
            error: function(xhr, status, error){
                console.error(error); // For debugging purposes
            }
        });
    });


});
$('.map-hover').hover(function() {
    var location = $(this).data('location');
    var mapLocation = $(this).data('map-location').split(',');
        if (location === 'Shopping District') {
            mapImage = 'sd.png';
        } else if (location === 'End') {
            mapImage = 'end.png';
        } else {
            return;
        }

    // Set the coordinates as data attributes
    var tooltipContent = $('<div class="tooltip-map"><img src="' + mapImage + '" data-original-x="' + mapLocation[0] + '" data-original-y="' + mapLocation[1] + '" onload="positionDot(this)"></div>');
    $(this).append(tooltipContent);
    tooltipContent.show();
}, function() {
    $('.tooltip-map').remove();
});

function positionDotOnMinecraftMap(imgElement, minecraftX, minecraftY, corner1, corner2) {
    // corner1 and corner2 are objects with x and y properties representing the coordinates
    // of the top-left and bottom-right corners of the map in Minecraft coordinates

    var currentWidth = imgElement.width;
    var currentHeight = imgElement.height;

    // Calculate the width and height of the Minecraft map area using the corners
    var minecraftMapWidth = Math.abs(corner2.x - corner1.x);
    var minecraftMapHeight = Math.abs(corner2.y - corner1.y);

    // Calculate the position of the dot relative to the top-left corner of the Minecraft map
    var relativeX = minecraftX - corner1.x;
    var relativeY = minecraftY - corner1.y;

    // Calculate scaling factors based on the current image size and the Minecraft map size
    var scaleX = currentWidth / minecraftMapWidth;
    var scaleY = currentHeight / minecraftMapHeight;

    // Calculate new position for the dot on the image
    var newX = relativeX * scaleX;
    var newY = relativeY * scaleY;

    // Create and position the dot
    var dot = $('<div class="map-dot"></div>');
    dot.css({
        position: 'absolute',
        top: newY + 'px',
        left: newX + 'px',
        width: '10px',
        height: '10px',
        borderRadius: '50%',
        backgroundColor: 'red',
        transform: 'translate(-50%, -50%)' // Center the dot on the exact location
    });

    // Append the dot to the parent of the imgElement
    $(imgElement).parent().append(dot);
}
</script>
