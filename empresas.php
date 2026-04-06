<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['user_id'])) { jsonResponse(['error'=>'Não autorizado'],401); }

$body   = json_decode(file_get_contents('php://input'),true) ?? [];
$action = $body['action'] ?? $_GET['action'] ?? '';
$uid    = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD']==='GET' && !$action) {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    if ($id) { $e=db_find('empresas',$id); jsonResponse($e?:['error'=>'Não encontrado']); }
    jsonResponse(db_all('empresas','nome ASC'));
}

switch ($action) {
case 'create':
    $nome = trim($body['nome']??'');
    if (!$nome) { jsonResponse(['error'=>'Nome obrigatório'],400); }
    $id = db_insert('empresas',[
        'nome'     => $nome,
        'cnpj'     => $body['cnpj']??'',
        'email'    => $body['email']??'',
        'telefone' => $body['telefone']??'',
        'endereco' => $body['endereco']??'',
        'cidade'   => $body['cidade']??'',
        'estado'   => $body['estado']??'',
        'contato'  => $body['contato']??'',
        'segmento' => $body['segmento']??'',
        'notas'    => $body['notas']??'',
        'ativo'    => 1,
    ]);
    registrarLog($uid,'CREATE','empresas',$id,"Empresa $nome criada");
    jsonResponse(['success'=>true,'id'=>$id]);

case 'update':
    $id = (int)($body['id']??0);
    db_update('empresas',$id,[
        'nome'     => $body['nome']??'',
        'cnpj'     => $body['cnpj']??'',
        'email'    => $body['email']??'',
        'telefone' => $body['telefone']??'',
        'endereco' => $body['endereco']??'',
        'cidade'   => $body['cidade']??'',
        'estado'   => $body['estado']??'',
        'contato'  => $body['contato']??'',
        'segmento' => $body['segmento']??'',
        'notas'    => $body['notas']??'',
    ]);
    jsonResponse(['success'=>true]);

case 'delete':
    $id = (int)($body['id']??0);
    db_delete('empresas',$id);
    jsonResponse(['success'=>true]);
}
jsonResponse(['error'=>'Ação inválida'],400);
