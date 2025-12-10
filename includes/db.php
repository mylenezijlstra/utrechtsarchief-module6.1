<?php 
$servername = "localhost";
$username   = "u240381_utrechtsarchief";
$password   = "dauB4hsUwxpcHkZdMGTd";
$dbname     = "u240381_utrechtsarchief";

// Maak verbinding met de database
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer verbinding
if ($conn->connect_error) {
	die("Verbinding mislukt: " . $conn->connect_error);
}

define("WEBSITEROOT", "/utrechtsarchief");

?>