<?php
session_set_cookie_params(['path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
session_start();


include 'db.php';

// Als al ingelogd, ga naar admin
if (!empty($_SESSION['logged_in'])) {
  header("Location: ".WEBSITEROOT."/admin/admin.php");
  exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  // Let op: gebruik hier mysqli of PDO afhankelijk van je db.php
  // Voor mysqli:
  $stmt = $conn->prepare("SELECT username, password_hash FROM users WHERE username=?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();
  $user = $result->fetch_assoc();

  if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['logged_in'] = true;
    $_SESSION['username'] = $username;
    header("Location: ".WEBSITEROOT."/admin/admin.php");
    exit;
  } else {
    $error = "Onjuiste login";
  }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="<?php echo WEBSITEROOT; ?>/assets/css/admin.css" />

  <style>

/* VOLLEDIGE PAGINA */
body {
    margin: 0;
    padding: 0;
    font-family: Arial, Helvetica, sans-serif;
    height: 100vh;
    display: flex;
    align-items: center;      /* verticaal centreren */
    justify-content: center;  /* horizontaal centreren */
    overflow: hidden;
    position: relative;
}

/* ACHTERGRONDAFBEELDING MET BLUR */
body::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url("<?php echo WEBSITEROOT; ?>/assets/img/2.jpg") center/cover no-repeat;
    filter: blur(6px) brightness(0.55); /* wazig + donkerder */
    z-index: -1;
}

.login-box input,
.login-box button {
    box-sizing: border-box;
}


/* LOGIN BOX */
.login-box {
    width: 360px;
    padding: 35px 30px;
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(4px);
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.25);
    border-top: 6px solid #bda888;
    animation: fadeIn 0.5s ease-out;
}

/* TITEL */
.login-box h2 {
    margin-bottom: 25px;
    text-align: center;
    color: #2d2d2d;
    font-size: 24px;
}

/* INPUTS */
.login-box input {
    width: 100%;
    padding: 13px;
    margin: 12px 0;
    border: 1px solid #cccccc;
    border-radius: 8px; /* mooie ronde hoeken */
    font-size: 15px;
    background: #fafafa;
    transition: border-color 0.3s ease, background 0.3s ease;
}

.login-box input:focus {
    background: #ffffff;
    border-color: #bda888;
    outline: none;
}

/* BUTTON */
.login-box button {
    width: 100%;
    padding: 13px;
    background: #a79478ff;
    border: none;
    border-radius: 8px; /* ronde knoppen */
    color: white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 10px;
    transition: background 0.25s ease;
}

.login-box button:hover {
    background: #bda888;
}

/* ERROR */
.error {
    margin-top: 15px;
    text-align: center;
    color: #bda888;
    font-weight: bold;
}

/* ANIMATIE */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}


  </style>
</head>
<body>
  <div class="login-box">
    <h2>Login beheer</h2>
    <form method="post" action="<?php echo WEBSITEROOT; ?>/admin/login.php">
      <input name="username" placeholder="Gebruiker" autocomplete="username">
      <input name="password" type="password" placeholder="Wachtwoord" autocomplete="current-password">
      <button type="submit">Login</button>
      <?php if($error) echo "<p class='error'>$error</p>"; ?>
    </form>
  </div>
</body>
</html>
