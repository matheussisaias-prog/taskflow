<?php
// ============================================================
// config.php — Configurações Terra System CRM
// ============================================================

// ── BANCO DE DADOS MySQL (XAMPP) ──────────────────────────
define('DB_HOST',    'localhost');
define('DB_NAME',    'system');   // nome do banco no phpMyAdmin
define('DB_USER',    'root');          // usuário padrão XAMPP
define('DB_PASS',    '');              // senha padrão XAMPP é vazia
define('DB_CHARSET', 'utf8mb4');

// ── SISTEMA ───────────────────────────────────────────────
define('APP_NAME', 'Terra System CRM');

// ── E-MAIL ────────────────────────────────────────────────
define('MAIL_FROM',      'contato@terrasystem.com.br');
define('MAIL_FROM_NAME', 'Terra System Geologia e Meio Ambiente');

// ── URL BASE AUTOMÁTICA ───────────────────────────────────
// Calcula automaticamente o caminho relativo ao DOCUMENT_ROOT
// Funciona independente de onde a pasta foi colocada no XAMPP
if (!defined('BASE_URL')) {
    // __DIR__ = pasta onde está este config.php (raiz do sistema)
    $system_root = str_replace('\\', '/', __DIR__);
    $doc_root    = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
    if ($doc_root && strpos($system_root, $doc_root) === 0) {
        $base = substr($system_root, strlen($doc_root));
    } else {
        // Fallback: tenta via SCRIPT_NAME
        $base = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')));
        if ($base === '.' || $base === '/') $base = '';
    }
    define('BASE_URL', rtrim($base, '/'));
}

// ── AMBIENTE ──────────────────────────────────────────────
$host = $_SERVER['HTTP_HOST'] ?? 'cli';
define('IS_LOCAL', in_array($host, ['localhost','127.0.0.1']) || str_starts_with($host, '192.168.'));
