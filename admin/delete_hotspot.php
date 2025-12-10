<?php
// delete_hotspot.php
session_start();
if (empty($_SESSION['logged_in'])) {
    header("Location: ".WEBSITEROOT."/admin/login.php");
    exit;
}

include __DIR__ . '/db.php';

if (!isset($_POST['image_id'])) {
    die("Geen image_id opgegeven");
}

$image_id = (int)$_POST['image_id'];

// Verwijder hoofd-hotspot (indien aanwezig)
$stmt = $conn->prepare("DELETE FROM hotspots WHERE image_id = ?");
$stmt->bind_param("i", $image_id);
$stmt->execute();
$stmt->close();

// Verwijder eventuele extra hotspots die bij dit image horen
$stmt2 = $conn->prepare("DELETE FROM hotspot_extra WHERE hotspot_id = ?");
$stmt2->bind_param("i", $image_id);
$stmt2->execute();
$stmt2->close();

// Terug naar de adminpagina
header("Location: ".WEBSITEROOT."/admin/admin.php");
exit;

?>