<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colofonpagina</title>

    <style>
        :root {
            --ua-red: #C4002E;
            --ua-ink: #1A1A1A;
            --ua-muted: #5A5A5A;
            --ua-bg: #FAF8F6;
            --ua-card: #FFFFFF;
            --radius: 12px;
            --shadow: 0 1px 2px rgba(0, 0, 0, 0.06), 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        html {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Inter, Arial, sans-serif;
        }

        body {
            margin: 0;
            background: linear-gradient(180deg, var(--ua-bg), #fff 40%);
            color: var(--ua-ink);
        }

        .page {
            max-width: 880px;
            margin: 4rem auto;
            padding: 0 1.25rem;
        }

        .card {
            background: var(--ua-card);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .brandbar {
            height: 10px;
            background: var(--ua-red);
        }

        .header {
            padding: 2rem 2rem 1.5rem;
            border-bottom: 1px solid #eee;
        }

        .kicker {
            display: inline-block;
            font-size: 0.78rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--ua-muted);
            margin-bottom: .5rem;
        }

        h1 {
            margin: 0;
            font-size: clamp(1.8rem, 3.5vw, 2.4rem);
            line-height: 1.15;
        }

        .content {
            padding: 1.5rem 2rem 2rem;
            display: grid;
            gap: 1.75rem;
        }

        section {
            position: relative;
            padding-left: 1rem;
        }

        section::before {
            content: "";
            position: absolute;
            left: 0;
            top: .35rem;
            bottom: .35rem;
            width: 3px;
            background: var(--ua-red);
            border-radius: 3px;
            opacity: .9;
        }

        h2 {
            font-size: clamp(1.15rem, 2.4vw, 1.35rem);
            margin: 0 0 .5rem;
        }

        p {
            margin: 0 0 .75rem;
            line-height: 1.6;
            color: var(--ua-ink);
            white-space: pre-line; /* behoudt nieuwe regels uit DB */
        }

        .credits {
            display: grid;
            gap: .5rem;
        }

        .credits dt {
            font-weight: 600;
            color: var(--ua-ink);
        }

        .credits dd {
            margin: 0 0 .5rem 0;
            color: var(--ua-muted);
        }

        a {
            color: var(--ua-red);
            text-underline-offset: 2px;
            text-decoration-thickness: 1.5px;
        }

        @media (min-width: 680px) {
            .content {
                grid-template-columns: 1fr;
            }
        }

        @media print {
            body {
                background: #fff;
            }

            .card {
                box-shadow: none;
                border: 1px solid #ddd;
            }

            .brandbar {
                height: 6px;
            }
        }
    </style>
</head>

<body>

<header>
    <?php include "includes/header.php"; ?>
</header>

<main class="page" aria-labelledby="colofon-title">
    <article class="card">
        <div class="brandbar" aria-hidden="true"></div>
        <div class="content">
            <?php
            // Databaseverbinding
            $host = "localhost";
            $user = "root";
            $pass = "";
            $db   = "utrechtsarchief";

            $conn = new mysqli($host, $user, $pass, $db);
            if ($conn->connect_error) {
                die("Verbinding mislukt: " . $conn->connect_error);
            }

            // Haal de laatste versie van colofon op
            $result = $conn->query("SELECT * FROM colofon ORDER BY id DESC LIMIT 1");
            if ($result && $row = $result->fetch_assoc()) {
                echo "<section>";
                echo "<h2 id='inleiding'>" . htmlspecialchars($row['title']) . "</h2>";
                echo "<p>" . htmlspecialchars($row['content']) . "</p>";
                echo "</section>";
            } else {
                echo "<p>Geen colofontekst gevonden.</p>";
            }
            ?>
        </div>
    </article>
</main>

<footer>
    <?php include "includes/footer.php"; ?>
</footer>

</body>
</html>
