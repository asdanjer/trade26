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
    <title>Add Shop</title>
</head>
<body>
<?php
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success" role="alert">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']);
}
?>

    <div class="container">
        <h2>Add New Shop</h2>
        <form action="src-a/insert_shop.php" method="post">
            <div class="mb-3">
                <label for="shopName" class="form-label">Shop Name</label>
                <input type="text" class="form-control" id="shopName" name="name" required>
            </div>
            <div class="mb-3">
                <label for="shopLocation" class="form-label">Location</label>
                <select class="form-select" id="shopLocation" name="location" required>
                    <option value="Shopping District">Shopping District</option>
                    <option value="End">End</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="shopAddress" class="form-label">Address</label>
                <input type="text" class="form-control" id="shopAddress" name="address" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Map Location</label>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="X Coordinate" name="map_location_x" >
                    <input type="text" class="form-control" placeholder="Y Coordinate" name="map_location_y" >
                </div>
            </div>
			<!-- Inside the <form> element, add the following field for the owner's name -->
<div class="mb-3">
    <label for="ownerName" class="form-label">Owner's Name (Pls use exact Minecraft username of main account)</label>
    <input type="text" class="form-control" id="ownerName" name="owner_name" required>
</div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</body>
</html>
