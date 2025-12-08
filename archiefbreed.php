<?php
require_once './includes/db.php';
$lang = $_COOKIE['lang'] ?? 'nl';

/**
 * Veilige helper: zet null altijd om naar een lege string
 */
function h($v) {
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

function safeInt($v) {
    return ($v === null || $v === '') ? '' : (int)$v;
}

$result = $conn->query("
    SELECT p.filename,
           h.pos_top, h.pos_left,
           h.description_nl, h.description_en,
           h.remark_top, h.remark_left,
           h.remark_nl, h.remark_en
    FROM panorama p
    LEFT JOIN hotspots h 
      ON CAST(REPLACE(p.filename, '.jpg', '') AS UNSIGNED) = h.image_id
    ORDER BY CAST(REPLACE(p.filename, '.jpg', '') AS UNSIGNED)
");
?>
<!DOCTYPE html>
<html lang="<?php echo h($lang); ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Panorama</title>
  <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
  <?php include './includes/header.php'; ?>

  <main>
    <div class="panorama-frame">
      <div class="panorama">
        <?php
        while ($row = $result->fetch_assoc()) {
          $filename = $row['filename'];
          if (empty($filename)) continue;
          $imageId = (int) str_replace('.jpg', '', $filename);
          $imgPath = './assets/img/' . $filename;

          echo '<div class="image-wrapper" data-id="' . h($imageId) . '">';
          echo '<img src="' . h($imgPath) . '" alt="Panorama ' . h($imageId) . '">';

          // Beschrijving
          $descTopPx = safeInt($row['pos_top']);
          $descLeftPx = safeInt($row['pos_left']);
          if ($descTopPx !== '' && $descLeftPx !== '') {
            $descId = "desc-" . $imageId;
            $desc = ($lang === 'en') ? $row['description_en'] : $row['description_nl'];
            echo '<div class="hotspot hotspot-desc" data-target="' . h($descId) . '" data-pos-top="' . h($descTopPx) . '" data-pos-left="' . h($descLeftPx) . '">●</div>';
            echo '<div class="info-box" id="' . h($descId) . '" hidden><strong>' . ($lang === 'en' ? 'Description:' : 'Beschrijving:') . '</strong><br>' . h($desc) . '</div>';
          }

          // Opmerking
          $remarkTopPx = safeInt($row['remark_top']);
          $remarkLeftPx = safeInt($row['remark_left']);
          if ($remarkTopPx !== '' && $remarkLeftPx !== '') {
            $remarkId = "remark-" . $imageId;
            $remark = ($lang === 'en') ? $row['remark_en'] : $row['remark_nl'];
            echo '<div class="hotspot hotspot-remark" data-target="' . h($remarkId) . '" data-pos-top="' . h($remarkTopPx) . '" data-pos-left="' . h($remarkLeftPx) . '">●</div>';
            echo '<div class="info-box" id="' . h($remarkId) . '" hidden><strong>' . ($lang === 'en' ? 'Remark:' : 'Opmerking:') . '</strong><br>' . h($remark) . '</div>';
          }

          // Extra hotspots
          $stmtExtra = $conn->prepare("SELECT id, pos_top, pos_left, info_nl, info_en, image FROM hotspot_extra WHERE hotspot_id = ?");
          if ($stmtExtra) {
            $stmtExtra->bind_param("i", $imageId);
            $stmtExtra->execute();
            $extraRes = $stmtExtra->get_result();
            while ($extra = $extraRes->fetch_assoc()) {
              $extraTopPx = safeInt($extra['pos_top']);
              $extraLeftPx = safeInt($extra['pos_left']);
              if ($extraTopPx !== '' && $extraLeftPx !== '') {
                $extraId = "extra-" . $imageId . "-" . (int)$extra['id'];
                $extraInfo = ($lang === 'en') ? $extra['info_en'] : $extra['info_nl'];
                echo '<div class="hotspot hotspot-extra" data-target="' . h($extraId) . '" data-pos-top="' . h($extraTopPx) . '" data-pos-left="' . h($extraLeftPx) . '">●</div>';
                echo '<div class="info-box" id="' . h($extraId) . '" hidden><strong>' . ($lang === 'en' ? 'Additional info:' : 'Aanvullende info:') . '</strong><br>' . h($extraInfo) . '</div>';
                if (!empty($extra['image'])) {
                  $extraImg = './assets/img/' . $extra['image'];
                  echo '<img src="' . h($extraImg) . '" alt="Extra afbeelding" class="extra-img">';
                }
              }
            }
            $stmtExtra->close();
          }

          echo '</div>'; // image-wrapper
        }
        ?>
      </div>
    </div>

    <!-- Mini-map buiten de frame -->
    <div class="mini-map" aria-hidden="false">
      <div class="mini-inner">
        <div class="mini-track" aria-hidden="true">
          <div class="mini-highlight"></div>
        </div>
    </div>
  </main>

  <script src="./assets/js/panorama.js"></script>

  <?php include './includes/footer.php'; ?>
</body>
</html>
