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
echo "<table class='table table-striped' id='internalTable'><thead><tr><th>Item</th><th>Description</th><th>Quantity</th><th>Price</th><th>Address</th><th>Location</th><th>Seller</th><th>In Stock</th></tr></thead><tbody>";

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
<td class='map-hover' data-latitude='".htmlspecialchars($row["latitude"])."' data-longitude='".htmlspecialchars($row["longitude"])."' data-location='".htmlspecialchars(strtolower($row["location"]))."'>".htmlspecialchars($row["address"])."</td>
<td class='map-hover' data-latitude='".htmlspecialchars($row["latitude"])."' data-longitude='".htmlspecialchars($row["longitude"])."' data-location='".htmlspecialchars(strtolower($row["location"]))."'>".htmlspecialchars($row["location"])."</td>
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

</style>

<script>
$(document).ready(function(){
    $('.stock-toggle').change(function(){
        var offerId = $(this).data('offer-id');
        var inStock = $(this).is(':checked') ? 1 : 0;
        
        $.ajax({
            url: 'src-a/update_stock_status.php',
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
</script>
