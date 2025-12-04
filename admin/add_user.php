<?php
require_once '../includes/db.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if ($username && $email && $password) {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);
        if ($stmt->execute()) {
            header("Location: users.php?success=1");
            exit;
        } else {
            $message = "❌ Fout bij toevoegen gebruiker.";
        }
    } else {
        $message = "⚠️ Vul alle velden in.";
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>Nieuwe gebruiker</title>
</head>
<body>
  <h1>Nieuwe gebruiker toevoegen</h1>
  <?php if ($message): ?><p><?php echo $message; ?></p><?php endif; ?>
  <form method="post" action="add_user.php">
    <label>Gebruikersnaam:</label>
    <input type="text" name="username" required><br>

    <label>Email:</label>
    <input type="email" name="email" required><br>

    <label>Wachtwoord:</label>
    <input type="password" name="password" required><br>

    <button type="submit">➕ Voeg gebruiker toe</button>
  </form>
</body>
</html>
