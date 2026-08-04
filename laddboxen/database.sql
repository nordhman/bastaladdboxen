-- BästaLaddboxen.se - Databasstruktur
-- Kör detta i din MySQL/phpMyAdmin

CREATE DATABASE IF NOT EXISTS laddboxen CHARACTER SET utf8mb4 COLLATE utf8mb4_swedish_ci;
USE laddboxen;

CREATE TABLE kategorier (
  id INT AUTO_INCREMENT PRIMARY KEY,
  namn VARCHAR(100) NOT NULL,
  slug VARCHAR(100) NOT NULL UNIQUE,
  beskrivning TEXT,
  ikon VARCHAR(50),
  skapad TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE produkter (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kategori_id INT,
  namn VARCHAR(200) NOT NULL,
  slug VARCHAR(200) NOT NULL UNIQUE,
  bild_url VARCHAR(500),
  kort_beskrivning VARCHAR(300),
  lang_beskrivning TEXT,
  pris DECIMAL(10,2),
  affiliate_url VARCHAR(500),
  affiliate_butik VARCHAR(100),
  betyg DECIMAL(3,1) DEFAULT 0,
  antal_recensioner INT DEFAULT 0,
  effekt_kw DECIMAL(5,2),
  laddtid_timmar DECIMAL(5,1),
  installation VARCHAR(100),
  smart_laddning BOOLEAN DEFAULT FALSE,
  app_styrning BOOLEAN DEFAULT FALSE,
  framhavd BOOLEAN DEFAULT FALSE,
  aktiv BOOLEAN DEFAULT TRUE,
  skapad TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (kategori_id) REFERENCES kategorier(id)
);

CREATE TABLE spec_varden (
  id INT AUTO_INCREMENT PRIMARY KEY,
  produkt_id INT,
  spec_namn VARCHAR(100),
  spec_varde VARCHAR(200),
  FOREIGN KEY (produkt_id) REFERENCES produkter(id)
);

-- Exempeldata - kategorier
INSERT INTO kategorier (namn, slug, beskrivning, ikon) VALUES
('Hemmaladdboxar', 'hemmaladdboxar', 'Laddboxar för hemmabruk – montera i garaget eller på väggen', '🏠'),
('Portabla laddare', 'portabla-laddare', 'Ta med laddaren vart du än åker', '🎒'),
('Laddkablar', 'laddkablar', 'Kablar för typ 1, typ 2 och CCS', '🔌'),
('Tillbehör', 'tillbehor', 'Skydd, adaptrar och smarta tillbehör', '⚡');

-- Exempelprodukter
INSERT INTO produkter (kategori_id, namn, slug, kort_beskrivning, pris, affiliate_url, affiliate_butik, betyg, antal_recensioner, effekt_kw, laddtid_timmar, installation, smart_laddning, app_styrning, framhavd, bild_url) VALUES
(1, 'Easee Home', 'easee-home', 'Nordens mest sålda laddbox – snygg, smart och enkel att installera', 4995.00, 'https://example.com/affiliate/easee-home', 'Inet', 4.8, 342, 22.0, 2.5, 'El-installatör krävs', TRUE, TRUE, TRUE, 'https://images.unsplash.com/photo-1593941707882-a5bba14938c7?w=400'),
(1, 'Zaptec Go', 'zaptec-go', 'Prisbelönad design med intelligent lastbalansering', 5490.00, 'https://example.com/affiliate/zaptec-go', 'Elgiganten', 4.7, 218, 22.0, 2.5, 'El-installatör krävs', TRUE, TRUE, TRUE, 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400'),
(1, 'Wallbox Pulsar Plus', 'wallbox-pulsar-plus', 'Kompakt och kraftfull med inbyggd WiFi och Bluetooth', 4290.00, 'https://example.com/affiliate/wallbox-pulsar', 'Amazon', 4.6, 189, 22.0, 3.0, 'El-installatör krävs', TRUE, TRUE, FALSE, 'https://images.unsplash.com/photo-1593941707882-a5bba14938c7?w=400'),
(2, 'Juice Booster 2', 'juice-booster-2', 'Världens smartaste portabla laddare – passar alla uttag', 3490.00, 'https://example.com/affiliate/juice-booster', 'Kjell & Co', 4.5, 97, 11.0, 5.0, 'Ingen installation', FALSE, FALSE, TRUE, 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=400'),
(3, 'Typ 2 Kabel 7.4kW 5m', 'typ2-kabel-74kw-5m', 'Robust spiralkabel med typ 2 kontakter för hemmaladdning', 890.00, 'https://example.com/affiliate/typ2-kabel', 'Inet', 4.4, 156, 7.4, 8.0, 'Ingen installation', FALSE, FALSE, FALSE, 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400');

INSERT INTO spec_varden (produkt_id, spec_namn, spec_varde) VALUES
(1, 'Max effekt', '22 kW'),
(1, 'Anslutning', 'Typ 2'),
(1, 'Kabelläng', '4.5 m'),
(1, 'IP-klass', 'IP54'),
(1, 'Garanti', '3 år'),
(2, 'Max effekt', '22 kW'),
(2, 'Anslutning', 'Typ 2'),
(2, 'Kabelläng', '7.5 m'),
(2, 'IP-klass', 'IP54'),
(2, 'Garanti', '5 år');
