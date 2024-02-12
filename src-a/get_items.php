<?php
// Database connection
include 'config.php';
try {
    $conn = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed!";
    exit;
}

$searchTerm = $_POST['searchTerm'] ?? '';

// Query to search for items
$query = "SELECT name FROM items WHERE name LIKE :searchTerm LIMIT 5"; // Adjust table and column names as needed
$stmt = $conn->prepare($query);
$searchTerm = "%$searchTerm%";
// Bind parameter
$stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
// Execute the statement
$stmt->execute();

$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return the results as JSON
header('Content-Type: application/json');
echo json_encode($items);
?>
