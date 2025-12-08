<?php
// colofonadmin.php

// Databaseverbinding
$host = "localhost";
$user = "root";
$pass = "";
$db   = "utrechtsarchief";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Opslaan van wijzigingen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("INSERT INTO colofon (title, content) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $content);
    $stmt->execute();
    $stmt->close();

    // Na opslaan opnieuw laden zodat de nieuwe tekst zichtbaar is
    header("Location: colofonadmin.php");
    exit;
}

// Haal huidige colofon op
$result = $conn->query("SELECT * FROM colofon ORDER BY id DESC LIMIT 1");
$row = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <title>Admin – Colofon</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --ua-red: #C4002E;
            --ua-ink: #1A1A1A;
            --ua-muted: #5A5A5A;
            --ua-bg: #FAF8F6;
            --ua-card: #FFFFFF;
            --radius: 8px;
            --shadow: 0 1px 2px rgba(0, 0, 0, 0.06), 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Inter, Arial, sans-serif;
            background: var(--ua-bg);
            color: var(--ua-ink);
        }

        .page {
            max-width: 900px;
            margin: 3rem auto;
            padding: 1rem;
        }

        .card {
            background: var(--ua-card);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 2rem;
        }

        h1 {
            margin-top: 0;
            color: var(--ua-red);
        }

        label {
            font-weight: 600;
            display: block;
            margin-top: 1rem;
            margin-bottom: .5rem;
        }

        input[type="text"] {
            width: 100%;
            padding: .5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        textarea {
            width: 100%;
            min-height: 400px;
            padding: .75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: inherit;
            line-height: 1.5;
        }

        button {
            margin-top: 1rem;
            background: var(--ua-red);
            color: #fff;
            padding: .7rem 1.4rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }

        button:hover {
            background: #a00025;
        }

        nav {
            display: flex;
            gap: 12px;
        }

        nav a {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            background-color: #d6c3a3;
            /* zachte zandkleur */
            color: #4b3a26;
            /* donkerbruine tekst */
            border-radius: 6px;
            /* afgeronde hoeken */
            border: 1px solid #bfa68c;
            /* subtiele rand */
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        nav a:hover {
            background-color: #cbb694;
            /* iets donkerder bij hover */
            border-color: #a89172;
            color: #3f2f1f;
        }

        /* Header */
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f9f1e3;
            padding: 10px 20px;
            border-bottom: 2px solid #c4ae8a;
        }

        .logout-btn {
            background: #e8dbc4;
            border: 1px solid #8c7455;
            padding: 6px 10px;
            text-decoration: none;
            color: #4b3a26;
        }

        .logout-btn:hover {
            background: #d6c4a5;
        }
    </style>
</head>

<body>

    <header class="admin-header">
        <div>
            <h2>Hotspots beheren</h2>
            <div>Ingelogd als <?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></div>
        </div>
        <nav>
            <a href="/utrechtsarchief-module6.1/admin/admin.php">Hotspots</a>
            <a href="/utrechtsarchief-module6.1/admin/colofonadmin.php">Colofon</a>
            <a href="/utrechtsarchief-module6.1/admin/logout.php">Logout</a>
        </nav>
    </header>

    <main class="page">
        <article class="card">
            <h1>Colofon bewerken</h1>
            <form action="colofonadmin.php" method="post">
                <label for="title">Titel</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($row['title'] ?? 'Colofon') ?>">

                <label for="content">Tekst</label>
                <textarea id="content" name="content"><?= htmlspecialchars($row['content'] ?? '') ?></textarea>

                <form action="update_colofon.php" method="post">
                    <!-- velden -->
                    <button type="submit">Opslaan</button>
                </form>

            </form>
        </article>
    </main>
</body>

</html>