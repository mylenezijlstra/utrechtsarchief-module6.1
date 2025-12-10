<?php
// delete_hotspot.php
session_start();

if (empty($_SESSION['logged_in'])) {
    header("Location: " . WEBSITEROOT . "/admin/login.php");
    exit;
}

include __DIR__ . '/db.php';

if (!isset($_POST['image_id'])) {
    die("Geen image_id opgegeven");
}

$image_id = (int) $_POST['image_id'];

// --- TRANSACTION START ---
$conn->begin_transaction();

try {

    // 1. Haal alle hotspot IDs op die bij dit image horen
    $stmt = $conn->prepare("SELECT id FROM hotspots WHERE image_id = ?");
    $stmt->bind_param("i", $image_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $hotspot_ids = [];
    while ($row = $result->fetch_assoc()) {
        $hotspot_ids[] = (int)$row['id'];
    }
    $stmt->close();

    // 2. Verwijder extra hotspots horend bij elke hotspot_id
    if (!empty($hotspot_ids)) {
        $stmt2 = $conn->prepare("DELETE FROM hotspot_extra WHERE hotspot_id = ?");

        foreach ($hotspot_ids as $hid) {
            $stmt2->bind_param("i", $hid);
            $stmt2->execute();
        }

        $stmt2->close();
    }

    // 3. Verwijder hoofd-hotspots zelf
    $stmt3 = $conn->prepare("DELETE FROM hotspots WHERE image_id = ?");
    $stmt3->bind_param("i", $image_id);
    $stmt3->execute();
    $stmt3->close();

    // 4. Alles OK → commit
    $conn->commit();

} catch (Exception $e) {

    // Fout → rollback
    $conn->rollback();
    die("Er is een fout opgetreden bij het verwijderen van hotspots: " . $e->getMessage());
}

// Terug naar adminpagina
header("Location: " . WEBSITEROOT . "/admin/admin.php");
exit;

?>
