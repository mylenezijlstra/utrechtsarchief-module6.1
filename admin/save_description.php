<?php
session_start();
if (!isset($_SESSION['logged_in'])) { 
  http_response_code(403); 
  exit; 
}
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'], $data['text_nl'], $data['text_en'])) {
  echo json_encode(['success' => false, 'error' => 'Missing fields']);
  exit;
}

$id = (int)$data['id'];
$text_nl = $data['text_nl'];
$text_en = $data['text_en'];

$stmt = $conn->prepare("UPDATE hotspots SET description_nl=?, description_en=? WHERE image_id=?");
$stmt->bind_param("ssi", $text_nl, $text_en, $id);
$ok = $stmt->execute();

echo json_encode(['success' => $ok]);
