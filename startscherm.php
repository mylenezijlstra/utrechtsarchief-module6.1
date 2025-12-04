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

    <script>
      const translations = {
        nl: {
          title: "PANORAMA VAN UTRECHT",
          subtitle: "Hoe werkt het?",
          icon1: "Zoekbalk gebruiken om plaatsen te vinden",
          icon2: "Klik op de hotspots",
          icon3: "Sleep om te verplaatsen",
          start: "Start Panorama",
          details: "Bekijk Details"
        },
        en: {
          title: "PANORAMA OF UTRECHT",
          subtitle: "How does it work?",
          icon1: "Use the search bar to find places",
          icon2: "Click on the hotspots",
          icon3: "Drag to move around",
          start: "Start Panorama",
          details: "View Details"
        }
      };

      // Helper om cookie te lezen
      function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
      }

      // Bepaal huidige taal: eerst cookie, dan localStorage, anders NL
      let currentLang = getCookie("lang") || localStorage.getItem("lang") || "nl";

      function setLanguage(lang) {
        currentLang = lang;
        localStorage.setItem("lang", lang);
        document.cookie = "lang=" + lang + "; path=/";

        document.querySelector(".title").textContent = translations[lang].title;
        document.querySelector(".icon-heading").textContent = translations[lang].subtitle;
        document.querySelectorAll(".icon-item p")[0].textContent = translations[lang].icon1;
        document.querySelectorAll(".icon-item p")[1].textContent = translations[lang].icon2;
        document.querySelectorAll(".icon-item p")[2].textContent = translations[lang].icon3;
        document.querySelectorAll(".button")[0].textContent = translations[lang].start;
        document.querySelectorAll(".button")[1].textContent = translations[lang].details;

        document.documentElement.lang = lang;
      }

      // Event listeners voor taal-knoppen
      document.getElementById("btn-nl").addEventListener("click", () => setLanguage("nl"));
      document.getElementById("btn-en").addEventListener("click", () => setLanguage("en"));

      // Zet taal bij laden
      setLanguage(currentLang);

      function startPanorama() {
        window.location.href = "archiefbreed.php";
      }

      function toonDetails() {
        alert(currentLang === "en" ? "Here come hotspots and extra info." : "Hier komen hotspots en extra uitleg.");
      }
    </script>
</body>
</html>
