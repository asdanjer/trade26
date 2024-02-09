<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}?>
<?php
include 'config.php';

// Establish database connection
try {
$conn = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Get the sellerName from AJAX request
$sellerName = isset($_POST['sellerName']) ? $_POST['sellerName'] : '';
// Prepare SQL query to fetch shop names for the given seller using JOIN
$sql = "SELECT shop.name FROM shop JOIN owner ON shop.owner_id = owner.owner_id WHERE LOWER(owner.name) = LOWER(:sellerName)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':sellerName', $sellerName, PDO::PARAM_STR);

$stmt->execute();

// Fetch the shop names
$shops = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convert the result to JSON and output it
echo json_encode($shops);

// No need to explicitly close the connection when using PDO, as it will be closed automatically when the script ends
?>
