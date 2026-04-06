<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['user_id'])) { jsonResponse(['error'=>'Não autorizado'],401); }
$body   = json_decode(file_get_contents('php://input'),true) ?? [];
$action = $body['action'] ?? '';
$uid    = (int)$_SESSION['user_id'];

if ($action==='mark_all_read') {
    db()->prepare("UPDATE notificacoes SET lida=1 WHERE usuario_id=?")->execute([$uid]);
    jsonResponse(['success'=>true]);
}
if ($action==='list') {
    $notifs = db_where_raw('notificacoes','usuario_id=? AND lida=0',[$uid],'id DESC');
    jsonResponse(['notifications'=>$notifs,'count'=>count($notifs)]);
}
jsonResponse(['error'=>'Ação inválida'],400);
