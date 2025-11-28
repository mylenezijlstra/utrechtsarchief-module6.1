<?php
session_set_cookie_params(['path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
session_start();


include 'db.php';

// Als al ingelogd, ga naar admin
if (!empty($_SESSION['logged_in'])) {
  header("Location: /utrechtsarchief-module6.1/admin/admin.php");
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
    header("Location: /utrechtsarchief-module6.1/admin/admin.php");
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
  <link rel="stylesheet" href="/utrechtsarchief-module6.1/assets/css/admin.css" />
</head>
<body>
  <div class="login-box">
    <h2>Login beheer</h2>
    <form method="post" action="/utrechtsarchief-module6.1/admin/login.php">
      <input name="username" placeholder="Gebruiker" autocomplete="username">
      <input name="password" type="password" placeholder="Wachtwoord" autocomplete="current-password">
      <button type="submit">Login</button>
      <?php if($error) echo "<p class='error'>$error</p>"; ?>
    </form>
  </div>
</body>
</html>
