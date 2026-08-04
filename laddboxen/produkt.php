<?php
require_once 'includes/config.php';
$db = db();

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

$stmt = $db->prepare("
    SELECT p.*, k.namn as kategori_namn, k.slug as kategori_slug
    FROM produkter p
    JOIN kategorier k ON p.kategori_id = k.id
    WHERE p.slug = ? AND p.aktiv = TRUE
");
$stmt->execute([$slug]);
$p = $stmt->fetch();

if (!$p) {
    header('HTTP/1.0 404 Not Found');
    include '404.php';
    exit;
}

// Specs
$specs = $db->prepare("SELECT * FROM spec_varden WHERE produkt_id = ?");
$specs->execute([$p['id']]);
$specs = $specs->fetchAll();

// Liknande produkter
$liknande = $db->prepare("
    SELECT * FROM produkter
    WHERE kategori_id = ? AND id != ? AND aktiv = TRUE
    ORDER BY betyg DESC LIMIT 3
");
$liknande->execute([$p['kategori_id'], $p['id']]);
$liknande = $liknande->fetchAll();

$page_title = h($p['namn']) . ' – Recension & Test 2026 | BästaLaddboxen.se';
?>
<!DOCTYPE html>
<html lang="sv">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $page_title ?></title>
<meta name="description" content="Läs vår recension av <?= h($p['namn']) ?>. Betyg: <?= $p['betyg'] ?>/5. <?= h($p['kort_beskrivning']) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
<!-- Strukturerad data för Google -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "<?= h($p['namn']) ?>",
  "description": "<?= h($p['kort_beskrivning']) ?>",
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "<?= $p['betyg'] ?>",
    "reviewCount": "<?= $p['antal_recensioner'] ?>"
  }
  <?php if ($p['pris']): ?>,
  "offers": {
    "@type": "Offer",
    "price": "<?= $p['pris'] ?>",
    "priceCurrency": "SEK",
    "availability": "https://schema.org/InStock"
  }
  <?php endif; ?>
}
</script>
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="produkt-page">
  <div class="container">
    <div class="breadcrumb">
      <a href="index.php">Hem</a> ›
      <a href="kategori.php?slug=<?= h($p['kategori_slug']) ?>"><?= h($p['kategori_namn']) ?></a> ›
      <?= h($p['namn']) ?>
    </div>

    <div class="produkt-detail">
      <div class="produkt-detail-bild">
        <?php if ($p['bild_url']): ?>
          <img src="<?= h($p['bild_url']) ?>" alt="<?= h($p['namn']) ?>">
        <?php else: ?>
          <div class="bild-placeholder bild-placeholder--stor">⚡</div>
        <?php endif; ?>
        <div class="betyg-stor">
          <div class="stjarnor-stor"><?= str_repeat('★', round($p['betyg'])) ?><?= str_repeat('☆', 5-round($p['betyg'])) ?></div>
          <div class="betyg-siffra"><?= number_format($p['betyg'],1) ?><span>/5</span></div>
          <div class="betyg-antal"><?= $p['antal_recensioner'] ?> recensioner</div>
        </div>
      </div>

      <div class="produkt-detail-info">
        <div class="produkt-kategori"><?= h($p['kategori_namn']) ?></div>
        <h1><?= h($p['namn']) ?></h1>
        <p class="produkt-ingress"><?= h($p['kort_beskrivning']) ?></p>

        <div class="pris-box">
          <?php if ($p['pris']): ?>
          <div class="pris-stor">från <strong><?= number_format($p['pris'], 0, ',', ' ') ?> kr</strong></div>
          <?php endif; ?>
          <?php if ($p['affiliate_url']): ?>
          <a href="<?= h($p['affiliate_url']) ?>" class="btn btn-primary btn-lg" target="_blank" rel="nofollow sponsored">
            🛒 Köp hos <?= h($p['affiliate_butik']) ?> →
          </a>
          <p class="affiliate-not">Affiliate-länk – vi tjänar provision</p>
          <?php endif; ?>
        </div>

        <?php if (!empty($specs)): ?>
        <div class="specs-tabell">
          <h3>Tekniska specifikationer</h3>
          <table>
            <tbody>
              <?php foreach ($specs as $s): ?>
              <tr>
                <th><?= h($s['spec_namn']) ?></th>
                <td><?= h($s['spec_varde']) ?></td>
              </tr>
              <?php endforeach; ?>
              <?php if ($p['effekt_kw']): ?>
              <tr><th>Max effekt</th><td><?= h($p['effekt_kw']) ?> kW</td></tr>
              <?php endif; ?>
              <?php if ($p['laddtid_timmar']): ?>
              <tr><th>Laddtid (ca)</th><td><?= h($p['laddtid_timmar']) ?> timmar</td></tr>
              <?php endif; ?>
              <?php if ($p['installation']): ?>
              <tr><th>Installation</th><td><?= h($p['installation']) ?></td></tr>
              <?php endif; ?>
              <tr><th>Smart laddning</th><td><?= $p['smart_laddning'] ? '✅ Ja' : '❌ Nej' ?></td></tr>
              <tr><th>App-styrning</th><td><?= $p['app_styrning'] ? '✅ Ja' : '❌ Nej' ?></td></tr>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Lång beskrivning / recension -->
    <?php if ($p['lang_beskrivning']): ?>
    <div class="recension-text">
      <h2>Vår recension av <?= h($p['namn']) ?></h2>
      <?= nl2br(h($p['lang_beskrivning'])) ?>
    </div>
    <?php endif; ?>

    <!-- Liknande produkter -->
    <?php if (!empty($liknande)): ?>
    <div class="liknande-produkter">
      <h2>Liknande produkter</h2>
      <div class="produkt-grid produkt-grid--liten">
        <?php foreach ($liknande as $lp): ?>
        <div class="produkt-kort">
          <div class="produkt-bild">
            <?php if ($lp['bild_url']): ?>
              <img src="<?= h($lp['bild_url']) ?>" alt="<?= h($lp['namn']) ?>" loading="lazy">
            <?php else: ?>
              <div class="bild-placeholder">⚡</div>
            <?php endif; ?>
          </div>
          <div class="produkt-body">
            <h3 class="produkt-namn"><a href="produkt.php?slug=<?= h($lp['slug']) ?>"><?= h($lp['namn']) ?></a></h3>
            <div class="produkt-footer">
              <div class="produkt-betyg">
                <span class="betyg-pill"><?= number_format($lp['betyg'],1) ?> ★</span>
              </div>
              <?php if ($lp['pris']): ?>
              <span class="pris"><?= number_format($lp['pris'], 0, ',', ' ') ?> kr</span>
              <?php endif; ?>
            </div>
            <a href="produkt.php?slug=<?= h($lp['slug']) ?>" class="btn btn-outline btn-sm">Se produkt</a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="js/main.js"></script>
</body>
</html>
