<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}?>
<?php
include 'config.php';
// Database connection - Adjust parameters as needed
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <nav class="navbar navbar-expand-lg navbar-light bg-light rounded mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Minecraft Trading</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">View Offers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="add.html">Submit New Offer</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>';
$conn = new mysqli('DB_SERVER', 'DB_USERNAME', 'DB_PASSWORD', 'DB_DATABASE');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Extracting form data
$sellerName = $conn->real_escape_string($_POST['seller_name']);
$shopName = $conn->real_escape_string($_POST['shop_name']);
$item_name = $conn->real_escape_string($_POST['item_name']);
$item_description = isset($_POST['item_description']) ? $conn->real_escape_string($_POST['item_description']) : '';
$quantity = intval($_POST['quantity']);
$type = $conn->real_escape_string($_POST['type']);
$diamond_blocks = intval($_POST['diamond_blocks']);
$diamonds = intval($_POST['diamonds']);
$lerokko_coins = intval($_POST['lerokko_coins']);
$in_stock = 1;


// Calculate price
$price = ($diamond_blocks * 9) + $diamonds + ($lerokko_coins / 10);


// Functions to handle owner and shop logic
function getOrCreateOwnerId($conn, $sellerName) {
    $sellerNameLower = strtolower($sellerName);
    $owner_sql = "SELECT owner_id FROM owner WHERE LOWER(name) = '$sellerNameLower'";
    $result = $conn->query($owner_sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['owner_id'];
    } else {
        $insert_sql = "INSERT INTO owner (name) VALUES ('$sellerName')";
        if ($conn->query($insert_sql) === TRUE) {
            return $conn->insert_id;
        } else {
            die("Error creating new owner: " . $conn->error);
        }
    }
}

// New function to fetch shop_id using shop name and owner_id
function getShopIdByNameAndOwner($conn, $shopName, $owner_id) {
    $shop_sql = "SELECT shop_id FROM shop WHERE name = '$shopName' AND owner_id = '$owner_id'";
    $result = $conn->query($shop_sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['shop_id'];
    } else {
        die("Error: Shop not found.");
    }
}

// Main logic
$owner_id = getOrCreateOwnerId($conn, $sellerName);
$shop_id = getShopIdByNameAndOwner($conn, $shopName, $owner_id);

// SQL to insert the new offer
$sql = "INSERT INTO offer (shop_id, item, item_description, type, quantity, price, in_stock) VALUES ('$shop_id', '$item_name', '$item_description', '$type', '$quantity', '$price', '$in_stock')";

if ($conn->query($sql) === TRUE) {
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

?>
