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
    <!-- Latest compiled and minified CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<?php include 'navbar.php'; ?>

</head>
    <div class="container mt-5">
        <h1 class="mb-4">Trade Offers</h1>
        <!-- Search form -->
        <form id="searchForm" class="mb-4">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Search Item" id="searchInput">
                <button class="btn btn-outline-secondary" type="submit">Filter</button>
            </div>
        </form>
		<div class="mb-3">
			<label for="currencyType" class="form-label">Display Prices In:</label>
			<select id="currencyType" class="form-select">
				<option value="diamonds">Diamonds</option>
				<option value="diamond_blocks">Diamond Blocks</option>
				<option value="lerokko_coins">Lerokko Coins</option>
			</select>
</div>
        <!-- Container where the table from search_offers.php will be loaded -->
        <div id="offersTableContainer"></div>
    </div>

<script>
$(document).ready(function() {
	function loadOffersTable(searchTerm) {
		var currencyType = $('#currencyType').val();
		$("#offersTableContainer").load(`search_offers.php?search_item=${encodeURIComponent(searchTerm)}&currency_type=${currencyType}`);
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
</script>
	
</body>
</html>
