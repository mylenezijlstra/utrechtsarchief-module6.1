<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utrechts Archief Panorama</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            margin: 0;
            overflow-x: scroll;
            white-space: nowrap;
            scroll-behavior: smooth;
            background: #111;
        }

        img.draggable {
            position: absolute;
            cursor: grab;
            user-select: none;
            height: 100vh;
        }

        #fullscreenBtn {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 2000;
            padding: 8px 12px;
            background: #fff;
            border: none;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <button id="fullscreenBtn">Volledig scherm</button>

    <?php
    include "./includes/localhostverbinding.php";

    // Haal alle afbeeldingen op
    $sql = "SELECT afbeeldingen, x, y FROM panorama ORDER BY id ASC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $offset = 0;
        while ($row = $result->fetch_assoc()) {
            $src = "assets/img/" . $row['afbeeldingen'];
            $x = $row['x'] ?? $offset;
            $y = $row['y'] ?? 0;
            echo "<img src='$src' class='draggable' data-name='{$row['afbeeldingen']}' style='left:{$x}px; top:{$y}px;' />";
            $offset += 300; // standaard afstand tussen foto's
        }
    }
    ?>

    <script>
        let dragged = null;

        document.querySelectorAll('.draggable').forEach(img => {
            img.addEventListener('mousedown', (e) => {
                dragged = img;
                dragged.style.zIndex = 1000;
            });

            document.addEventListener('mousemove', (e) => {
                if (dragged) {
                    dragged.style.left = e.pageX + 'px';
                    dragged.style.top = e.pageY + 'px';
                }
            });

            document.addEventListener('mouseup', () => {
                if (dragged) {
                    const name = dragged.dataset.name;
                    const x = parseInt(dragged.style.left);
                    const y = parseInt(dragged.style.top);

                    // AJAX-call om positie op te slaan
                    fetch('save_position.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                name,
                                x,
                                y
                            })
                        })
                        .then(res => res.text())
                        .then(data => console.log(data));

                    dragged = null;
                }
            });
        });

        // Volledig scherm knop
        document.getElementById('fullscreenBtn').addEventListener('click', () => {
            const elem = document.documentElement;
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            }
        });
    </script>

</body>

</html>