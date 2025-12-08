<?php
session_start();
header('Content-Type: application/json');
if (empty($_SESSION['logged_in'])) {
    http_response_code(403);
    echo json_encode(['success'=>false,'error'=>'Not logged in']);
    exit;
}
include __DIR__ . '/db.php';

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['id'])) {
    echo json_encode(['success'=>false,'error'=>'Missing id']);
    exit;
}

$id = (int)$data['id'];
$action = $data['action'] ?? 'update';
$type = $data['type'] ?? null;

try {
    if ($action === 'delete' && $type) {
        if ($type === 'desc') {
            $stmt = $conn->prepare("UPDATE hotspots SET pos_top=NULL, pos_left=NULL, description_nl='', description_en='' WHERE image_id=?");
        } elseif ($type === 'remark') {
            $stmt = $conn->prepare("UPDATE hotspots SET remark_top=NULL, remark_left=NULL, remark_nl='', remark_en='' WHERE image_id=?");
        } elseif ($type === 'extra') {
            $stmt = $conn->prepare("UPDATE hotspots SET extra_top=NULL, extra_left=NULL, extra_info_nl='', extra_info_en='', extra_image='' WHERE image_id=?");
        } else {
            throw new Exception('Unknown type for delete');
        }
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        echo json_encode(['success'=>(bool)$ok]);
        exit;
    }

    // update or insert
    $stmt = $conn->prepare("SELECT * FROM hotspots WHERE image_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $existing = $res ? $res->fetch_assoc() : null;
    $stmt->close();

    // merge incoming with existing
    $pos_top      = array_key_exists('desc_top', $data)   ? (int)$data['desc_top']   : ($existing['pos_top'] ?? 20);
    $pos_left     = array_key_exists('desc_left', $data)  ? (int)$data['desc_left']  : ($existing['pos_left'] ?? 20);

    $remark_top   = array_key_exists('remark_top', $data) ? (int)$data['remark_top'] : ($existing['remark_top'] ?? null);
    $remark_left  = array_key_exists('remark_left', $data)? (int)$data['remark_left']: ($existing['remark_left'] ?? null);

    $extra_top    = array_key_exists('extra_top', $data)  ? (int)$data['extra_top']  : ($existing['extra_top'] ?? null);
    $extra_left   = array_key_exists('extra_left', $data) ? (int)$data['extra_left'] : ($existing['extra_left'] ?? null);

    $desc_nl       = array_key_exists('description_nl', $data) ? $data['description_nl'] : ($existing['description_nl'] ?? '');
    $desc_en       = array_key_exists('description_en', $data) ? $data['description_en'] : ($existing['description_en'] ?? '');
    $remark_nl     = array_key_exists('remark_nl', $data) ? $data['remark_nl'] : ($existing['remark_nl'] ?? '');
    $remark_en     = array_key_exists('remark_en', $data) ? $data['remark_en'] : ($existing['remark_en'] ?? '');
    $extra_info_nl = array_key_exists('extra_info_nl', $data) ? $data['extra_info_nl'] : ($existing['extra_info_nl'] ?? '');
    $extra_info_en = array_key_exists('extra_info_en', $data) ? $data['extra_info_en'] : ($existing['extra_info_en'] ?? '');
    $extra_image   = array_key_exists('extra_image', $data) ? $data['extra_image'] : ($existing['extra_image'] ?? '');

    if ($existing) {
        $stmt = $conn->prepare("UPDATE hotspots SET 
            pos_top=?, pos_left=?, description_nl=?, description_en=?,
            remark_top=?, remark_left=?, remark_nl=?, remark_en=?,
            extra_top=?, extra_left=?, extra_info_nl=?, extra_info_en=?, extra_image=?
            WHERE image_id=?");
        $stmt->bind_param("iissiissiisssi",
            $pos_top, $pos_left, $desc_nl, $desc_en,
            $remark_top, $remark_left, $remark_nl, $remark_en,
            $extra_top, $extra_left, $extra_info_nl, $extra_info_en, $extra_image,
            $id
        );
        $ok = $stmt->execute();
        $stmt->close();
        echo json_encode(['success'=>(bool)$ok]);
        exit;
    } else {
        $stmt = $conn->prepare("INSERT INTO hotspots (
            image_id, pos_top, pos_left, description_nl, description_en,
            remark_top, remark_left, remark_nl, remark_en,
            extra_top, extra_left, extra_info_nl, extra_info_en, extra_image
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiissiissiisss",
            $id, $pos_top, $pos_left, $desc_nl, $desc_en,
            $remark_top, $remark_left, $remark_nl, $remark_en,
            $extra_top, $extra_left, $extra_info_nl, $extra_info_en, $extra_image
        );
        $ok = $stmt->execute();
        $stmt->close();
        echo json_encode(['success'=>(bool)$ok]);
        exit;
    }

} catch (Exception $e) {
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
    exit;
}
