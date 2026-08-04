<?php
require_once 'includes/config.php';
$db = db();

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

$kategori = $db->prepare("SELECT * FROM kategorier WHERE slug = ?");
$kategori->execute([$slug]);
$kategori = $kategori->fetch();

if (!$kategori) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

// Sortering
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'betyg';
$sort_sql = match($sort) {
    'pris_asc' => 'p.pris ASC',
    'pris_desc' => 'p.pris DESC',
    'namn' => 'p.namn ASC',
    default => 'p.betyg DESC'
};

$produkter = $db->prepare("
    SELECT p.* FROM produkter p
    WHERE p.kategori_id = ? AND p.aktiv = TRUE
    ORDER BY $sort_sql
");
$produkter->execute([$kategori['id']]);
$produkter = $produkter->fetchAll();

$page_title = 'Bästa ' . $kategori['namn'] . ' 2026 – Jämför priser & betyg | BästaLaddboxen.se';
?>
<!DOCTYPE html>
<html lang="sv">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($page_title) ?></title>
<meta name="description" content="Jämför <?= h(strtolower($kategori['namn'])) ?> för elbil 2026. <?= h($kategori['beskrivning']) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="page-hero page-hero--kategori">
  <div class="container">
    <div class="breadcrumb">
      <a href="index.php">Hem</a> › <?= h($kategori['namn']) ?>
    </div>
    <span class="kategori-ikon-stor"><?= h($kategori['ikon']) ?></span>
    <h1><?= h($kategori['namn']) ?></h1>
    <p><?= h($kategori['beskrivning']) ?></p>
  </div>
</div>

<section class="section-produkter">
  <div class="container">
    <div class="filter-bar">
      <span><?= count($produkter) ?> produkter</span>
      <div class="sort-knappar">
        <span>Sortera:</span>
        <a href="?slug=<?= h($slug) ?>&sort=betyg" class="sort-btn <?= $sort==='betyg'?'active':'' ?>">Bäst betyg</a>
        <a href="?slug=<?= h($slug) ?>&sort=pris_asc" class="sort-btn <?= $sort==='pris_asc'?'active':'' ?>">Lägst pris</a>
        <a href="?slug=<?= h($slug) ?>&sort=pris_desc" class="sort-btn <?= $sort==='pris_desc'?'active':'' ?>">Högst pris</a>
      </div>
    </div>

    <?php if (empty($produkter)): ?>
    <div class="empty-state">
      <p>Inga produkter hittades i denna kategori ännu.</p>
    </div>
    <?php else: ?>
    <div class="produkt-grid">
      <?php foreach ($produkter as $i => $p): ?>
      <div class="produkt-kort <?= $i === 0 ? 'produkt-kort--featured' : '' ?>">
        <?php if ($i === 0): ?><div class="badge-editor">Bäst i test</div><?php endif; ?>
        <div class="produkt-bild">
          <?php if ($p['bild_url']): ?>
            <img src="<?= h($p['bild_url']) ?>" alt="<?= h($p['namn']) ?>" loading="lazy">
          <?php else: ?>
            <div class="bild-placeholder">⚡</div>
          <?php endif; ?>
        </div>
        <div class="produkt-body">
          <h3 class="produkt-namn"><a href="produkt.php?slug=<?= h($p['slug']) ?>"><?= h($p['namn']) ?></a></h3>
          <p class="produkt-desc"><?= h($p['kort_beskrivning']) ?></p>
          <div class="produkt-specs">
            <?php if ($p['effekt_kw']): ?>
            <span class="spec-chip">⚡ <?= h($p['effekt_kw']) ?> kW</span>
            <?php endif; ?>
            <?php if ($p['smart_laddning']): ?><span class="spec-chip">📱 Smart</span><?php endif; ?>
            <?php if ($p['app_styrning']): ?><span class="spec-chip">📲 App</span><?php endif; ?>
          </div>
          <div class="produkt-footer">
            <div class="produkt-betyg">
              <div class="stjarnor"><?= str_repeat('★', round($p['betyg'])) ?><?= str_repeat('☆', 5-round($p['betyg'])) ?></div>
              <span><?= number_format($p['betyg'],1) ?> (<?= $p['antal_recensioner'] ?> rec.)</span>
            </div>
            <?php if ($p['pris']): ?>
            <span class="pris">från <?= number_format($p['pris'], 0, ',', ' ') ?> kr</span>
            <?php endif; ?>
          </div>
          <div class="produkt-knappar">
            <a href="produkt.php?slug=<?= h($p['slug']) ?>" class="btn btn-outline btn-sm">Läs recension</a>
            <?php if ($p['affiliate_url']): ?>
            <a href="<?= h($p['affiliate_url']) ?>" class="btn btn-primary btn-sm" target="_blank" rel="nofollow sponsored">
              Köp hos <?= h($p['affiliate_butik']) ?> →
            </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
<script src="js/main.js"></script>
</body>
</html>
