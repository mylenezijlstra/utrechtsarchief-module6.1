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
        $result = $conn->query("SELECT p.filename, h.pos_top, h.pos_left, h.description
                              FROM panorama p
                              LEFT JOIN hotspots h 
                                ON CAST(REPLACE(p.filename, '.jpg', '') AS UNSIGNED) = h.image_id
                              ORDER BY CAST(REPLACE(p.filename, '.jpg', '') AS UNSIGNED)");

        $count = 1;
        while ($row = $result->fetch_assoc()) {

          echo '<div class="image-wrapper">';
          echo '<img src="./assets/img/' . $row['filename'] . '" alt="Panorama ' . $count . '">';

          if ($count == 21) { // Specifieke hotspot voor foto 21
            // Maak een vaste hotspot die linkt naar spel.php
            echo '<div class="hotspot" style="top:50px; left:50px;">'; // Positie naar wens aanpassen
            echo '<a href="spel.php" class="hotspot-link">•</a>';
            echo '</div>';
          } elseif ($row['pos_top'] !== null && $row['pos_left'] !== null) {

            echo '<div class="hotspot" style="top:' . (int)$row['pos_top'] . 'px; left:' . (int)$row['pos_left'] . 'px;">•';

            if (!empty($row['description'])) {

              // Verborgen tekst voor voorlezen
              echo '<div class="hotspot-text" id="text-' . $count . '">'
                . htmlspecialchars($row['description']) . 
                '</div>';

              // Infobox inclusief voorleesicons
              echo '<div class="info-box">
                <div class="hotspot-toolbar" data-id="' . $count . '">
                    <button class="hotspot-btn read"><i class="fa-solid fa-play"></i></button>
                    <button class="hotspot-btn pause"><i class="fa-solid fa-pause"></i></button>
                    <button class="hotspot-btn play"><i class="fa-solid fa-stop"></i></button>
                    <button class="hotspot-btn stop"><i class="fa-solid fa-reply-all"></i></button>
                </div>
                <strong>Beschrijving:</strong><br>' . htmlspecialchars($row['description']) . '
              </div>';
            }

            echo '</div>';
          }

          echo '</div>';
          $count++;
        }
        ?>

      </div>
    </div>

    <footer>
      <?php include "includes/footer.php"; ?>
    </footer>

  </main>

  <script src="./assets/js/panorama.js"></script>

  <script>
    // Voorlezen voor ALLE hotspots
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
  </script>

</body>

</html>
