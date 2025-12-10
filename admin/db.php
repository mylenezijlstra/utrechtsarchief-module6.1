<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username   = "u240381_utrechtsarchief";
$password   = "dauB4hsUwxpcHkZdMGTd";
$dbname     = "u240381_utrechtsarchief";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

define("WEBSITEROOT", "/utrechtsarchief");