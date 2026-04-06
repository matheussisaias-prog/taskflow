<?php
// includes/auth_check.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$user_id     = (int)$_SESSION['user_id'];
$user_nome   = $_SESSION['user_nome']   ?? 'Usuário';
$user_email  = $_SESSION['user_email']  ?? '';
$user_perfil = $_SESSION['user_perfil'] ?? 'tecnico';

function can(string $perfil): bool {
    global $user_perfil;
    $ordem = ['tecnico'=>1,'financeiro'=>2,'comercial'=>3,'gerente'=>4,'admin'=>5];
    return ($ordem[$user_perfil] ?? 0) >= ($ordem[$perfil] ?? 99);
}
