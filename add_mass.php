<!DOCTYPE html>
<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}?>
<html lang="en">
<head>
	<?php include 'navbar.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer Submission</title>
</head>
<body>
<div class="container mt-5">
    <h2>Mass Submit Offers</h2>
    <form id="massOfferForm" class="mt-4" action="src-a/submit_mass_offers.php" method="POST">
	    <!-- Button to add a new item row -->
        <button type="button" id="addItem" class="btn btn-secondary">Add Item</button>

        <!-- Submit button -->
        <button type="submit" class="btn btn-primary">Submit Offers</button>
        <!-- Seller -->
<!-- Seller -->
		<div class="mb-3">
			<label for="sellerName" class="form-label">Seller</label>
			<input type="text" class="form-control" id="sellerName" name="seller_name" required>
			<div id="sellerSuggestions" class="list-group"></div> <!-- Container for suggestions -->
		</div>

<!-- Shop Selector -->
<div class="mb-3">
            <label for="shopName" class="form-label">Shop Name</label>
            <select class="form-select" id="shopName" name="shop_name">
                <option selected>Select a seller first...</option>
            </select>
</div>


        <!-- Container for dynamic item rows -->
        <div id="itemRows"></div>


    </form>
</div>

<script>
$(document).ready(function() {
    var itemCount = 1; // Start with 1 to keep track of how many items have been added
    var debounceTimer;
    $('#sellerName').on('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            var sellerName = $(this).val();
            if (sellerName.length > 0) { // Check if seller name input is not empty
                $.ajax({
                    url: 'src-a/get_shops_by_seller.php', // The PHP script that returns shops for a given seller
                    type: 'POST',
                    dataType: 'json', // Expect a JSON response
                    data: {sellerName: sellerName}, // Match the key expected by your PHP script
                    success: function(data) {
                        $('#shopId').empty(); // Clear the existing options
                        $('#shopId').append($('<option>', {
                            value: '',
                            text: 'Select a shop...'
                        }));
                        $.each(data, function(i, shop) {
                            $('#shopId').append($('<option>', { 
                                value: shop.name, // Use shop.name, as your PHP script returns shop names
                                text: shop.name // The text to show in the dropdown
                            }));
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("An error occurred: " + error);
                    }
                });
            } else {
                $('#shopId').empty(); // Clear the shop selector if seller name is cleared
            }
        }, 300); // Delay the execution to prevent too many requests
    });
    // Function to add a new item row
    function addItemRow() {
        var row = `
            <div class="row g-3 mb-3" id="itemRow${itemCount}">
                <div class="col">
                    <label for="itemName${itemCount}" class="form-label">Item</label>
                    <input type="text" class="form-control" id="itemName${itemCount}" name="item_name[]" required>
                </div>
                <div class="col">
                    <label for="itemQuantity${itemCount}" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="itemQuantity${itemCount}" name="quantity[]" min="0" required>
                </div>
                <div class="col">
                    <label for="itemPrice${itemCount}" class="form-label">Price (Diamonds)</label>
                    <input type="number" class="form-control" id="itemPrice${itemCount}" name="price[]" min="0" required>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-danger removeItem" data-id="${itemCount}">Remove</button>
                </div>
            </div>
        `;
        $('#itemRows').append(row); // Append the new row to the container
        itemCount++; // Increment the item counter
    }

    // Initially add one row
    addItemRow();

    // Event handler for adding more item rows
    $('#addItem').click(function() {
        addItemRow();
    });

    // Event handler for removing an item row
    $('body').on('click', '.removeItem', function() {
        var id = $(this).data('id'); // Get the id of the row to be removed
        $('#itemRow' + id).remove(); // Remove the row
    });
});

</script>



<script>
    $(document).ready(function() {
	var debounceTimeout;
    $("#sellerName").on("input", function(){
		clearTimeout(debounceTimeout);
		debounceTimeout = setTimeout(() => {
		console.log("Input event triggered");
        var inputVal = $(this).val();
		console.log("Searching for:", inputVal); //
        $.ajax({
            url: "src-a/get_sellers.php",
            type: "POST",
            data: { searchTerm: inputVal },
            success: function(data){
				console.log("Received data:", data);
                $("#sellerSuggestions").empty(); // Clear previous suggestions
                if(data.length > 0) {
                    // Assuming 'data' is an array of seller names
                    $.each(JSON.parse(data), function(index, owner) {
                        // Create a new suggestion item and append it to the suggestions container
						$("#sellerSuggestions").append(`<a href="#" class="list-group-item list-group-item-action" data-seller="${owner.name}">${owner.name}</a>`);
                    });

                    // Make the suggestions clickable
                    $("#sellerSuggestions a").on("click", function() {
						var selectedSeller = $(this).data("seller");
                        $("#sellerName").val($(this).data("seller")); // Set the input value to the selected suggestion
                        $("#sellerSuggestions").empty(); // Clear suggestions
						loadShopsForSeller(selectedSeller); // Load shops for the selected seller

                    });
                }
            }
        });
		}, 100);
    });
	function loadShopsForSeller(selectedSeller) {
    console.log("Loading shops for seller:", selectedSeller);
    $.ajax({
        url: "src-a/get_shops_by_seller.php",
        type: "POST",
        data: { sellerName: selectedSeller },
        dataType: "json",
        success: function(data) {
            $("#shopName").empty();
            if(data && data.length > 0) {
                $.each(data, function(index, shop) {
                    $("#shopName").append(new Option(shop.name, shop.name));
                });
            } else {
                $("#shopName").append(new Option("No shops found for this seller", ""));
            }
        }
    });
}

    });
	
</script>

</body>
</html>
