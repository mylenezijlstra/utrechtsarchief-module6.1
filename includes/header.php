<!doctype html>
<html lang="nl">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Het Utrechts Archief header</title>
    <style>
        /* CSS uit jouw header, nu als los stylesheet */
        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial;
            margin: 0;
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background: #fff;
            height: 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 1rem;
            box-sizing: border-box;
            overflow: visible;
            /* belangrijk: logo mag uitsteken */
        }

        .logo {
            position: relative;
            width: 120px;
            /* vaste ruimte voor ALLE formaten logo */
            height: 70px;
            /* zelfde hoogte als header */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo img {
            position: absolute;
            bottom: -40px;
            /* laat logo onder header uitsteken */
            height: 95px;
            transition: all .25s ease;
        }

        .logo.shrink img {
            height: 60px;
            bottom: -0px;
        }

        nav {
            display: flex;
            gap: 1.2rem;
            font-size: 1.05rem;
        }

        nav a {
            text-decoration: none;
            color: #000;
            font-weight: 500;
        }

        nav a i {
            font-style: italic;
            color: #444;
        }

        .search-btn {

            border-radius: 2px;
            display: flex;
            align-items: flex-end;
            ;
            justify-content: center;
        }

        .search-btn img {
            height: 55px;
            /* groter icoon */
        }

        header {
            position: fixed;
            top: 0;
            /* zet hem tegen de bovenkant */
            left: 0;
            width: 100%;
            /* laat hem de hele breedte nemen */
            z-index: 1000;
            /* zodat hij boven andere elementen blijft */
        }
    </style>
</head>

<body>
    <header>
        <a class="search-btn" href="/zoeken">
            <img src="./assets/img/searchicon.png" alt="Zoeken" />
        </a>

        <nav>
            <a href="startscherm.php"> Startscherm</a>
            <a href="archiefbreed.php"> Panorama</a>
            <a href="/spel"> Spel</a>
            <a href="/Colofon"> Colofon</a>
            <a href="/over-ons">› Over ons</a>
            <a href="https://hetutrechtsarchief.nl/contact"> Contact</a>
            <a href="/english"><i>English</i></a>
            <a href="/nederlands"><i>Nederlands</i></a>
        </nav>

        <a class="logo" href="/">
            <img id="mainlogo" src="./assets/img/logoklein.png" alt="Het Utrechts Archief Logo" />
        </a>
    </header>

    <script>
        // Logo kleiner maken bij scroll
        const logo = document.querySelector('.logo');
        const img = document.getElementById('mainlogo');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                logo.classList.add('shrink');
                img.src = './assets/img/logoklein.png';
            } else {
                logo.classList.remove('shrink');
                img.src = './assets/img/logogroot.png';
            }
        });
    </script>
</body>

</html>