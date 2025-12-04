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

        /* Positioneer de dropdown binnen je nav */
        nav .dropdown {
            position: relative;
            display: inline-block;
        }

        /* Stijl van de knop “› Over ons” */
        nav .dropbtn {
            background: none;
            border: none;
            font-size: 16px;
            cursor: pointer;
            padding: 0 10px;
            text-decoration: none;
            font: inherit;
            font-weight: 500;

        }

        /* Dropdown stijl */
        nav .dropdown-content {
            display: none;
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            padding: 15px 20px;
            min-width: 220px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            z-index: 1000;
        }

        /* Links in dropdown */
        nav .dropdown-content a {
            display: block;
            padding: 6px 0;
            color: black;
            text-decoration: none;
            font-size: 16px;
        }

        nav .dropdown-content a:hover {
            text-decoration: underline;
        }

        /* Dropdown openen bij hover */
        nav .dropdown:hover .dropdown-content {
            display: block;
        }


        .menu-toggle {
            display: none;
        }

        /* Tablet (iPad) */
        @media (max-width: 1024px) {

            header {
                flex-direction: row;
                justify-content: space-between;

            }

            header nav {
                display: none;
                /* verberg menu standaard */
                flex-direction: column;
                width: 100%;
                background: #f9f9f9;
                padding: 10px;
                margin-bottom: -300px;

            }

            header nav.active {
                display: flex;
                /* toon menu bij klik */
            }

            .menu-toggle {
                display: block;
                cursor: pointer;
                font-size: 1.5rem;
            }

            .search-btn {
                display: none;
                /* verberg zoekknop op kleine schermen */

            }

            .logo img {
                height: 95px;
                /* kleiner logo */
                bottom: -35px;
                /* minder uitsteken */

            }

            .logo.shrink img {
                height: 70px;
                /* nog kleiner bij scroll */
                bottom: 0px;

            }
        }

        /* Telefoon */
        @media (max-width: 768px) {
            header {
                flex-direction: row;
                justify-content: space-between;

            }

            header nav {
                display: none;
                /* verberg menu standaard */
                flex-direction: column;
                width: 100%;
                background: #f9f9f9;
                padding: 10px;
                margin-bottom: -300px;
            }

            header nav.active {
                display: flex;
                /* toon menu bij klik */
            }

            .menu-toggle {
                display: block;
                cursor: pointer;
                font-size: 1.5rem;
            }

            .search-btn {
                display: none;
                /* verberg zoekknop op kleine schermen */

            }

            .logo img {
                height: 65px;
                /* kleiner logo */
                bottom: -15px;
                /* minder uitsteken */

            }

            .logo.shrink img {
                height: 50px;
                /* nog kleiner bij scroll */
                bottom: 15px;

            }

        }

        #btn-en,
        #btn-nl {
            all: unset;
            /* haalt alle standaard button-styling weg */
           
            /* maakt de tekst dikgedrukt */
            cursor: pointer;
            /* blijft klikbaar */
            font-style: italic;  
        }
    </style>
</head>

<body>
    <header>



        <a class="search-btn" href="/zoeken">
            <img src="./assets/img/searchicon.png" alt="Zoeken" />
        </a>

        <div class="menu-toggle">☰</div>

        <nav>
            <a href="startscherm.php"> Startscherm</a>
            <a href="archiefbreed.php"> Panorama</a>
            <a href="colofon.php"> Colofon</a>

            <div class="dropdown">
                <button class="dropbtn">› Over ons</button>

                <div class="dropdown-content">
                    <a href="https://hetutrechtsarchief.nl/over-ons/archief-overdragen">Archief overdragen</a>
                    <a href="https://hetutrechtsarchief.nl/over-ons/missie">Beleid</a>
                    <a href="https://hetutrechtsarchief.nl/over-ons/projecten">Projecten</a>
                    <a href="https://hetutrechtsarchief.nl/over-ons/nieuws">Nieuws</a>
                    <a href="https://hetutrechtsarchief.nl/over-ons/medewerkers">Medewerkers</a>
                    <a href="https://hetutrechtsarchief.nl/over-ons/vacatures">Vacatures</a>
                    <a href="https://hetutrechtsarchief.nl/over-ons/word-vriend">Word vriend</a>
                    <a href="https://hetutrechtsarchief.nl/over-ons/toegankelijkheid">Toegankelijkheid</a>
                    <a href="https://hetutrechtsarchief.nl/over-ons/heeft-u-een-klacht">Heeft u een klacht?</a>
                </div>
            </div>


            <a href="https://hetutrechtsarchief.nl/contact"> Contact</a>
            <button id="btn-en">English</button>
            <button id="btn-nl">Nederlands</button>
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


        const toggle = document.querySelector('.menu-toggle');
        const nav = document.querySelector('header nav');

        toggle.addEventListener('click', () => {
            nav.classList.toggle('active');
        });
    </script>
</body>

</html>