<?php
// ============================================================
// config.php — Configurações Terra System CRM
// Copie este arquivo para config.php e preencha os valores
// ============================================================

// ── BANCO DE DADOS MySQL ───────────────────────────────────
define('DB_HOST',    'localhost');
define('DB_NAME',    'terra_system');   // nome do banco
define('DB_USER',    'root');            // usuário do banco
define('DB_PASS',    '');                // senha do banco
define('DB_CHARSET', 'utf8mb4');

// ── SISTEMA ───────────────────────────────────────────────
define('APP_NAME', 'Terra System CRM');

// ── E-MAIL ────────────────────────────────────────────────
define('MAIL_FROM',      'contato@seudominio.com.br');
define('MAIL_FROM_NAME', 'Terra System');

// ── URL BASE AUTOMÁTICA ───────────────────────────────────
if (!defined('BASE_URL')) {
    $system_root = str_replace('\\', '/', __DIR__);
    $doc_root    = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
    if ($doc_root && strpos($system_root, $doc_root) === 0) {
        $base = substr($system_root, strlen($doc_root));
    } else {
        $base = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')));
        if ($base === '.' || $base === '/') $base = '';
    }
    define('BASE_URL', rtrim($base, '/'));
}

// ── AMBIENTE ──────────────────────────────────────────────
$host = $_SERVER['HTTP_HOST'] ?? 'cli';
define('IS_LOCAL', in_array($host, ['localhost','127.0.0.1']) || str_starts_with($host, '192.168.'));
