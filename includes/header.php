<!doctype html>
<html lang="<?php echo $_COOKIE['lang'] ?? 'nl'; ?>">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Het Utrechts Archief header</title>
  <style>
    body {
      font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial;
      margin: 0;
    }

    header {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 70px;
      z-index: 1000;
      background: #fff;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 1rem;
      box-sizing: border-box;
    }

    .logo {
      position: relative;
      width: 120px;
      height: 70px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .logo img {
      position: absolute;
      bottom: -40px;
      height: 95px;
      transition: all .25s ease;
    }

    .logo.shrink img {
      height: 60px;
      bottom: 0;
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

    .menu-toggle {
      display: none;
    }

    /* Zoekveld direct achter icoon */
    .search-container {
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .search-icon {
      height: 40px;
      cursor: pointer;
    }

    #searchInput {
      padding: 6px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    #searchBtn {
      padding: 6px 12px;
      background: #288074;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    #searchBtn:hover {
      background: #288062;
    }

    /* Highlight bij zoeken */
    .image-wrapper.highlight {
      outline: 4px solid orange;
      transition: outline 0.4s ease;
    }

    /* Taal-knoppen mooi maken */
    .lang-btn {
      background: #288074;
      color: #fff;
      padding: 6px 12px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 0.95rem;
      font-weight: 600;
      transition: background 0.25s ease, transform 0.15s ease;
    }

    .lang-btn:hover {
      background: #1f645c;
      transform: translateY(-2px);
    }

    .lang-btn:active {
      transform: translateY(0);
    }
  </style>
</head>

<body>
  <header>
    <!-- Zoekcontainer -->
    <div class="search-container">
      <img src="./assets/img/searchicon.png" alt="Zoeken" class="search-icon" />
      <input type="text" id="searchInput" placeholder="Zoek plaatsnaam..." />
      <button id="searchBtn">Zoek</button>
    </div>

    <div class="menu-toggle">☰</div>

    <nav>
      <a href="startscherm.php">Start</a>
      <a href="archiefbreed.php">Leporello</a>
      <a href="colofon.php">Colofon</a>
      <a href="https://hetutrechtsarchief.nl/contact">Contact</a>
      <button id="btn-en" class="lang-btn">EN</button>
      <button id="btn-nl" class="lang-btn">NL</button>

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

    // Menu toggle
    const toggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('header nav');
    toggle.addEventListener('click', () => nav.classList.toggle('active'));

    // Taalkeuze cookie zetten
    document.getElementById('btn-en').addEventListener('click', () => {
      document.cookie = "lang=en; path=/";
      location.reload();
    });
    document.getElementById('btn-nl').addEventListener('click', () => {
      document.cookie = "lang=nl; path=/";
      location.reload();
    });

    // Zoekindex (plaatsnamen koppelen aan panorama-id)
    const searchIndex = {
      1: "Titelblad Panorama van Utrecht",
      2: "Wittevrouwenbrug Wittevrouwenstraat Utrecht",
      3: "Wolvenplein gevangenis Wolvenburg Utrecht",
      4: "Plompetorengracht Noorderkade Begijnebolwerk Utrecht",
      5: "Begijnebolwerk Utrecht",
      6: "Begijnebolwerk Van Asch van Wijckskade Utrecht",
      7: "Van Asch van Wijckskade Weerdbrug Noorderkade Utrecht",
      8: "Noorderkade Nieuwekade Paardenveld Utrecht",
      9: "Paardenveld molen De Meiboom Wasch- en Badinrichting Utrecht",
      10: "Catharijnebrug Catharijnekade Vredenburg Utrecht",
      11: "Vredenburg Rijnkade koperpletterij Utrecht",
      12: "Willemsbrug Rijnkade Willemsplantsoen Utrecht",
      13: "Singelplantsoen Mariaplaats Domtoren Utrecht",
      14: "Singelplantsoen Zeven Steegjes Utrecht",
      15: "Singelplantsoen Bartholomeusgasthuis Utrecht",
      16: "Singelplantsoen Geertekerk houtvlot Utrecht",
      17: "Singelplantsoen Springweg Sterrenburg Bijlhouwerstoren Utrecht",
      18: "Singelplantsoen bastion Sterrenburg Bijlhouwerstoren Utrecht",
      19: "Tolsteegbrug Ledig Erf bastion Manenburg Utrecht",
      20: "Singelplantsoen Nicolaikerk St.-Agnietenklooster Centraal Museum Utrecht",
      21: "Singelplantsoen Fundatie van Renswoude Agnietenstraat Utrecht",
      22: "Singelplantsoen Nieuwegracht Servaasabdij Utrecht",
      23: "Singelplantsoen bastion Zonnenburg Meteorologisch Instituut Sterrenwacht Utrecht",
      24: "Singelplantsoen bastion Lepelenburg Utrecht",
      25: "Singelplantsoen Servaasbolwerk Leeuwenberchgasthuis Utrecht",
      26: "Maliebrug Maliebarrière bolwerk Lepelenburg Utrecht",
      27: "Bolwerk Lepelenburg huis Lievendaal Utrecht",
      28: "Bolwerk Lepelenburg particuliere tuinen Utrecht",
      29: "Singelplantsoen Herenstraat Hieronymusplantsoen Utrecht",
      30: "Singelplantsoen Kromme Nieuwegracht Hieronymuskapel Utrecht",
      31: "Lucasbrug Lucasbolwerk Suikerhuis Utrecht",
      32: "Lucasbolwerk Suikerhuis Utrecht",
      33: "Singelplantsoen Wittevrouwenbrug Utrecht"
    };

    // Zoekactie
    document.getElementById('searchBtn').addEventListener('click', () => {
      const query = document.getElementById('searchInput').value.toLowerCase();
      if (!query) return;

      let foundPage = null;
      for (const [page, place] of Object.entries(searchIndex)) {
        if (place.toLowerCase().includes(query)) {
          foundPage = page;
          break;
        }
      }

      if (foundPage) {
        const target = document.querySelector(`.image-wrapper[data-id="${foundPage}"]`);
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
          });
          target.classList.add('highlight');
          setTimeout(() => target.classList.remove('highlight'), 2000);
        }
      } else {
        alert("Geen resultaat gevonden");
      }
    });
  </script>
</body>

</html>