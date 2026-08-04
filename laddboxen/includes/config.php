<?php
// =============================================
// KONFIGURATION - Ändra dessa värden!
// =============================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'laddboxen');
define('DB_USER', 'ditt_användarnamn');   // ← Ändra
define('DB_PASS', 'ditt_lösenord');        // ← Ändra
define('SITE_URL', 'https://bästaladdboxen.se'); // ← Ändra
define('SITE_NAME', 'BästaLaddboxen.se');

// Affiliate-inställningar
define('AFFILIATE_TAG', 'ditt-tag-20'); // Amazon affiliate tag

// =============================================

function db() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
            );
        } catch (PDOException $e) {
            die('Databasfel: ' . $e->getMessage());
        }
    }
    return $pdo;
}

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

session_start();
