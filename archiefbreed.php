<?php
require_once './includes/db.php';
$lang = $_COOKIE['lang'] ?? 'nl';

// Helper: zet px -> integer (maar we sturen nu px als data-attributes)
function safeInt($v)
{
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
<html lang="<?php echo htmlspecialchars($lang); ?>">

<head>
  <meta charset="utf-8">
  <title>Panorama</title>
  <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
  <header>
    <?php include "includes/header.php" ?>
  </header>
  <main>
    <div class="panorama-frame">
      <div class="panorama">
        <?php
        // We gebruiken imageId (uit bestandsnaam) als unieke identifier per paneel
        while ($row = $result->fetch_assoc()) {
          $filename = $row['filename'];
          $imageId = (int) str_replace('.jpg', '', $filename);
          $imgPath = './assets/img/' . $filename;

          echo '<div class="image-wrapper" data-id="' . htmlspecialchars($imageId) . '">';
          echo '<img src="' . htmlspecialchars($imgPath) . '" alt="Panorama ' . htmlspecialchars($imageId) . '">';

          // Beschrijving-hotspot (stuur ruwe px-waarden als data-attributes)
          $descTopPx = safeInt($row['pos_top']);
          $descLeftPx = safeInt($row['pos_left']);
          if ($descTopPx !== '' && $descLeftPx !== '') {
            $descId = "desc-" . $imageId;
            echo '<div class="hotspot hotspot-desc" data-target="' . htmlspecialchars($descId) . '" data-pos-top="' . htmlspecialchars($descTopPx) . '" data-pos-left="' . htmlspecialchars($descLeftPx) . '" style="--hotspot-top:0%; --hotspot-left:0%;">●</div>';
            $desc = ($lang === 'en') ? $row['description_en'] : $row['description_nl'];
            if (!empty($desc)) {
              echo '<div class="info-box" id="' . htmlspecialchars($descId) . '" hidden><strong>' . ($lang === 'en' ? 'Description:' : 'Beschrijving:') . '</strong><br>' . htmlspecialchars($desc) . '</div>';
            } else {
              echo '<div class="info-box" id="' . htmlspecialchars($descId) . '" hidden><strong>' . ($lang === 'en' ? 'Description:' : 'Beschrijving:') . '</strong><br></div>';
            }
          }

          // Opmerking-hotspot
          $remarkTopPx = safeInt($row['remark_top']);
          $remarkLeftPx = safeInt($row['remark_left']);
          if ($remarkTopPx !== '' && $remarkLeftPx !== '') {
            $remarkId = "remark-" . $imageId;
            echo '<div class="hotspot hotspot-remark" data-target="' . htmlspecialchars($remarkId) . '" data-pos-top="' . htmlspecialchars($remarkTopPx) . '" data-pos-left="' . htmlspecialchars($remarkLeftPx) . '" style="--hotspot-top:0%; --hotspot-left:0%;">●</div>';
            $remark = ($lang === 'en') ? $row['remark_en'] : $row['remark_nl'];
            if (!empty($remark)) {
              echo '<div class="info-box" id="' . htmlspecialchars($remarkId) . '" hidden><strong>' . ($lang === 'en' ? 'Remark:' : 'Opmerking:') . '</strong><br>' . htmlspecialchars($remark) . '</div>';
            } else {
              echo '<div class="info-box" id="' . htmlspecialchars($remarkId) . '" hidden><strong>' . ($lang === 'en' ? 'Remark:' : 'Opmerking:') . '</strong><br></div>';
            }
          }

          // Extra hotspots uit aparte tabel (optioneel)
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
                echo '<div class="hotspot hotspot-extra" data-target="' . htmlspecialchars($extraId) . '" data-pos-top="' . htmlspecialchars($extraTopPx) . '" data-pos-left="' . htmlspecialchars($extraLeftPx) . '" style="--hotspot-top:0%; --hotspot-left:0%;">●</div>';
                $extraInfo = ($lang === 'en') ? $extra['info_en'] : $extra['info_nl'];
                if (!empty($extraInfo)) {
                  echo '<div class="info-box" id="' . htmlspecialchars($extraId) . '" hidden><strong>' . ($lang === 'en' ? 'Additional info:' : 'Aanvullende info:') . '</strong><br>' . htmlspecialchars($extraInfo) . '</div>';
                } else {
                  echo '<div class="info-box" id="' . htmlspecialchars($extraId) . '" hidden><strong>' . ($lang === 'en' ? 'Additional info:' : 'Aanvullende info:') . '</strong><br></div>';
                }
                if (!empty($extra['image'])) {
                  $extraImg = './assets/img/' . $extra['image'];
                  echo '<img src="' . htmlspecialchars($extraImg) . '" alt="Extra afbeelding" class="extra-img">';
                }
              }
            }
            $stmtExtra->close();
          }

          echo '</div>'; // einde image-wrapper
        }
        ?>
      </div>
    </div>

    <!-- Mini-map: structuur voor JS (JS vult thumbnails en highlight) -->
    <div class="mini-map" aria-hidden="false">
      <div class="mini-inner">
        <div class="mini-track" aria-hidden="true">
          <div class="mini-highlight"></div>
          <!-- thumbnails worden dynamisch toegevoegd door panorama.js -->
        </div>
        <div class="mini-label">Overzicht</div>
      </div>
    </div>
  </main>

  <script src="./assets/js/panorama.js"></script>

  <script>
    // Voorlezen voor ALLE hotspots

    document.addEventListener("DOMContentLoaded", function() {


      let utterance = null;

      document.querySelectorAll('.hotspot').forEach(hotspot => {

        const id = hotspot.getAttribute('data-id');
        const textBlock = document.getElementById('text-' + id);
        const toolbar = hotspot.querySelector('.hotspot-toolbar');

        const readBtn = toolbar.querySelector('.read');
        const pauseBtn = toolbar.querySelector('.pause');
        const playBtn = toolbar.querySelector('.play');
        const stopBtn = toolbar.querySelector('.stop');

        // Als hotspot wordt aangeklikt, wordt de toolbar zichtbaar
        hotspot.addEventListener('click', () => {
          hotspot.classList.toggle('open');

          // Voorlezen functionaliteit
          readBtn.addEventListener('click', () => {
            speechSynthesis.cancel();
            utterance = new SpeechSynthesisUtterance(textBlock.innerText);
            utterance.lang = 'nl-NL';
            speechSynthesis.speak(utterance);
          });

          pauseBtn.addEventListener('click', () => {
            if (utterance) speechSynthesis.pause();
          });

          playBtn.addEventListener('click', () => {
            if (utterance) speechSynthesis.resume();
          });

          stopBtn.addEventListener('click', () => {
            speechSynthesis.cancel();
          });
        });
      });
    });
  </script>


  <footer>
    <?php include "includes/footer.php" ?>
  </footer>

</body>

</html>