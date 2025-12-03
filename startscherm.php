<?php
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <title>Panorama Utrecht – 1859 Stijl</title>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="./assets/css/startscherm.css">

    <style>
        /* Voorlees-iconen bovenaan */
        .top-buttons {
            display: flex;
            gap: 1rem;
            justify-content: flex-start;
            margin-bottom: 1rem;
        }

        .icon-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            font-size: 1.8rem;
            color: #000000ff;
            transition: 0.2s ease;
        }

        .icon-btn:hover {
            color: #000;
            transform: scale(1.15);
        }

        .icon-btn:active {
            transform: scale(0.95);
        }
    </style>
</head>

<body>

<header>
    <?php include "includes/header.php" ?>
</header>

<div class="frame">

    <div class="top-buttons">

        <!-- PLAY -->
        <button id="readPage" class="icon-btn" title="Voorlezen">
            <i class="fa-solid fa-play"></i>
        </button>

        <!-- PAUSE -->
        <button id="pauseRead" class="icon-btn" title="Pauzeer">
            <i class="fa-solid fa-pause"></i>
        </button>

        <!-- STOP -->
        <button id="stopRead" class="icon-btn" title="Stop">
            <i class="fa-solid fa-stop"></i>
        </button>

        <!-- HERSTART -->
        <button id="restartRead" class="icon-btn" title="Opnieuw voorlezen">
            <i class="fa-solid fa-reply-all"></i>
        </button>

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
    <?php include "includes/footer.php" ?>
</footer>

<script src="./assets/js/app.js"></script>

<script>
    let utterance = new SpeechSynthesisUtterance();
    utterance.lang = 'nl-NL';

    const readBtn = document.getElementById('readPage');
    const pauseBtn = document.getElementById('pauseRead');
    const stopBtn = document.getElementById('stopRead');
    const restartBtn = document.getElementById('restartRead');

    // Alleen de inhoud binnen .frame voorlezen
    function getReadableText() {
        return document.querySelector('.frame').innerText;
    }

    readBtn.addEventListener('click', () => {
        speechSynthesis.cancel();
        utterance.text = getReadableText();
        speechSynthesis.speak(utterance);
    });

    pauseBtn.addEventListener('click', () => {
        speechSynthesis.pause();
    });

    stopBtn.addEventListener('click', () => {
        speechSynthesis.cancel();
    });

    restartBtn.addEventListener('click', () => {
        speechSynthesis.cancel();
        utterance.text = getReadableText();
        speechSynthesis.speak(utterance);
    });
</script>

</body>
</html>
