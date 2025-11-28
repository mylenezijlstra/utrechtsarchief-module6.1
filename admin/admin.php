<?php
session_set_cookie_params(['path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
session_start();

if (empty($_SESSION['logged_in'])) {
  header("Location: /utrechtsarchief-module6.1/admin/login.php");
  exit;
}

include 'db.php';

$files = glob(__DIR__ . "/../assets/img/*.jpg");

// Sorteer numeriek op bestandsnaam (1,2,3,...)
function imgIndex($path) {
    return intval(pathinfo(basename($path), PATHINFO_FILENAME));
}
usort($files, function($a, $b) {
    return imgIndex($a) <=> imgIndex($b);
});
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>Panorama Admin</title>
  <link rel="stylesheet" href="/utrechtsarchief-module6.1/assets/css/admin.css" />
</head>
<body>
  <header class="admin-header">
    <h1>Hotspots beheren</h1>
    <span>Ingelogd als <?php echo htmlspecialchars($_SESSION['username']); ?></span>
    <nav>
      <a href="/utrechtsarchief-module6.1/admin/users.php">Gebruikersbeheer</a>
      <a href="/utrechtsarchief-module6.1/admin/logout.php" class="logout-btn">Logout</a>
    </nav>
  </header>

  <div class="panorama-frame">
    <div class="panorama">
      <?php foreach ($files as $file):
        $idx = imgIndex($file);
        $stmt = $conn->prepare("SELECT pos_top, pos_left, description FROM hotspots WHERE image_id=?");
        $stmt->bind_param("i", $idx);
        $stmt->execute();
        $result = $stmt->get_result();
        $spot = $result->fetch_assoc() ?: ['pos_top'=>20,'pos_left'=>20,'description'=>''];
      ?>
        <div class="image-wrapper" data-id="<?php echo $idx; ?>">
          <img src="<?php echo "/utrechtsarchief-module6.1/assets/img/" . basename($file); ?>" alt="Panorama <?php echo $idx; ?>">
          
          <!-- Hotspot -->
          <div class="hotspot" style="top:<?php echo (int)$spot['pos_top']; ?>px; left:<?php echo (int)$spot['pos_left']; ?>px;">+</div>
          
          <!-- Info-box los van hotspot -->
          <div class="info-box" style="top:<?php echo (int)$spot['pos_top']+30; ?>px; left:<?php echo (int)$spot['pos_left']; ?>px;">
            <textarea class="info-text"><?php echo htmlspecialchars($spot['description']); ?></textarea>
            <button class="save-desc">Opslaan</button>
            <span class="save-status"></span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <script src="/utrechtsarchief-module6.1/assets/js/script.js"></script>
</body>
</html>
