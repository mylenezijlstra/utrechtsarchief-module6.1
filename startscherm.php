<?php
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <title>Panorama Utrecht – 1859 Stijl</title>
    <link rel="stylesheet" href="./assets/css/startscherm.css">
</head>

<body>

   <header>
      <?php
      include "includes/header.php"
      ?>
   </header>

    <div class="frame">
        <div class="top-buttons">
            <button id="btn-nl" class="nav-button">Nederlands</button>
            <button id="btn-en" class="nav-button">English</button>
            <button class="nav-button info-button">i</button>
        </div>

        <h1 class="title">PANORAMA VAN UTRECHT</h1>

        <div class="icon-block">
            <h2 class="icon-heading">Hoe werkt het?</h2>

            <div class="icon-row">
                <div class="icon-item">
                    <img src="./assets/img-voorkant/zoekbalkje.png" alt="Zoekicoon">
                    <p>Zoekbalk gebruiken om plaatsen te vinden</p>
                </div>

                <div class="icon-item">
                    <img src="./assets/img-voorkant/handje.png" alt="Hotspoticoon">
                    <p>Klik op de hotspots</p>
                </div>

                <div class="icon-item">
                    <img src="./assets/img-voorkant/pijltjes.png" alt="Pijltjesicoon">
                    <p>Sleep om te verplaatsen</p>
                </div>
            </div>
        </div>

        <div class="buttons">
            <button class="button" onclick="startPanorama()">Start Panorama</button>
            <button class="button" onclick="toonDetails()">Bekijk Details</button>
        </div>
    </div>

     <footer>
      <?php
      include "includes/footer.php"
      ?>
    </footer>

    <script src="./assets/js/app.js"></script>
</body>
</html>