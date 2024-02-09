<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}?>
<?php
// Database connection
include 'config.php';
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to get or create owner ID
function getOrCreateOwnerId($conn, $ownerName) {
    $ownerNameLower = strtolower($ownerName); // Convert to lowercase
    $owner_sql = "SELECT owner_id FROM owner WHERE LOWER(name) = '$ownerNameLower'";
    $result = $conn->query($owner_sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['owner_id'];
    } else {
        $insert_sql = "INSERT INTO owner (name) VALUES ('$ownerNameLower')";
        if ($conn->query($insert_sql) === TRUE) {
            return $conn->insert_id;
        } else {
            die("Error creating new owner: " . $conn->error);
        }
    }
}

// Extracting form data
$name = $conn->real_escape_string($_POST['name']);
$location = $conn->real_escape_string($_POST['location']);
$address = $conn->real_escape_string($_POST['address']);
$map_location_x = $conn->real_escape_string($_POST['map_location_x']);
$map_location_y = $conn->real_escape_string($_POST['map_location_y']);
$ownerName = $conn->real_escape_string($_POST['owner_name']);

// Constructing point for map_location
$map_location = "POINT($map_location_x $map_location_y)";

// Handle the owner logic
$owner_id = getOrCreateOwnerId($conn, $ownerName);

// SQL to insert the new shop with owner reference
$sql = "INSERT INTO shop (name, location, address, map_location, owner_id) VALUES ('$name', '$location', '$address', ST_PointFromText('$map_location'), '$owner_id')";

if ($conn->query($sql) === TRUE) {
    $_SESSION['success_message'] = "New shop added successfully";
    header("Location: add_shop.php");
    exit;
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
