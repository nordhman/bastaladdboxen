<?php
require_once 'includes/config.php';

$db = db();

// Hämta framhävda produkter
$framhavda = $db->query("
    SELECT p.*, k.namn as kategori_namn, k.slug as kategori_slug
    FROM produkter p
    JOIN kategorier k ON p.kategori_id = k.id
    WHERE p.framhavd = TRUE AND p.aktiv = TRUE
    ORDER BY p.betyg DESC
    LIMIT 6
")->fetchAll();

// Hämta alla kategorier
$kategorier = $db->query("
    SELECT k.*, COUNT(p.id) as antal_produkter
    FROM kategorier k
    LEFT JOIN produkter p ON p.kategori_id = k.id AND p.aktiv = TRUE
    GROUP BY k.id
    ORDER BY k.id
")->fetchAll();

// Hämta topplista - bäst betyg
$topplista = $db->query("
    SELECT p.*, k.namn as kategori_namn
    FROM produkter p
    JOIN kategorier k ON p.kategori_id = k.id
    WHERE p.aktiv = TRUE
    ORDER BY p.betyg DESC
    LIMIT 5
")->fetchAll();

$page_title = 'Bästa Laddboxen 2026 – Jämför & Hitta Rätt | BästaLaddboxen.se';
$page_desc = 'Jämför laddboxar för elbil 2026. Oberoende tester, expertrecensioner och bästa priser. Hitta rätt laddbox för hemmet.';
?>
<!DOCTYPE html>
<html lang="sv">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($page_title) ?></title>
<meta name="description" content="<?= h($page_desc) ?>">
<meta property="og:title" content="<?= h($page_title) ?>">
<meta property="og:description" content="<?= h($page_desc) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg">
    <div class="hero-orb hero-orb-1"></div>
    <div class="hero-orb hero-orb-2"></div>
    <div class="grid-lines"></div>
  </div>
  <div class="container">
    <div class="hero-content">
      <div class="hero-badge">⚡ Uppdaterat <?= date('Y') ?></div>
      <h1>Hitta <em>bästa laddboxen</em><br>för din elbil</h1>
      <p class="hero-sub">Oberoende jämförelser av hemmaladdboxar, portabla laddare och tillbehör. Vi hjälper dig välja rätt – utan krångel.</p>
      <div class="hero-actions">
        <a href="kategori.php?slug=hemmaladdboxar" class="btn btn-primary">Se hemmaladdboxar</a>
        <a href="#topplista" class="btn btn-ghost">Topplistan 2026</a>
      </div>
      <div class="hero-stats">
        <div class="stat"><strong>50+</strong><span>Testade produkter</span></div>
        <div class="stat"><strong>100%</strong><span>Oberoende</span></div>
        <div class="stat"><strong>4.8★</strong><span>Snittbetyg</span></div>
      </div>
    </div>
  </div>
</section>

<!-- KATEGORIER -->
<section class="section-kategorier">
  <div class="container">
    <h2 class="section-title">Utforska kategorier</h2>
    <div class="kategori-grid">
      <?php foreach ($kategorier as $k): ?>
      <a href="kategori.php?slug=<?= h($k['slug']) ?>" class="kategori-kort">
        <span class="kategori-ikon"><?= h($k['ikon']) ?></span>
        <div class="kategori-info">
          <strong><?= h($k['namn']) ?></strong>
          <span><?= $k['antal_produkter'] ?> produkter</span>
        </div>
        <span class="kategori-pil">→</span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- FRAMHÄVDA PRODUKTER -->
<section class="section-produkter" id="topplista">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Bästa laddboxarna 2026</h2>
      <p>Handplockade av våra experter baserat på prestanda, pris och användarvänlighet</p>
    </div>
    <div class="produkt-grid">
      <?php foreach ($framhavda as $i => $p): ?>
      <div class="produkt-kort <?= $i === 0 ? 'produkt-kort--featured' : '' ?>">
        <?php if ($i === 0): ?><div class="badge-editor">Redaktionens val</div><?php endif; ?>
        <?php if ($i < 3): ?><div class="badge-rank">#<?= $i+1 ?></div><?php endif; ?>
        <div class="produkt-bild">
          <?php if ($p['bild_url']): ?>
            <img src="<?= h($p['bild_url']) ?>" alt="<?= h($p['namn']) ?>" loading="lazy">
          <?php else: ?>
            <div class="bild-placeholder">⚡</div>
          <?php endif; ?>
        </div>
        <div class="produkt-body">
          <div class="produkt-kategori"><?= h($p['kategori_namn']) ?></div>
          <h3 class="produkt-namn"><a href="produkt.php?slug=<?= h($p['slug']) ?>"><?= h($p['namn']) ?></a></h3>
          <p class="produkt-desc"><?= h($p['kort_beskrivning']) ?></p>
          <div class="produkt-specs">
            <?php if ($p['effekt_kw']): ?>
            <span class="spec-chip">⚡ <?= h($p['effekt_kw']) ?> kW</span>
            <?php endif; ?>
            <?php if ($p['smart_laddning']): ?>
            <span class="spec-chip">📱 Smart</span>
            <?php endif; ?>
            <?php if ($p['app_styrning']): ?>
            <span class="spec-chip">📲 App</span>
            <?php endif; ?>
          </div>
          <div class="produkt-footer">
            <div class="produkt-betyg">
              <div class="stjarnor"><?= str_repeat('★', round($p['betyg'])) ?><?= str_repeat('☆', 5 - round($p['betyg'])) ?></div>
              <span><?= number_format($p['betyg'], 1) ?> (<?= $p['antal_recensioner'] ?> rec.)</span>
            </div>
            <div class="produkt-pris-rad">
              <?php if ($p['pris']): ?>
              <span class="pris">från <?= number_format($p['pris'], 0, ',', ' ') ?> kr</span>
              <?php endif; ?>
            </div>
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
  </div>
</section>

<!-- TOPPLISTA SIDEBAR STYLE -->
<section class="section-topplista">
  <div class="container">
    <div class="topplista-wrapper">
      <div class="topplista-huvud">
        <h2>Snabb topplista</h2>
        <p>Bäst betyg just nu</p>
      </div>
      <ol class="topplista">
        <?php foreach ($topplista as $i => $p): ?>
        <li class="topplista-item">
          <span class="rank"><?= $i+1 ?></span>
          <div class="topplista-info">
            <a href="produkt.php?slug=<?= h($p['slug']) ?>"><?= h($p['namn']) ?></a>
            <span class="kategori-tag"><?= h($p['kategori_namn']) ?></span>
          </div>
          <div class="topplista-right">
            <span class="betyg-pill"><?= number_format($p['betyg'], 1) ?> ★</span>
            <?php if ($p['pris']): ?>
            <span class="pris-liten"><?= number_format($p['pris'], 0, ',', ' ') ?> kr</span>
            <?php endif; ?>
          </div>
        </li>
        <?php endforeach; ?>
      </ol>
    </div>
  </div>
</section>

<!-- USP -->
<section class="section-usp">
  <div class="container">
    <div class="usp-grid">
      <div class="usp-kort">
        <div class="usp-ikon">🔍</div>
        <h3>Oberoende tester</h3>
        <p>Vi köper och testar produkterna själva. Inga sponsrade "recensioner".</p>
      </div>
      <div class="usp-kort">
        <div class="usp-ikon">📊</div>
        <h3>Faktabaserat</h3>
        <p>Alla betyg baseras på mätbara prestanda, inte magkänsla.</p>
      </div>
      <div class="usp-kort">
        <div class="usp-ikon">💰</div>
        <h3>Bästa priser</h3>
        <p>Vi jämför priser hos svenska återförsäljare så du slipper det.</p>
      </div>
      <div class="usp-kort">
        <div class="usp-ikon">🔄</div>
        <h3>Alltid uppdaterat</h3>
        <p>Marknaden förändras snabbt. Vi uppdaterar löpande.</p>
      </div>
    </div>
  </div>
</section>

<!-- AFFILIATE DISCLAIMER -->
<div class="affiliate-notice">
  <div class="container">
    <p>💡 <strong>Transparens:</strong> Vissa länkar på denna sida är affiliate-länkar. Det kostar dig inget extra, men vi får en liten provision om du köper via länken – vilket gör att vi kan fortsätta driva sajten.</p>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="js/main.js"></script>
</body>
</html>
