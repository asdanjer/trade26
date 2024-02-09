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
    $conn = new PDO("mysql:host=DB_SERVER;dbname=DB_DATABASE", DB_USERNAME, DB_PASSWORD);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Get the searchTerm from AJAX request
$searchTerm = isset($_POST['searchTerm']) ? $_POST['searchTerm'] : '';

// Prepare SQL query to fetch seller names case-insensitively
$sql = "SELECT DISTINCT name FROM owner WHERE LOWER(name) LIKE :searchTerm";
$stmt = $conn->prepare($sql);
$searchTerm = '%' . strtolower($searchTerm) . '%'; // Prepare the term for case-insensitive search
$stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);

$stmt->execute();

// Fetch the matching seller names
$sellers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convert the result to JSON and output it
echo json_encode($sellers);
