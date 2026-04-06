<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['user_id'])) { jsonResponse(['error'=>'Não autorizado'],401); }

$body   = json_decode(file_get_contents('php://input'),true) ?? [];
$action = $body['action'] ?? $_POST['action'] ?? $_GET['action'] ?? '';
$uid    = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD']==='GET' && !$action) {
    $lista = db_all('financeiro','id DESC');
    foreach ($lista as &$f) {
        $emp = $f['empresa_id'] ? db_find('empresas',(int)$f['empresa_id']) : null;
        $f['empresa_nome'] = $emp['nome']??'–';
    }
    jsonResponse($lista);
}

switch ($action) {
case 'create':
    $desc  = trim($body['descricao']??'');
    $tipo  = $body['tipo']??'';
    $valor = (float)($body['valor']??0);
    if (!$desc)   { jsonResponse(['error'=>'Descrição obrigatória'],400); }
    if (!$tipo)   { jsonResponse(['error'=>'Tipo obrigatório'],400); }
    if ($valor<=0){ jsonResponse(['error'=>'Valor deve ser maior que zero'],400); }
    $id = db_insert('financeiro',[
        'proposta_id'    => (int)($body['proposta_id']??0)?:null,
        'os_id'          => (int)($body['os_id']??0)?:null,
        'empresa_id'     => (int)($body['empresa_id']??0)?:null,
        'usuario_id'     => $uid,
        'descricao'      => $desc,
        'tipo'           => $tipo,
        'categoria'      => $body['categoria']??'',
        'valor'          => $valor,
        'data_vencimento'=> $body['data_vencimento']??null?:null,
        'data_pagamento' => null,
        'status'         => 'pendente',
        'forma_pagamento'=> $body['forma_pagamento']??'pix',
        'observacao'     => $body['observacao']??'',
    ]);
    registrarLog($uid,'CREATE','financeiro',$id,"Lançamento $tipo: $desc");
    jsonResponse(['success'=>true,'id'=>$id]);

case 'update':
    $id = (int)($body['id']??0);
    db_update('financeiro',$id,[
        'descricao'      => $body['descricao']??'',
        'tipo'           => $body['tipo']??'receita',
        'categoria'      => $body['categoria']??'',
        'valor'          => (float)($body['valor']??0),
        'data_vencimento'=> $body['data_vencimento']??null?:null,
        'empresa_id'     => (int)($body['empresa_id']??0)?:null,
        'forma_pagamento'=> $body['forma_pagamento']??'pix',
        'observacao'     => $body['observacao']??'',
    ]);
    jsonResponse(['success'=>true]);

case 'pagar':
    $id = (int)($body['id']??0);
    db_update('financeiro',$id,[
        'status'         => 'pago',
        'data_pagamento' => $body['data_pagamento']??date('Y-m-d'),
        'forma_pagamento'=> $body['forma_pagamento']??'pix',
    ]);
    registrarLog($uid,'UPDATE','financeiro',$id,"Lançamento #{$id} marcado como pago");
    jsonResponse(['success'=>true]);

case 'cancelar':
    $id = (int)($body['id']??0);
    db_update('financeiro',$id,['status'=>'cancelado']);
    jsonResponse(['success'=>true]);

case 'delete':
    $id = (int)($body['id']??0);
    db_delete('financeiro',$id);
    jsonResponse(['success'=>true]);

case 'resumo':
    $mes = $body['mes'] ?? date('Y-m');
    $st_r = db()->prepare("SELECT COALESCE(SUM(valor),0) FROM financeiro WHERE tipo='receita' AND status='pago' AND DATE_FORMAT(data_pagamento,'%Y-%m')=?");
    $st_r->execute([$mes]); $rec = (float)$st_r->fetchColumn();
    $st_d = db()->prepare("SELECT COALESCE(SUM(valor),0) FROM financeiro WHERE tipo='despesa' AND status='pago' AND DATE_FORMAT(data_pagamento,'%Y-%m')=?");
    $st_d->execute([$mes]); $desp = (float)$st_d->fetchColumn();
    $st_p = db()->prepare("SELECT COALESCE(SUM(valor),0) FROM financeiro WHERE tipo='receita' AND status='pendente'");
    $st_p->execute(); $pend = (float)$st_p->fetchColumn();
    $st_a = db()->prepare("SELECT COALESCE(SUM(valor),0) FROM financeiro WHERE status='pendente' AND data_vencimento < CURDATE()");
    $st_a->execute(); $atras = (float)$st_a->fetchColumn();
    jsonResponse(['receitas'=>$rec,'despesas'=>$desp,'pendentes'=>$pend,'atrasados'=>$atras,'saldo'=>$rec-$desp]);
}

jsonResponse(['error'=>'Ação inválida'],400);
