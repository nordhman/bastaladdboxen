<?php
// ENKELT ADMIN-PANEL
// VIKTIGT: Lägg denna fil i en lösenordsskyddad mapp eller lägg till autentisering!
require_once '../includes/config.php';

// Basic lösenordsskydd - ÄNDRA DETTA!
$admin_password = 'ändra-mig-123';
if (!isset($_SESSION['admin_logged_in'])) {
    if ($_POST['password'] ?? '' === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Admin Login</title>
        <style>body{font-family:sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;background:#0a0d14;color:#e8eaf0;}
        form{background:#111622;padding:40px;border-radius:16px;border:1px solid rgba(255,255,255,0.07);}
        input{display:block;width:100%;padding:10px;margin:12px 0;background:#0f1420;border:1px solid rgba(255,255,255,0.1);color:#e8eaf0;border-radius:8px;}
        button{width:100%;padding:12px;background:#5ee7c1;color:#0a0d14;border:none;border-radius:8px;font-weight:700;cursor:pointer;}
        </style></head><body>
        <form method="post"><h2>Admin Login</h2>
        <input type="password" name="password" placeholder="Lösenord">
        <button type="submit">Logga in</button>
        </form></body></html>';
        exit;
    }
}

$db = db();
$message = '';

// Hantera formulär
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_product') {
        $stmt = $db->prepare("INSERT INTO produkter 
            (kategori_id, namn, slug, kort_beskrivning, lang_beskrivning, pris, affiliate_url, affiliate_butik, betyg, antal_recensioner, effekt_kw, laddtid_timmar, installation, smart_laddning, app_styrning, framhavd, bild_url) 
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        
        $slug = strtolower(str_replace([' ', 'å','ä','ö','Å','Ä','Ö'], ['-','a','a','o','a','a','o'], $_POST['namn']));
        $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
        
        $stmt->execute([
            $_POST['kategori_id'],
            $_POST['namn'],
            $slug,
            $_POST['kort_beskrivning'],
            $_POST['lang_beskrivning'] ?? '',
            $_POST['pris'] ?: null,
            $_POST['affiliate_url'],
            $_POST['affiliate_butik'],
            $_POST['betyg'],
            $_POST['antal_recensioner'],
            $_POST['effekt_kw'] ?: null,
            $_POST['laddtid_timmar'] ?: null,
            $_POST['installation'],
            isset($_POST['smart_laddning']) ? 1 : 0,
            isset($_POST['app_styrning']) ? 1 : 0,
            isset($_POST['framhavd']) ? 1 : 0,
            $_POST['bild_url']
        ]);
        $message = '✅ Produkt tillagd!';
    }
    
    if ($_POST['action'] === 'delete_product') {
        $db->prepare("DELETE FROM produkter WHERE id = ?")->execute([$_POST['id']]);
        $db->prepare("DELETE FROM spec_varden WHERE produkt_id = ?")->execute([$_POST['id']]);
        $message = '🗑️ Produkt borttagen.';
    }

    if ($_POST['action'] === 'toggle_featured') {
        $db->prepare("UPDATE produkter SET framhavd = NOT framhavd WHERE id = ?")->execute([$_POST['id']]);
        $message = '⭐ Uppdaterat!';
    }
}

$kategorier = $db->query("SELECT * FROM kategorier ORDER BY id")->fetchAll();
$produkter = $db->query("SELECT p.*, k.namn as k_namn FROM produkter p JOIN kategorier k ON p.kategori_id=k.id ORDER BY p.skapad DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="sv">
<head>
<meta charset="UTF-8">
<title>Admin – BästaLaddboxen.se</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:#0a0d14;color:#e8eaf0;min-height:100vh}
.admin-header{background:#111622;border-bottom:1px solid rgba(255,255,255,0.07);padding:16px 32px;display:flex;align-items:center;gap:16px}
.admin-header h1{font-size:1.2rem;font-weight:700}
.container{max-width:1200px;margin:0 auto;padding:32px}
.card{background:#111622;border:1px solid rgba(255,255,255,0.07);border-radius:16px;padding:28px;margin-bottom:28px}
h2{font-size:1.1rem;font-weight:700;margin-bottom:20px;color:#5ee7c1}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.form-group{display:flex;flex-direction:column;gap:6px}
.form-group label{font-size:0.82rem;color:#7a8499;font-weight:600}
.form-group input,.form-group select,.form-group textarea{background:#0f1420;border:1px solid rgba(255,255,255,0.1);color:#e8eaf0;padding:10px 14px;border-radius:8px;font-size:0.9rem;font-family:inherit}
.form-group textarea{resize:vertical;min-height:80px}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{outline:none;border-color:#5ee7c1}
.checkbox-group{display:flex;gap:24px;flex-wrap:wrap}
.checkbox-label{display:flex;align-items:center;gap:8px;font-size:0.9rem;cursor:pointer}
.btn{display:inline-block;padding:10px 22px;border-radius:8px;font-weight:600;cursor:pointer;border:none;font-size:0.9rem;font-family:inherit;transition:0.2s}
.btn-green{background:#5ee7c1;color:#0a0d14}
.btn-green:hover{background:#7af0d6}
.btn-red{background:rgba(255,77,109,0.15);color:#ff4d6d;border:1px solid rgba(255,77,109,0.3)}
.btn-red:hover{background:rgba(255,77,109,0.25)}
.btn-sm{padding:5px 12px;font-size:0.8rem}
.message{background:rgba(94,231,193,0.1);border:1px solid rgba(94,231,193,0.3);color:#5ee7c1;padding:12px 18px;border-radius:8px;margin-bottom:24px}
table{width:100%;border-collapse:collapse}
th,td{padding:12px 14px;text-align:left;border-bottom:1px solid rgba(255,255,255,0.05);font-size:0.875rem}
th{color:#7a8499;font-weight:600;font-size:0.78rem;text-transform:uppercase;letter-spacing:0.06em}
tr:hover td{background:rgba(255,255,255,0.02)}
.betyg-badge{background:rgba(246,201,14,0.1);color:#f6c90e;padding:2px 8px;border-radius:100px;font-size:0.78rem;font-weight:700}
.featured-badge{background:rgba(94,231,193,0.1);color:#5ee7c1;padding:2px 8px;border-radius:100px;font-size:0.78rem}
.full-width{grid-column:1/-1}
@media(max-width:768px){.form-grid{grid-template-columns:1fr}table{font-size:0.8rem}}
</style>
</head>
<body>
<div class="admin-header">
  <span>⚡</span>
  <h1>BästaLaddboxen.se – Admin</h1>
  <a href="../index.php" style="margin-left:auto;color:#7a8499;font-size:0.85rem">← Tillbaka till sajten</a>
</div>

<div class="container">
  <?php if ($message): ?>
  <div class="message"><?= h($message) ?></div>
  <?php endif; ?>

  <!-- LÄGG TILL PRODUKT -->
  <div class="card">
    <h2>+ Lägg till produkt</h2>
    <form method="POST">
      <input type="hidden" name="action" value="add_product">
      <div class="form-grid">
        <div class="form-group">
          <label>Produktnamn *</label>
          <input type="text" name="namn" required placeholder="t.ex. Easee Home">
        </div>
        <div class="form-group">
          <label>Kategori *</label>
          <select name="kategori_id" required>
            <?php foreach ($kategorier as $k): ?>
            <option value="<?= $k['id'] ?>"><?= h($k['namn']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Kort beskrivning</label>
          <input type="text" name="kort_beskrivning" placeholder="Max 200 tecken">
        </div>
        <div class="form-group">
          <label>Affiliate URL *</label>
          <input type="url" name="affiliate_url" placeholder="https://...">
        </div>
        <div class="form-group">
          <label>Butik</label>
          <input type="text" name="affiliate_butik" placeholder="t.ex. Inet, Amazon, Elgiganten">
        </div>
        <div class="form-group">
          <label>Pris (kr)</label>
          <input type="number" name="pris" step="0.01" placeholder="4995">
        </div>
        <div class="form-group">
          <label>Betyg (0–5)</label>
          <input type="number" name="betyg" step="0.1" min="0" max="5" value="4.5">
        </div>
        <div class="form-group">
          <label>Antal recensioner</label>
          <input type="number" name="antal_recensioner" value="0">
        </div>
        <div class="form-group">
          <label>Effekt (kW)</label>
          <input type="number" name="effekt_kw" step="0.1" placeholder="22">
        </div>
        <div class="form-group">
          <label>Laddtid (timmar)</label>
          <input type="number" name="laddtid_timmar" step="0.5" placeholder="3">
        </div>
        <div class="form-group">
          <label>Installation</label>
          <input type="text" name="installation" placeholder="El-installatör krävs">
        </div>
        <div class="form-group">
          <label>Bild URL</label>
          <input type="url" name="bild_url" placeholder="https://...">
        </div>
        <div class="form-group full-width">
          <label>Lång beskrivning / recension</label>
          <textarea name="lang_beskrivning" rows="4" placeholder="Detaljerad recension av produkten..."></textarea>
        </div>
        <div class="form-group full-width">
          <div class="checkbox-group">
            <label class="checkbox-label">
              <input type="checkbox" name="smart_laddning"> Smart laddning
            </label>
            <label class="checkbox-label">
              <input type="checkbox" name="app_styrning"> App-styrning
            </label>
            <label class="checkbox-label">
              <input type="checkbox" name="framhavd"> Framhävd (visas på startsidan)
            </label>
          </div>
        </div>
      </div>
      <div style="margin-top:20px">
        <button type="submit" class="btn btn-green">Lägg till produkt</button>
      </div>
    </form>
  </div>

  <!-- PRODUKTLISTA -->
  <div class="card">
    <h2>Alla produkter (<?= count($produkter) ?>)</h2>
    <table>
      <thead>
        <tr>
          <th>Produkt</th>
          <th>Kategori</th>
          <th>Pris</th>
          <th>Betyg</th>
          <th>Status</th>
          <th>Åtgärder</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($produkter as $p): ?>
        <tr>
          <td>
            <a href="../produkt.php?slug=<?= h($p['slug']) ?>" target="_blank" style="color:#5ee7c1"><?= h($p['namn']) ?></a>
          </td>
          <td><?= h($p['k_namn']) ?></td>
          <td><?= $p['pris'] ? number_format($p['pris'], 0, ',', ' ') . ' kr' : '–' ?></td>
          <td><span class="betyg-badge"><?= number_format($p['betyg'],1) ?> ★</span></td>
          <td>
            <?php if ($p['framhavd']): ?>
            <span class="featured-badge">⭐ Framhävd</span>
            <?php else: ?>
            <span style="color:#4a5568;font-size:0.8rem">Standard</span>
            <?php endif; ?>
          </td>
          <td style="display:flex;gap:6px;flex-wrap:wrap">
            <form method="POST" style="display:inline">
              <input type="hidden" name="action" value="toggle_featured">
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <button type="submit" class="btn btn-sm" style="background:rgba(255,255,255,0.06);color:#e8eaf0;border:1px solid rgba(255,255,255,0.1)">
                <?= $p['framhavd'] ? '★ Avfram.' : '☆ Framhäv' ?>
              </button>
            </form>
            <form method="POST" style="display:inline" onsubmit="return confirm('Säker?')">
              <input type="hidden" name="action" value="delete_product">
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <button type="submit" class="btn btn-sm btn-red">Radera</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
