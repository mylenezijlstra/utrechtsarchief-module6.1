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

        // Gebruik array_key_exists om te detecteren of veld expliciet is meegegeven.
        // Als veld ontbreekt: laat het als NULL zodat COALESCE de oude waarde behoudt.
        $pos_top  = array_key_exists('pos_top', $data) && $data['pos_top'] !== '' ? (int)$data['pos_top'] : null;
        $pos_left = array_key_exists('pos_left', $data) && $data['pos_left'] !== '' ? (int)$data['pos_left'] : null;
        $info_nl  = array_key_exists('info_nl', $data) ? $data['info_nl'] : (array_key_exists('extra_info_nl', $data) ? $data['extra_info_nl'] : null);
        $info_en  = array_key_exists('info_en', $data) ? $data['info_en'] : (array_key_exists('extra_info_en', $data) ? $data['extra_info_en'] : null);
        $image    = array_key_exists('image', $data) ? $data['image'] : (array_key_exists('extra_image', $data) ? $data['extra_image'] : null);

        // Gebruik COALESCE zodat NULL parameters de bestaande DB-waarde behouden
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

        // volgorde bind_param: pos_top, pos_left, info_nl, info_en, image, extra_id, hotspot_id
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
