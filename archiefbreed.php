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

  <!-- Voeg Font Awesome toe via de CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />

  <style>
    .hotspot-toolbar {
      display: none;
      /* Maak de toolbar verborgen */
      gap: 6px;
      margin-top: 6px;
    }

    .hotspot-btn {
      font-size: 16px;
      cursor: pointer;
      border: none;
      background: none;
      /* Geen achtergrondkleur */
      color: #000;
      /* Zwarte kleur voor de iconen */
      padding: 6px;
    }

    .hotspot-text {
      display: none;
    }

    .hotspot.open .hotspot-toolbar {
      display: flex;
      /* Toon de toolbar wanneer de hotspot open is */
    }
  </style>
</head>

<body>

  <header>
    <?php include "includes/header.php"; ?>
  </header>

  <main>
    <div class="panorama-frame">
      <div class="panorama">

        <?php
        $result = $conn->query("SELECT p.filename, h.pos_top, h.pos_left, h.description_nl
                              FROM panorama p
                              LEFT JOIN hotspots h 
                                ON CAST(REPLACE(p.filename, '.jpg', '') AS UNSIGNED) = h.image_id
                              ORDER BY CAST(REPLACE(p.filename, '.jpg', '') AS UNSIGNED)");

        $count = 1;
        while ($row = $result->fetch_assoc()) {

          $imgPath = './assets/img/' . $row['filename'];
          $size = @getimagesize($imgPath);
          $imgWidth = $size[0] ?? 0;
          $imgHeight = $size[1] ?? 0;
          $desc = $row['description_nl'];

          $topPercent = toPercent($row['pos_top'], $imgHeight);
          $leftPercent = toPercent($row['pos_left'], $imgWidth);

          echo '<div class="image-wrapper" style="--hotspot-top:' . $topPercent . '%; --hotspot-left:' . $leftPercent . '%;">';
          echo '<img src="' . $imgPath . '" alt="Panorama ' . $count . '">';

          if ($topPercent !== null && $leftPercent !== null) {
            echo '<div class="hotspot">•';
            if (!empty($desc)) {
              echo '<div class="info-box">';
              echo '<strong>' . ($lang === 'en' ? 'Description:' : 'Beschrijving:') . '</strong><br>' . htmlspecialchars($desc);
              echo '</div>';
            }

            echo '</div>';
          }

          echo '</div>';
          $count++;
        }
        ?>

      </div>

      <!-- Mini-map onderin -->
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

    <footer>
      <?php include "includes/footer.php"; ?>
    </footer>

  </main>

  <script src="./assets/js/panorama.js"></script>

  <script>
    // Voorlezen voor ALLE hotspots

    document.addEventListener("DOMContentLoaded",function(){

      
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

</body>

</html>
