<?php
// admin.php

include __DIR__ . '/db.php'; // verwacht $conn (mysqli)

session_start();
if (empty($_SESSION['logged_in'])) {
  header("Location: ".WEBSITEROOT."/admin/login.php");
  exit;
}


// Lees alle afbeeldingen
$files = glob(__DIR__ . "/../assets/img/*.jpg");
function imgIndex($path)
{
  return intval(pathinfo(basename($path), PATHINFO_FILENAME));
}
usort($files, function ($a, $b) {
  return imgIndex($a) <=> imgIndex($b);
});
?>
<!doctype html>
<html lang="nl">

<head>
  <meta charset="utf-8">
  <title>Panorama Admin</title>
  <link rel="stylesheet" href="<?php echo WEBSITEROOT; ?>/assets/css/admin.css">
</head>

<body>
  <header class="admin-header">
    <div>
      <h2>Hotspots beheren</h2>
      <div>Ingelogd als <?php echo htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
    </div>
    <nav>
      <a href="<?php echo WEBSITEROOT; ?>/admin/users.php">Admin toevoegen</a>
      <a href="<?php echo WEBSITEROOT; ?>/admin/admin.php">Hotspots</a>
      <a href="<?php echo WEBSITEROOT; ?>/admin/colofonadmin.php">Colofon</a>
      <a href="<?php echo WEBSITEROOT; ?>/admin/logout.php">Logout</a>
    </nav>
  </header>

  <main class="panorama-frame">
    <div class="panorama">
      <?php foreach ($files as $file):
        $idx = imgIndex($file);

        $spot = [
          'pos_top' => 20,
          'pos_left' => 20,
          'description_nl' => '',
          'description_en' => '',
          'remark_top' => null,
          'remark_left' => null,
          'remark_nl' => '',
          'remark_en' => ''
        ];
        $stmt = $conn->prepare("SELECT * FROM hotspots WHERE image_id = ? LIMIT 1");
        if ($stmt) {
          $stmt->bind_param("i", $idx);
          if ($stmt->execute()) {
            $res = $stmt->get_result();
            if ($res && $row = $res->fetch_assoc()) {
              $spot = array_merge($spot, $row);
            }
            if ($res) $res->free();
          }
          $stmt->close();
        }

        $extras = [];
        $stmt2 = $conn->prepare("SELECT id, pos_top, pos_left, info_nl, info_en, image FROM hotspot_extra WHERE hotspot_id = ?");
        if ($stmt2) {
          $stmt2->bind_param("i", $idx);
          if ($stmt2->execute()) {
            $res2 = $stmt2->get_result();
            if ($res2) {
              while ($r = $res2->fetch_assoc()) $extras[] = $r;
              $res2->free();
            }
          }
          $stmt2->close();
        }
      ?>
        <div class="image-wrapper" data-id="<?php echo $idx; ?>">
          <div class="image-panel">
            <?php $safeFile = WEBSITEROOT.'/assets/img/' . rawurlencode(basename($file)); ?>
            <img src="<?php echo $safeFile; ?>" alt="Panorama <?php echo $idx; ?>">

            <!-- Hoofd-hotspot -->
            <div class="hotspot hotspot-desc" style="top:<?php echo (int)$spot['pos_top']; ?>px; left:<?php echo (int)$spot['pos_left']; ?>px;">i</div>
            <div class="info-box info-desc" style="display:none;">
              <label>Beschrijving (NL)</label>
              <textarea class="info-text-nl" rows="3"><?php echo htmlspecialchars($spot['description_nl'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
              <label>Beschrijving (EN)</label>
              <textarea class="info-text-en" rows="3"><?php echo htmlspecialchars($spot['description_en'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
              <div class="controls">
                <button class="save-hotspot" data-type="desc">Opslaan</button>
                <form method="post" action="<?php echo WEBSITEROOT; ?>/admin/delete_hotspot.php" onsubmit="return confirm('Hoofd-hotspot van afbeelding <?php echo $idx; ?> verwijderen?');" style="display:inline;">
                  <input type="hidden" name="image_id" value="<?php echo $idx; ?>">
                  <button type="submit" class="delete-extra">Verwijderen</button>
                </form>
                <span class="save-status"></span>
              </div>
            </div>

            <!-- Opmerking-hotspot -->
            <?php if ($spot['remark_top'] !== null && $spot['remark_left'] !== null): ?>
              <div class="hotspot hotspot-remark" style="top:<?php echo (int)$spot['remark_top']; ?>px; left:<?php echo (int)$spot['remark_left']; ?>px;">i</div>
              <div class="info-box info-remark" style="display:none;">
                <label>Opmerking (NL)</label>
                <textarea class="remark-nl" rows="3"><?php echo htmlspecialchars($spot['remark_nl'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                <label>Opmerking (EN)</label>
                <textarea class="remark-en" rows="3"><?php echo htmlspecialchars($spot['remark_en'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                <div class="controls">
                  <button class="save-hotspot" data-type="remark">Opslaan</button>
                  <form method="post" action="<?php echo WEBSITEROOT; ?>/admin/delete_hotspot.php" onsubmit="return confirm('Hoofd-hotspot van afbeelding <?php echo $idx; ?> verwijderen?');" style="display:inline;">
                    <input type="hidden" name="image_id" value="<?php echo $idx; ?>">
                    <button type="submit" class="delete-extra">Verwijderen</button>
                  </form>
                  <span class="save-status"></span>
                </div>
              </div>
            <?php endif; ?>

            <!-- Extra hotspots -->
            <?php foreach ($extras as $ex): ?>
              <div class="hotspot hotspot-extra" data-extra-id="<?php echo (int)$ex['id']; ?>"
                style="top:<?php echo (int)$ex['pos_top']; ?>px; left:<?php echo (int)$ex['pos_left']; ?>px;">i</div>
              <div class="info-box info-extra" style="display:none;">
                <label>Aanvullende info (NL)</label>
                <textarea class="extra-info-nl" rows="3"><?php echo htmlspecialchars($ex['info_nl'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                <label>Additional info (EN)</label>
                <textarea class="extra-info-en" rows="3"><?php echo htmlspecialchars($ex['info_en'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                <label>Extra afbeelding</label>
                <input class="extra-image" type="file" accept="image/*">
                <?php if (!empty($ex['image'])): ?>
                  <div style="margin-top:4px;font-size:12px;color:#555">
                    Huidig bestand: <?php echo htmlspecialchars($ex['image'], ENT_QUOTES, 'UTF-8'); ?>
                  </div>
                <?php endif; ?>
                <div class="controls">
                  <button class="save-extra" data-extra-id="<?php echo (int)$ex['id']; ?>">Opslaan</button>
                  <button class="delete-extra" data-extra-id="<?php echo (int)$ex['id']; ?>">Verwijderen</button>
                  <span class="save-status"></span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <div style="margin-top:8px;display:flex;gap:8px;align-items:center">
            <button class="add-extra">+ Extra toevoegen</button>
            <span style="font-size:13px;color:#666">Image ID: <?php echo $idx; ?></span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </main>

  <script src="/<?php echo WEBSITEROOT; ?>assets/js/script.js"></script>
</body>

</html>
