<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['user_id'])) { jsonResponse(['error'=>'Não autorizado'],401); }

$body   = json_decode(file_get_contents('php://input'),true) ?? [];
$action = $body['action'] ?? $_GET['action'] ?? '';
$uid    = (int)$_SESSION['user_id'];
$perfil = $_SESSION['user_perfil'] ?? 'tecnico';

// Ping DB
if ($action==='ping') {
    try { db(); jsonResponse(['ok'=>true]); }
    catch(Throwable $e) { jsonResponse(['ok'=>false,'error'=>$e->getMessage()]); }
}

// Listar (sem senhas)
if ($_SERVER['REQUEST_METHOD']==='GET' && !$action) {
    $lista = db_all('users','nome ASC');
    foreach ($lista as &$u) unset($u['senha']);
    jsonResponse($lista);
}

// Alterar própria senha
if ($action==='change_password') {
    $senha = $body['senha']??'';
    if (strlen($senha)<6) { jsonResponse(['error'=>'Mínimo 6 caracteres'],400); }
    db_update('users',$uid,['senha'=>password_hash($senha,PASSWORD_DEFAULT)]);
    registrarLog($uid,'UPDATE','users',$uid,'Senha alterada');
    jsonResponse(['success'=>true]);
}

// Admin only abaixo
if ($perfil!=='admin') { jsonResponse(['error'=>'Sem permissão'],403); }

switch ($action) {
case 'create':
    $nome  = trim($body['nome']??'');
    $email = trim($body['email']??'');
    $senha = $body['senha']??'';
    if (!$nome||!$email) { jsonResponse(['error'=>'Nome e e-mail obrigatórios'],400); }
    if (strlen($senha)<6){ jsonResponse(['error'=>'Senha mínima: 6 caracteres'],400); }
    // Verificar e-mail único
    $exist = db_where('users',['email'=>$email]);
    if ($exist) { jsonResponse(['error'=>'E-mail já cadastrado'],400); }
    $id = db_insert('users',[
        'nome'     => $nome,
        'email'    => $email,
        'senha'    => password_hash($senha,PASSWORD_DEFAULT),
        'perfil'   => $body['perfil']??'tecnico',
        'cargo'    => $body['cargo']??'',
        'telefone' => $body['telefone']??'',
        'ativo'    => 1,
    ]);
    registrarLog($uid,'CREATE','users',$id,"Usuário $nome criado");
    jsonResponse(['success'=>true,'id'=>$id]);

case 'update':
    $id    = (int)($body['id']??0);
    $upd   = [
        'nome'     => $body['nome']??'',
        'email'    => $body['email']??'',
        'perfil'   => $body['perfil']??'tecnico',
        'cargo'    => $body['cargo']??'',
        'telefone' => $body['telefone']??'',
    ];
    if (!empty($body['senha']) && strlen($body['senha'])>=6) {
        $upd['senha'] = password_hash($body['senha'],PASSWORD_DEFAULT);
    }
    db_update('users',$id,$upd);
    registrarLog($uid,'UPDATE','users',$id,"Usuário #{$id} atualizado");
    jsonResponse(['success'=>true]);

case 'toggle':
    $id    = (int)($body['id']??0);
    $ativo = $body['ativo'] ? 1 : 0;
    db_update('users',$id,['ativo'=>$ativo]);
    jsonResponse(['success'=>true]);
}

jsonResponse(['error'=>'Ação inválida'],400);
