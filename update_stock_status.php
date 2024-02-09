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

$offerId = intval($_POST['offer_id']);
$inStock = intval($_POST['in_stock']);

$stmt = $conn->prepare("UPDATE offer SET in_stock = ? WHERE offer_id = ?");
$stmt->bind_param("ii", $inStock, $offerId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Stock status updated successfully";
} else {
    echo "Error updating record: " . $conn->error;
}

$conn->close();
?>
