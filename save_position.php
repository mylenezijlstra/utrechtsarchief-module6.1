<?php
include "./includes/localhostverbinding.php";

$data = json_decode(file_get_contents("php://input"), true);
$name = $conn->real_escape_string($data['name']);
$x = intval($data['x']);
$y = intval($data['y']);

$sql = "UPDATE panorama SET x=$x, y=$y WHERE afbeeldingen='$name'";
if ($conn->query($sql) === TRUE) {
  echo "Positie opgeslagen";
} else {
  echo "Fout: " . $conn->error;
}
$conn->close();
?>
