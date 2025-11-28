<?php
require_once './includes/db.php';
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panorama</title>
  <link rel="stylesheet" href="./assets/css/style.css" />
</head>
<body>
  <main>
    <div class="panorama-frame">
      <div class="panorama">
        <?php
        // Haal bestandsnamen uit panorama-tabel en koppel hotspots
        $result = $conn->query("
            SELECT p.filename, h.pos_top, h.pos_left, h.description
            FROM panorama p
            LEFT JOIN hotspots h 
              ON CAST(REPLACE(p.filename, '.jpg', '') AS UNSIGNED) = h.image_id
            ORDER BY CAST(REPLACE(p.filename, '.jpg', '') AS UNSIGNED)
        ");

        $count = 1;
        while ($row = $result->fetch_assoc()) {
          echo '<div class="image-wrapper">';
          echo '<img src="./assets/img/' . $row['filename'] . '" alt="Panorama ' . $count . '">';
          
          // Hotspot + info-box samen
          if ($row['pos_top'] !== null && $row['pos_left'] !== null) {
            echo '<div class="hotspot" style="top:' . (int)$row['pos_top'] . 'px; left:' . (int)$row['pos_left'] . 'px;">•';
            
            if (!empty($row['description'])) {
              echo '<div class="info-box"><strong>Beschrijving:</strong><br>' . htmlspecialchars($row['description']) . '</div>';
            }
            
            echo '</div>'; // sluit hotspot
          }

          echo '</div>'; // sluit image-wrapper
          $count++;
        }
        ?>
      </div>
    </div>
  </main>
  <script src="./assets/js/panorama.js"></script>
</body>
</html>
