<?php
// save_hotspot_extra.php
header('Content-Type: application/json; charset=utf-8');
session_start();
include __DIR__ . '/db.php'; // verwacht: $conn = new mysqli(...)

$response = ['success' => false, 'error' => null];

try {
    if (!isset($conn) || !($conn instanceof mysqli)) {
        throw new Exception('Database connection ($conn) not found. Controleer db.php');
    }
    $conn->set_charset('utf8mb4');

    $action = strtolower($_POST['action'] ?? '');
    $hotspot_id = isset($_POST['hotspot_id']) ? (int)$_POST['hotspot_id'] : null;
    if (!$hotspot_id) throw new Exception('Missing hotspot_id');

    // Helper: upload bestand naar admin/assets/img
    function handleUpload($fieldName) {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        $uploadDir = __DIR__ . '/assets/img/'; // admin/assets/img
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        // unieke naam genereren om botsingen te voorkomen
        $ext = pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION);
        $fileName = 'extra_' . uniqid() . '.' . strtolower($ext);
        $targetFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $targetFile)) {
            return $fileName; // alleen bestandsnaam opslaan
        }
        return null;
    }

    if ($action === 'add') {
        $pos_top  = $_POST['pos_top'] !== '' ? (int)$_POST['pos_top'] : null;
        $pos_left = $_POST['pos_left'] !== '' ? (int)$_POST['pos_left'] : null;
        $info_nl  = $_POST['info_nl'] ?? null;
        $info_en  = $_POST['info_en'] ?? null;
        $image    = handleUpload('image');

        $stmt = $conn->prepare("INSERT INTO hotspot_extra (hotspot_id, pos_top, pos_left, info_nl, info_en, image) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) throw new Exception($conn->error);
        $stmt->bind_param("iiisss", $hotspot_id, $pos_top, $pos_left, $info_nl, $info_en, $image);
        if (!$stmt->execute()) {
            $response['error'] = $stmt->error;
        } else {
            $response['success'] = true;
            $response['insert_id'] = $stmt->insert_id;
        }
        $stmt->close();

    } elseif ($action === 'update') {
        if (empty($_POST['extra_id'])) throw new Exception('Missing extra_id for update');
        $extra_id = (int)$_POST['extra_id'];

        $pos_top  = $_POST['pos_top'] !== '' ? (int)$_POST['pos_top'] : null;
        $pos_left = $_POST['pos_left'] !== '' ? (int)$_POST['pos_left'] : null;
        $info_nl  = $_POST['info_nl'] ?? null;
        $info_en  = $_POST['info_en'] ?? null;
        $image    = handleUpload('image');

        $stmt = $conn->prepare("
            UPDATE hotspot_extra
            SET pos_top = COALESCE(?, pos_top),
                pos_left = COALESCE(?, pos_left),
                info_nl = COALESCE(?, info_nl),
                info_en = COALESCE(?, info_en),
                image = COALESCE(?, image)
            WHERE id = ? AND hotspot_id = ?
        ");
        if (!$stmt) throw new Exception($conn->error);
        $stmt->bind_param("iisssii", $pos_top, $pos_left, $info_nl, $info_en, $image, $extra_id, $hotspot_id);
        if (!$stmt->execute()) {
            $response['error'] = $stmt->error;
        } else {
            $response['success'] = true;
            $response['affected_rows'] = $stmt->affected_rows;
        }
        $stmt->close();

    } elseif ($action === 'delete') {
        if (empty($_POST['extra_id'])) throw new Exception('Missing extra_id for delete');
        $extra_id = (int)$_POST['extra_id'];

        $stmt = $conn->prepare("DELETE FROM hotspot_extra WHERE id = ? AND hotspot_id = ?");
        if (!$stmt) throw new Exception($conn->error);
        $stmt->bind_param("ii", $extra_id, $hotspot_id);
        if (!$stmt->execute()) {
            $response['error'] = $stmt->error;
        } else {
            $response['success'] = true;
            $response['affected_rows'] = $stmt->affected_rows;
        }
        $stmt->close();

    } else {
        throw new Exception('Unknown action. Gebruik add, update of delete.');
    }

} catch (Exception $ex) {
    $response['error'] = $ex->getMessage();
}

echo json_encode($response);