<?php
// update_colofon.php

// Databaseverbinding
$host = "localhost";
$user = "root";
$pass = "";
$db   = "utrechtsarchief";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Controleer of formulier is verzonden
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = $_POST['title'];
    $content = $_POST['content'];

    // Voeg nieuwe versie toe
    $stmt = $conn->prepare("INSERT INTO colofon (title, content) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $content);
    $stmt->execute();
    $stmt->close();

    // Terug naar adminpagina
    header("Location: colofonadmin.php");
    exit;
}
?>
