<?php
require_once './includes/db.php';

// taalkeuze ophalen
$lang = $_COOKIE['lang'] ?? 'nl';

// functie om pixelwaarden naar percentages om te zetten
function toPercent($value, $total) {
    if ($total > 0 && $value !== null && $value !== '') {
        return round(($value / $total) * 100, 2);
    }
    return null;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
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
        $result = $conn->query("
            SELECT p.filename, h.pos_top, h.pos_left, h.description_nl, h.description_en
            FROM panorama p
            LEFT JOIN hotspots h 
              ON CAST(REPLACE(p.filename, '.jpg', '') AS UNSIGNED) = h.image_id
            ORDER BY CAST(REPLACE(p.filename, '.jpg', '') AS UNSIGNED)
        ");

        $count = 1;
        while ($row = $result->fetch_assoc()) {
          $desc = ($lang === 'en') ? ($row['description_en'] ?? '') : ($row['description_nl'] ?? '');

          // originele afbeelding afmetingen ophalen
          $imgPath = './assets/img/' . $row['filename'];
          $size = @getimagesize($imgPath);
          $imgWidth = $size[0] ?? 0;
          $imgHeight = $size[1] ?? 0;

          // pixelwaarden omzetten naar percentages
          $topPercent = toPercent($row['pos_top'], $imgHeight);
          $leftPercent = toPercent($row['pos_left'], $imgWidth);

          // wrapper met CSS-variabelen
          echo '<div class="image-wrapper" style="--hotspot-top:' . $topPercent . '%; --hotspot-left:' . $leftPercent . '%;">';
          echo '<img src="' . $imgPath . '" alt="Panorama ' . $count . '">';

          if ($topPercent !== null && $leftPercent !== null) {
            // Hotspot (positie via CSS-variabelen)
            echo '<div class="hotspot">•</div>';

            if (!empty($desc)) {
              // Info-box (positie via CSS-variabelen)
              echo '<div class="info-box">';
              echo '<strong>' . ($lang === 'en' ? 'Description:' : 'Beschrijving:') . '</strong><br>' . htmlspecialchars($desc);
              echo '</div>';
            }
          }

          echo '</div>'; // sluit image-wrapper
          $count++;
        }
        ?>
      </div>

<!-- Mini-map onderin (alle 33 afbeeldingen uit DB) -->
<div class="mini-map">
  <?php
  $miniResult = $conn->query("
      SELECT filename 
      FROM panorama 
      ORDER BY CAST(REPLACE(filename, '.jpg', '') AS UNSIGNED) ASC
  ");
  while ($miniRow = $miniResult->fetch_assoc()) {
    $miniPath = './assets/img/' . $miniRow['filename'];
    echo '<img src="' . $miniPath . '" alt="Miniatuur panorama" class="mini-thumb">';
  }
  ?>
  <div class="mini-highlight"></div>
</div>

    </div>
  </main>
  <script src="./assets/js/panorama.js"></script>
</body>
</html>
