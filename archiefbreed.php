<?php
require_once './includes/db.php';
$lang = $_COOKIE['lang'] ?? 'nl';

function h($v)
{
  return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}
function safeInt($v)
{
  return ($v === null || $v === '') ? '' : (int)$v;
}
function t($nl, $en, $lang)
{
  return $lang === 'en' ? $en : $nl;
}

// Zet hier je projectmap base
$BASE = WEBSITEROOT;

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
  <title><?php echo t("Panorama", "Panorama", $lang); ?></title>
  <link rel="stylesheet" href="./assets/css/style.css">
  <style>
    .info-box .info-content {
      display: flex;
      align-items: flex-start;
      gap: 12px;
    }

    .info-box .info-text {
      flex: 1;
    }

    .info-box .info-image img {
      max-width: 150px;
      height: auto;
      border-radius: 4px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }
  </style>
</head>

<body>
  <header>
    <?php include "includes/headerzoekbalk.php" ?>
  </header>
  <main>
    <div class="panorama-frame">
      <div class="panorama">
        <?php
        while ($row = $result->fetch_assoc()) {
          $filename = $row['filename'];
          if (empty($filename)) continue;
          $imageId = (int) str_replace('.jpg', '', $filename);
          $imgPath = $BASE . '/assets/img/' . $filename;

          echo '<div class="image-wrapper" data-id="' . h($imageId) . '">';
          echo '<img src="' . h($imgPath) . '" alt="Panorama ' . h($imageId) . '">';

          // ★ Beschrijving-hotspot
          $descTopPx = safeInt($row['pos_top']);
          $descLeftPx = safeInt($row['pos_left']);
          if ($descTopPx !== '' && $descLeftPx !== '') {
            $descId = "desc-" . $imageId;
            $desc = ($lang === 'en') ? $row['description_en'] : $row['description_nl'];

            echo '<div class="hotspot hotspot-desc" data-target="' . h($descId) . '" 
          style="top:' . h($descTopPx) . 'px; left:' . h($descLeftPx) . 'px;">●</div>';

            echo '<div class="info-box" id="' . h($descId) . '" hidden>' . h($desc) . '</div>';
          }

          // ★ Remark hotspot – CORRECT HERSTELD
          $remarkTopPx = safeInt($row['remark_top']);
          $remarkLeftPx = safeInt($row['remark_left']);
          if ($remarkTopPx !== '' && $remarkLeftPx !== '') {
            $remarkId = "remark-" . $imageId;
            $remark = ($lang === 'en') ? $row['remark_en'] : $row['remark_nl'];

            echo '<div class="hotspot hotspot-remark" data-target="' . h($remarkId) . '"
          style="top:' . h($remarkTopPx) . 'px; left:' . h($remarkLeftPx) . 'px;">●</div>';

            echo '<div class="info-box" id="' . h($remarkId) . '" hidden>' . h($remark) . '</div>';
          }

          // ★ EXTRA HOTSPOTS (blijft hetzelfde, maar nu binnen de while!)
          $stmtExtra = $conn->prepare("SELECT id, pos_top, pos_left, info_nl, info_en, image 
                               FROM hotspot_extra WHERE hotspot_id = ?");
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

                echo '<div class="hotspot hotspot-extra" data-target="' . h($extraId) . '"
              style="top:' . h($extraTopPx) . 'px; left:' . h($extraLeftPx) . 'px;">●</div>';

                echo '<div class="info-box" id="' . h($extraId) . '" hidden>
  <div class="info-content">
    <div class="info-text">' . h($extraInfo) . '</div>';


                if (!empty($extra['image'])) {
                  $extraImg = $BASE . '/admin/assets/img/' . $extra['image'];
                  echo '<div class="info-image"><img src="' . h($extraImg) . '" alt="Extra image"></div>';
                }

                echo '  </div>
              </div>';
              }
            }
            $stmtExtra->close();
          }

          // ★ Fake hotspot voor foto 21
          if ($imageId === 21) {
            echo '<a href="spel.php" class="hotspot hotspot-fake"
           style="position:absolute; top:40%; left:25%;
           transform:translate(-50%, -100%) rotate(315deg); 
           width:44px; height:44px;
           background:#e8dbc4; border:4px solid #8c7455;
           border-radius:50% 50% 50% 0;
           display:flex; align-items:center; justify-content:center;
           color:#6b5334; font-weight:800; font-size:18px; text-decoration:none;">●</a>';
          }

          echo '</div>'; // EINDE image-wrapper
        }
        ?>




        ?>
      </div>
    </div>

    <!-- Mini-map -->
    <div class="mini-map" aria-hidden="false">
      <div class="mini-inner">
        <div class="mini-track" aria-hidden="true">
          <div class="mini-highlight"></div>
        </div>
      </div>
    </div>
  </main>

  <script src="./assets/js/panorama.js"></script>

  <footer>
    <?php include "includes/footer.php" ?>
  </footer>

</body>

</html>