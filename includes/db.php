<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username   = "u240381_utrechtsarchief";
$password   = "dauB4hsUwxpcHkZdMGTd";
$dbname     = "u240381_utrechtsarchief";

// Maak verbinding met de database
$conn = new mysqli($servername, $username, $password, $dbname);
$conn -> set_charset("utf8");

// Controleer verbinding
if ($conn->connect_error) {
	die("Verbinding mislukt: " . $conn->connect_error);
}

define("WEBSITEROOT", "/utrechtsarchief");

?>