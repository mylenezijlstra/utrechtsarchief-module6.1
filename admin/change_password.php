<?php
require_once '../includes/db.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$message = "";

// gebruiker ophalen
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
}

// wachtwoord wijzigen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = (int)$_POST['id'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $password, $id);
    if ($stmt->execute()) {
        $message = "✅ Wachtwoord gewijzigd!";
    } else {
        $message = "❌ Fout bij wijzigen wachtwoord.";
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>Wachtwoord wijzigen</title>
</head>
<body>
  <h1>Wachtwoord wijzigen voor <?php echo htmlspecialchars($user['username']); ?></h1>
  <?php if ($message): ?><p><?php echo $message; ?></p><?php endif; ?>
  <form method="post" action="change_password.php">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <label>Nieuw wachtwoord:</label>
    <input type="password" name="password" required><br>
    <button type="submit">🔑 Wijzig wachtwoord</button>
  </form>
</body>
</html>
