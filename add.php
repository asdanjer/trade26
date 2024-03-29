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
<div id="successMessage" class="alert alert-success d-none" role="alert"></div>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offer Submission</title>
</head>
<body>
<div class="container mt-5">
    <h2>Submit New Offer</h2>
    <form id="offerForm" class="mt-4">
		<div class="mb-3">
			<label for="sellerName" class="form-label">Seller</label>
			<input type="text" class="form-control" id="sellerName" name="seller_name" required>
			<div id="sellerSuggestions" class="list-group"></div> <!-- Container for suggestions -->
		</div>

        <!-- Shop Name dropdown -->
        <div class="mb-3">
            <label for="shopName" class="form-label">Shop Name</label>
            <select class="form-select" id="shopName" name="shop_name">
                <option selected>Select a seller first...</option>
            </select>
        <div class="mb-3">
            <label for="itemName" class="form-label">Item</label>
            <input type="text" class="form-control" id="itemName" name="item_name" required>
            <div id="itemSuggestions" class="list-group"></div>

        </div>
        <div class="mb-3">
            <label for="itemDescription" class="form-label">Item Description (Optional)</label>
            <textarea class="form-control" id="itemDescription" name="item_description"></textarea>
        </div>
        <div class="row g-3 mb-3">
            <div class="col">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" required>
            </div>
            <div class="col">
                <label for="type" class="form-label">Type</label>
                <select class="form-select" id="type" name="type">
                    <option value="Item">Item</option>
                    <option value="Shulker Box">Shulker Box</option>
                </select>
            </div>
        </div>
        <div class="row g-3 mb-3">
            <div class="col">
                <label for="diamondBlocks" class="form-label">Diamond Blocks</label>
                <input type="number" class="form-control" id="diamondBlocks" name="diamond_blocks" min="0">
            </div>
            <div class="col">
                <label for="diamonds" class="form-label">Diamonds</label>
                <input type="number" class="form-control" id="diamonds" name="diamonds" min="0">
            </div>
            <div class="col">
                <label for="lerokkoCoins" class="form-label">Lerokko Coins</label>
                <input type="number" class="form-control" id="lerokkoCoins" name="lerokko_coins" min="0">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Submit Offer</button>
    </form>
</div>

<script>
$(document).ready(function(){
	var debounceTimeout;
    $("#sellerName").on("input", function(){
		clearTimeout(debounceTimeout);
		debounceTimeout = setTimeout(() => {
        var inputVal = $(this).val();
        $.ajax({
            url: "src-a/get_sellers.php",
            type: "POST",
            data: { searchTerm: inputVal },
            success: function(data){
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

        $('#offerForm').submit(function(e) {
            e.preventDefault(); // Prevent the default form submission
            var formData = $(this).serialize(); // Serialize form data

            $.ajax({
                type: "POST",
                url: "src-a/submit_offer.php", // Your PHP processing file path
                data: formData,
                success: function(response) {
                    // Handle success (e.g., display a success message)
					$('#successMessage').text("Offer successfully added").removeClass('d-none');
					setTimeout(function() {
						$('#successMessage').addClass('d-none');
					}, 5000);
                    // Optionally, clear the form or take any other necessary action
                    $('#offerForm')[0].reset(); // Reset form fields
                },
                error: function() {
                    // Handle error
                    alert("There was an error submitting the offer.");
                }
            });
        });
        $("#itemName").on("input", function() {
    clearTimeout(debounceTimeout); // Reuse the existing debounceTimeout variable
    debounceTimeout = setTimeout(() => {
        var inputVal = $(this).val();
        $.ajax({
            url: "src-a/get_items.php", // PHP script to fetch item names
            type: "POST",
            dataType: "json", // This ensures the response is parsed as JSON automatically
            data: { searchTerm: inputVal },
            success: function(data) {
                $("#itemSuggestions").empty(); // Clear previous suggestions
                if (data.length > 0) {
                    // No need to parse 'data' as it's already a JavaScript object/array
                    $.each(data, function(index, item) {
                        // Append each suggestion to the suggestions container
                        $("#itemSuggestions").append(`<a href="#" class="list-group-item list-group-item-action" data-item="${item.name}">${item.name}</a>`);
                    });

                    // Make the suggestions clickable
                    $("#itemSuggestions a").on("click", function() {
                        $("#itemName").val($(this).data("item")); // Set the input value to the selected suggestion
                        $("#itemSuggestions").empty(); // Clear suggestions
                    });
                }
            }
        });
    }, 100); // Adjust debounce delay
});
$("#itemName").on("blur", function() {
    setTimeout(function() {
        $("#itemSuggestions").empty(); // Clear suggestions with a slight delay
    }, 200); // Delay to allow click event on suggestions to be processed
});

    });
</script>

</body>
</html>
