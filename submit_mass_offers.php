<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}?>
<?php
include 'config.php';
// Database connection
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Extracting and sanitizing common data
$seller_name = $conn->real_escape_string($_POST['seller_name']);
$shopName = $conn->real_escape_string($_POST['shop_name']);


// Get or create owner_id and shop_id
$owner_id = getOrCreateOwnerId($conn, $seller_name); // Reuse the existing function with seller_name
$shop_id = getShopIdByNameAndOwner($conn, $shopName, $owner_id);

// Iterate over each item submitted
// Iterate over each item submitted
for ($i = 0; $i < count($_POST['item_name']); $i++) {
    $item_name = $conn->real_escape_string($_POST['item_name'][$i]);
    $quantity = intval($_POST['quantity'][$i]);
    $price = intval($_POST['price'][$i]); // Assuming price is submitted directly without need for calculation

    // Default values for item_description and type, as they are not included in the mass submission form
    $item_description = ""; // Left blank in mass submission
    $type = "Item"; // Default type for mass submission

    // SQL to insert the new offer, now directly using the provided shop_id
    $sql = "INSERT INTO offer (shop_id, item, item_description, type, quantity, price, in_stock) VALUES ('$shop_id', '$item_name', '$item_description', '$type', '$quantity', '$price', 1)";

    // Execute the query
    if (!$conn->query($sql)) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
header("Location: add_mass.php");
exit;
// Functions to handle owner and shop logic
function getOrCreateOwnerId($conn, $owner_name) {
    // Check if the owner already exists
    $owner_sql = "SELECT owner_id FROM owner WHERE name = '$owner_name'";
    $result = $conn->query($owner_sql);

    if ($result->num_rows > 0) {
        // Owner exists, fetch the owner_id
        $row = $result->fetch_assoc();
        return $row['owner_id'];
    } else {
        // Owner does not exist, create a new one
        $insert_sql = "INSERT INTO owner (name) VALUES ('$owner_name')";
        if ($conn->query($insert_sql) === TRUE) {
            // Return the new owner_id
            return $conn->insert_id;
        } else {
            die("Error creating new owner: " . $conn->error);
        }
    }
}
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

$conn->close();

?>

