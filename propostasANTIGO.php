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
        $p = db_find('propostas',$id);
        if (!$p) { jsonResponse(['error'=>'Não encontrado'],404); }
        $u = db_find('users',(int)($p['usuario_id']??0));
        $p['user_nome'] = $u['nome']??'–';
        jsonResponse($p);
    }
    $lista = db_all('propostas','id DESC');
    foreach ($lista as &$p) {
        $u = db_find('users',(int)($p['usuario_id']??0));
        $p['user_nome'] = $u['nome']??'–';
    }
    jsonResponse($lista);
}

function nextNumeroProp(): string {
    $ano = date('Y');
    $st = db()->prepare("SELECT numero FROM propostas WHERE numero LIKE ? ORDER BY id DESC LIMIT 1");
    $st->execute(["PROP-$ano-%"]);
    $last = $st->fetchColumn();
    $seq  = $last ? ((int)substr($last, strrpos($last,'-')+1)+1) : 1;
    return "PROP-$ano-".str_pad($seq,4,'0',STR_PAD_LEFT);
}

switch ($action) {
case 'create_v2':
    $titulo = trim($body['titulo']??'');
    $valor  = (float)($body['valor']??0);
    if (!$titulo) { jsonResponse(['error'=>'Título obrigatório'],400); }
    $numero = nextNumeroProp();
    $extra  = [
        'cliente_nome'     => $body['cliente_nome']??'',
        'cliente_cnpj'     => $body['cliente_cnpj']??'',
        'cliente_end'      => $body['cliente_end']??'',
        'cliente_contato'  => $body['cliente_contato']??'',
        'local_exec'       => $body['local_exec']??'Fortaleza/CE',
        'data_prop'        => $body['data_prop']??date('Y-m-d'),
        'introducao'       => $body['introducao']??'',
        'objetivo'         => $body['objetivo']??'',
        'objetivos_esp'    => $body['objetivos_esp']??'',
        'metodologias'     => $body['metodologias']??[],
        'etapas_exec'      => $body['etapas_exec']??[],
        'cronograma'       => $body['cronograma']??[],
        'equipe_desc'      => $body['equipe_desc']??'',
        'equipe_membros'   => $body['equipe_membros']??[],
        'logistica'        => $body['logistica']??'',
        'qualidade'        => $body['qualidade']??'',
        'condicoes'        => $body['condicoes']??'',
        'obrig_contratante'=> $body['obrig_contratante']??'',
        'observacoes'      => $body['observacoes']??'',
        'grupos'           => $body['grupos']??[],
    ];
    $id = db_insert('propostas',[
        'numero'        => $numero,
        'lead_id'       => (int)($body['lead_id']??0)?:null,
        'empresa_id'    => null,
        'empresa_nome'  => $body['cliente_nome']??'',
        'usuario_id'    => $uid,
        'titulo'        => $titulo,
        'servico'       => $body['servico']??'',
        'escopo'        => $body['objetivo']??'',
        'valor'         => $valor,
        'prazo'         => $body['prazo']??'',
        'condicoes'     => $body['condicoes']??'',
        'validade'      => $body['validade']??null?:null,
        'status'        => $body['status']??'rascunho',
        'email_enviado' => 0,
        'data_envio'    => null,
        'data_aprovacao'=> null,
        'notas'         => '',
        'extra_data'    => json_encode($extra, JSON_UNESCAPED_UNICODE),
    ]);
    registrarLog($uid,'CREATE','propostas',$id,"Proposta $numero criada");
    jsonResponse(['success'=>true,'id'=>$id,'numero'=>$numero]);

case 'update_status':
    $id     = (int)($body['id']??0);
    $status = $body['status']??'';
    $validos= ['rascunho','enviada','em_negociacao','aprovada','rejeitada','cancelada'];
    if (!in_array($status,$validos)) { jsonResponse(['error'=>'Status inválido'],400); }
    $upd = ['status'=>$status];
    if ($status==='aprovada') $upd['data_aprovacao'] = agora();
    if ($status==='enviada')  $upd['data_envio']     = agora();
    db_update('propostas',$id,$upd);

    // Auto-gerar receita financeiro ao aprovar
    if ($status==='aprovada') {
        $prop = db_find('propostas',$id);
        $existing = db_where('financeiro',['proposta_id'=>$id]);
        if ($prop && empty($existing)) {
            $fin_id = db_insert('financeiro',[
                'proposta_id'    => $id,
                'empresa_id'     => $prop['empresa_id']??null,
                'usuario_id'     => $uid,
                'descricao'      => ($prop['empresa_nome']??'') . ' — ' . $prop['titulo'],
                'tipo'           => 'receita',
                'categoria'      => 'Projetos',
                'valor'          => (float)$prop['valor'],
                'data_vencimento'=> $prop['validade']??date('Y-m-d',strtotime('+30 days')),
                'data_pagamento' => null,
                'status'         => 'pendente',
                'forma_pagamento'=> 'pix',
                'observacao'     => 'Gerado automaticamente — Proposta '.$prop['numero'].' aprovada',
            ]);
            db_insert('notificacoes',[
                'usuario_id'=> $uid,
                'titulo'    => '🎉 Proposta Aprovada!',
                'mensagem'  => 'Receita de '.moeda((float)$prop['valor']).' lançada no Financeiro.',
                'tipo'      => 'sucesso','lida'=>0,'link'=>'financeiro.php',
            ]);
        }
    }
    registrarLog($uid,'UPDATE','propostas',$id,"Status → $status");
    jsonResponse(['success'=>true]);

case 'delete':
    $id = (int)($body['id']??0);
    db_delete('propostas',$id);
    jsonResponse(['success'=>true]);
}
jsonResponse(['error'=>'Ação inválida'],400);
