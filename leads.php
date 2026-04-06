<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['user_id'])) { jsonResponse(['error'=>'Não autorizado'],401); }

$body   = json_decode(file_get_contents('php://input'),true) ?? [];
$action = $body['action'] ?? $_POST['action'] ?? $_GET['action'] ?? '';
$uid    = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD']==='GET' && !$action) {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    if ($id) {
        $lead = db_find('leads',$id);
        if (!$lead) { jsonResponse(['error'=>'Não encontrado'],404); }
        $atend = db_where_raw('atendimentos','lead_id=?',[$id],'id DESC');
        foreach ($atend as &$a) {
            $u = db_find('users',(int)$a['usuario_id']);
            $a['usuario_nome'] = $u['nome']??'-';
        }
        $lead['atendimentos'] = $atend;
        $resp = db_find('users',(int)($lead['responsavel_id']??0));
        $lead['responsavel_nome'] = $resp['nome']??'–';
        jsonResponse($lead);
    }
    $leads = db_all('leads','id DESC');
    foreach ($leads as &$l) {
        $u = db_find('users',(int)($l['responsavel_id']??0));
        $l['responsavel_nome'] = $u['nome']??'–';
    }
    jsonResponse($leads);
}

switch ($action) {

case 'create':
    $nome = trim($_POST['nome']??$body['nome']??'');
    if (!$nome) { jsonResponse(['error'=>'Nome obrigatório'],400); }
    $id = db_insert('leads',[
        'nome'           => $nome,
        'empresa_id'     => (int)($_POST['empresa_id']??$body['empresa_id']??0)?:null,
        'empresa_nome'   => $_POST['empresa_nome']??$body['empresa_nome']??'',
        'email'          => $_POST['email']??$body['email']??'',
        'telefone'       => $_POST['telefone']??$body['telefone']??'',
        'cargo'          => $_POST['cargo']??$body['cargo']??'',
        'origem'         => $_POST['origem']??$body['origem']??'outro',
        'responsavel_id' => (int)($_POST['responsavel_id']??$body['responsavel_id']??$uid),
        'etapa'          => 'lead_recebido',
        'temperatura'    => $_POST['temperatura']??$body['temperatura']??'morno',
        'valor_estimado' => (float)($_POST['valor_estimado']??$body['valor_estimado']??0),
        'data_follow_up' => ($_POST['data_follow_up']??$body['data_follow_up']??'')?:null,
        'notas'          => $_POST['notas']??$body['notas']??'',
        'motivo_perda'   => '',
    ]);
    registrarLog($uid,'CREATE','leads',$id,"Lead $nome criado");
    jsonResponse(['success'=>true,'id'=>$id]);

case 'update_etapa':
    $id    = (int)($body['id']??0);
    $etapa = $body['etapa']??'';
    $validas=['lead_recebido','contato_realizado','proposta_enviada','negociacao','fechado','perdido'];
    if (!in_array($etapa,$validas)) { jsonResponse(['error'=>'Etapa inválida'],400); }
    db_update('leads',$id,['etapa'=>$etapa]);
    registrarLog($uid,'UPDATE','leads',$id,"Etapa → $etapa");
    jsonResponse(['success'=>true]);

case 'add_atendimento':
    $lead_id = (int)($body['lead_id']??0);
    $desc    = trim($body['descricao']??'');
    if (!$desc) { jsonResponse(['error'=>'Descrição obrigatória'],400); }
    db_insert('atendimentos',[
        'lead_id'          => $lead_id,
        'usuario_id'       => $uid,
        'tipo'             => $body['tipo']??'nota',
        'descricao'        => $desc,
        'data_atendimento' => agora(),
        'proximo_contato'  => $body['proximo_contato']??null?:null,
    ]);
    jsonResponse(['success'=>true]);

case 'delete':
    $id = (int)($body['id']??0);
    db_delete('leads',$id);
    jsonResponse(['success'=>true]);
}

jsonResponse(['error'=>'Ação inválida'],400);
