<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}?>
<?php
require   '../src/MinecraftQuery.php';
require   '../src/MinecraftQueryException.php';

use xPaw\MinecraftQuery;
use xPaw\MinecraftQueryException;

$Query = new MinecraftQuery();

try {
    $Query->Connect('play.server26.net', 25565);
    $players = $Query->GetPlayers();
    echo json_encode($players);
} catch (MinecraftQueryException $e) {
    echo(json_encode("Error fetching players!"));
}
?>
