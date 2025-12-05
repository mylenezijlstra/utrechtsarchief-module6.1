<?php
session_start();
include 'db.php';

// Functie: gebruiker toevoegen
function addUser($conn, $username, $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hash);
    $stmt->execute();
    echo "<div class='message success'>Gebruiker <b>$username</b> toegevoegd!</div>";
}

// Functie: gebruiker verwijderen
function deleteUser($conn, $username) {
    $stmt = $conn->prepare("DELETE FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    echo "<div class='message danger'>Gebruiker <b>$username</b> verwijderd!</div>";
}

// Functie: login check
function loginUser($conn, $username, $password) {
    $stmt = $conn->prepare("SELECT password_hash FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && password_verify($password, $row['password_hash'])) {
        $_SESSION['user'] = $username;
        echo "<div class='message success'>Welkom $username, je bent ingelogd!</div>";
    } else {
        echo "<div class='message danger'>Foute login.</div>";
    }
}

// Uitloggen
if(isset($_POST['logout'])) {
    session_destroy();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// Form verwerken
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) addUser($conn, $_POST['username'], $_POST['password']);
    if (isset($_POST['delete'])) deleteUser($conn, $_POST['username']);
    if (isset($_POST['login'])) loginUser($conn, $_POST['username'], $_POST['password']);
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>Gebruikersbeheer</title>
  <style>
    body {
      margin: 0;
      padding: 40px;
      background: #f3ecdd; /* perkamentkleur */
      font-family: "Georgia", serif;
      color: #3b2a1a;
    }

    .frame {
      width: 80%;
      margin: auto;
      background: #fffaf0;
      border: 6px double #6b5334;
      box-shadow: 0 0 25px rgba(0,0,0,0.3);
      padding: 40px;
      text-align: center;
      position: relative;
    }

    h1 {
      font-size: 42px;
      font-family: "Times New Roman", serif;
      color: #4b3a26;
      text-shadow: 1px 1px 0 #bda888;
      margin-bottom: 30px;
    }

    .form-block {
      margin: 40px auto;
      background: #f9f1e3;
      border: 3px solid #c4ae8a;
      padding: 25px;
      width: 70%;
      text-align: left;
      border-radius: 8px;
      box-shadow: inset 0 0 10px rgba(0,0,0,0.15);
    }

    .form-block h2 {
      font-family: "Times New Roman", serif;
      font-size: 26px;
      color: #6b5334;
      margin-bottom: 20px;
      text-align: center;
      border-bottom: 2px solid #bda888;
      padding-bottom: 10px;
    }

    label {
      display:block;
      margin-top:15px;
      font-weight:bold;
      color:#4b3a26;
    }

    input {
      width:100%;
      padding:10px;
      margin-top:5px;
      border:2px solid #8c7455;
      border-radius:4px;
      font-family:"Georgia",serif;
      background:#fffaf0;
    }

    button, .back-button {
      margin-top:20px;
      padding:12px 24px;
      background:#e8dbc4;
      border:2px solid #8c7455;
      font-size:18px;
      cursor:pointer;
      transition:0.2s;
      color:#4b3a26;
      font-family:"Georgia",serif;
      border-radius:6px;
      text-decoration:none;
      display:inline-block;
    }

    button:hover, .back-button:hover {
      background:#d6c4a5;
      transform:scale(1.05);
    }

    .message {
      margin:20px auto;
      padding:12px;
      width:70%;
      border-radius:6px;
      font-family:"Georgia",serif;
      text-align:center;
    }
    .success { background:#d4edda; color:#155724; border:2px solid #8c7455; }
    .danger { background:#f8d7da; color:#721c24; border:2px solid #8c7455; }
    .status { margin-bottom:20px; font-style:italic; }

    /* terugknop linksboven */
    .back-container {
      position: absolute;
      top: 20px;
      left: 20px;
    }
  </style>
</head>
<body>
  <div class="frame">
    <!-- Terugknop linksboven -->
    <div class="back-container">
      <a href="admin.php" class="back-button">← Terug naar Admin</a>
    </div>

    <h1>Gebruikersbeheer</h1>

    <!-- Login status -->
    <div class="status">
      <?php if(isset($_SESSION['user'])): ?>
        Ingelogd als <b><?php echo $_SESSION['user']; ?></b>
        <form method="post" style="display:inline;">
          <button type="submit" name="logout">Uitloggen</button>
        </form>
      <?php else: ?>
        Niet ingelogd
      <?php endif; ?>
    </div>

    <!-- Login -->
    <div class="form-block">
      <h2>Inloggen</h2>
      <form method="post">
        <label>Username:</label>
        <input type="text" name="username" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <button type="submit" name="login">Login</button>
      </form>
    </div>

    <!-- Toevoegen -->
    <div class="form-block">
      <h2>Nieuwe gebruiker toevoegen</h2>
      <form method="post">
        <label>Username:</label>
        <input type="text" name="username" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <button type="submit" name="add">Toevoegen</button>
      </form>
    </div>

    <!-- Verwijderen -->
    <div class="form-block">
      <h2>Gebruiker verwijderen</h2>
      <form method="post">
        <label>Username:</label>
        <input type="text" name="username" required>
        <button type="submit" name="delete">Verwijderen</button>
      </form>
    </div>
  </div>
</body>
</html>
