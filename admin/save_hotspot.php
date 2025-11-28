<?php
session_start();
if (!isset($_SESSION['logged_in'])) { 
  http_response_code(403); 
  exit; 
}
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'], $data['pos_top'], $data['pos_left'])) {
  echo json_encode(['success' => false, 'error' => 'Missing fields']);
  exit;
}

$id = (int)$data['id'];
$pos_top = (int)$data['pos_top'];
$pos_left = (int)$data['pos_left'];

$stmt = $conn->prepare("UPDATE hotspots SET pos_top=?, pos_left=? WHERE image_id=?");
$stmt->bind_param("iii", $pos_top, $pos_left, $id);
$ok = $stmt->execute();

echo json_encode(['success' => $ok]);
