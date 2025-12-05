<?php
// save_hotspot_extra.php
header('Content-Type: application/json; charset=utf-8');
session_start();
include __DIR__ . '/db.php'; // verwacht: $conn = new mysqli(...);

$response = ['success' => false, 'error' => null];

try {
    if (!isset($conn) || !($conn instanceof mysqli)) {
        throw new Exception('Database connection ($conn) not found. Controleer db.php');
    }
    $conn->set_charset('utf8mb4');

    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!is_array($data)) throw new Exception('Invalid JSON payload');

    $action = strtolower($data['action'] ?? '');
    $hotspot_id = isset($data['hotspot_id']) ? (int)$data['hotspot_id'] : null;
    if (!$hotspot_id) throw new Exception('Missing hotspot_id');

    if ($action === 'add') {
        $pos_top  = (isset($data['pos_top']) && $data['pos_top'] !== '') ? (int)$data['pos_top'] : null;
        $pos_left = (isset($data['pos_left']) && $data['pos_left'] !== '') ? (int)$data['pos_left'] : null;
        $info_nl  = (isset($data['info_nl']) && $data['info_nl'] !== '') ? $data['info_nl'] : null;
        $info_en  = (isset($data['info_en']) && $data['info_en'] !== '') ? $data['info_en'] : null;
        $image    = (isset($data['image']) && $data['image'] !== '') ? $data['image'] : null;

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
        if (empty($data['extra_id'])) throw new Exception('Missing extra_id for update');
        $extra_id = (int)$data['extra_id'];

        $pos_top  = (isset($data['pos_top']) && $data['pos_top'] !== '') ? (int)$data['pos_top'] : null;
        $pos_left = (isset($data['pos_left']) && $data['pos_left'] !== '') ? (int)$data['pos_left'] : null;
        $info_nl = isset($data['info_nl']) ? $data['info_nl'] : (isset($data['extra_info_nl']) ? $data['extra_info_nl'] : null);
        $info_en = isset($data['info_en']) ? $data['info_en'] : (isset($data['extra_info_en']) ? $data['extra_info_en'] : null);
        $image   = isset($data['image']) ? $data['image'] : (isset($data['extra_image']) ? $data['extra_image'] : null);

        $stmt = $conn->prepare("UPDATE hotspot_extra SET pos_top = ?, pos_left = ?, info_nl = ?, info_en = ?, image = ? WHERE id = ? AND hotspot_id = ?");
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
        if (empty($data['extra_id'])) throw new Exception('Missing extra_id for delete');
        $extra_id = (int)$data['extra_id'];

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
