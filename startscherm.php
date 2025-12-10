<?php
$lang = $_COOKIE['lang'] ?? 'nl';
function t($nl, $en, $lang) {
  return $lang === 'en' ? $en : $nl;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo t("Panorama Utrecht – 1859 Stijl", "Utrecht Panorama – 1859 Style", $lang); ?></title>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="./assets/css/startscherm.css">

    <style>
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
            color: #000;
            transition: 0.2s ease;
        }
        .icon-btn:hover { transform: scale(1.15); }
        .icon-btn:active { transform: scale(0.95); }
    </style>
</head>
<body>

    <header>
        <?php include "includes/header.php"; ?>
    </header>

    <div class="frame">

        <div class="top-buttons">
            <button id="readPage" class="icon-btn" title="<?php echo t("Voorlezen","Read aloud",$lang); ?>">
                <i class="fa-solid fa-play"></i>
            </button>
            <button id="pauseRead" class="icon-btn" title="<?php echo t("Pauzeer","Pause",$lang); ?>">
                <i class="fa-solid fa-pause"></i>
            </button>
            <button id="stopRead" class="icon-btn" title="<?php echo t("Stop","Stop",$lang); ?>">
                <i class="fa-solid fa-stop"></i>
            </button>
            <button id="restartRead" class="icon-btn" title="<?php echo t("Opnieuw voorlezen","Restart reading",$lang); ?>">
                <i class="fa-solid fa-reply-all"></i>
            </button>
        </div>

        <h1 class="title"><?php echo t("PANORAMA VAN UTRECHT","UTRECHT PANORAMA",$lang); ?></h1>

        <div class="icon-block">
            <h2 class="icon-heading"><?php echo t("Hoe werkt het?","How does it work?",$lang); ?></h2>
            <div class="icon-row">
                <div class="icon-item">
                    <img src="./assets/img-voorkant/zoekbalkje.png" alt="Zoekicoon">
                    <p><?php echo t("Zoekbalk gebruiken om plaatsen te vinden","Use the search bar to find places",$lang); ?></p>
                </div>
                <div class="icon-item">
                    <img src="./assets/img-voorkant/handje.png" alt="Hotspoticoon">
                    <p><?php echo t("Klik op de hotspots","Click on the hotspots",$lang); ?></p>
                </div>
                <div class="icon-item">
                    <img src="./assets/img-voorkant/pijltjes.png" alt="Pijltjesicoon">
                    <p><?php echo t("Sleep om te verplaatsen","Drag to move",$lang); ?></p>
                </div>
            </div>
        </div>

        <div class="buttons">
            <a href="archiefbreed.php">
                <button class="button"><?php echo t("Start Panorama","Start Panorama",$lang); ?></button>
            </a>
        </div>
    </div>

    <footer>
        <?php include "includes/footer.php"; ?>
    </footer>

    <script src="./assets/js/app.js"></script>

    <script>
        let utterance = new SpeechSynthesisUtterance();
        utterance.lang = '<?php echo $lang === "en" ? "en-US" : "nl-NL"; ?>';

        const readBtn = document.getElementById('readPage');
        const pauseBtn = document.getElementById('pauseRead');
        const stopBtn = document.getElementById('stopRead');
        const restartBtn = document.getElementById('restartRead');

        function getReadableText() {
            return document.querySelector('.frame').innerText;
        }

        readBtn.addEventListener('click', () => {
            speechSynthesis.cancel();
            utterance.text = getReadableText();
            speechSynthesis.speak(utterance);
        });
        pauseBtn.addEventListener('click', () => speechSynthesis.pause());
        stopBtn.addEventListener('click', () => speechSynthesis.cancel());
        restartBtn.addEventListener('click', () => {
            speechSynthesis.cancel();
            utterance.text = getReadableText();
            speechSynthesis.speak(utterance);
        });
    </script>

</body>
</html>
