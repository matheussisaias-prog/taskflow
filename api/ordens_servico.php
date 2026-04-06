<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['user_id'])) { jsonResponse(['error'=>'Não autorizado'],401); }

$body   = json_decode(file_get_contents('php://input'),true) ?? [];
$action = $body['action'] ?? $_GET['action'] ?? '';
$uid    = (int)$_SESSION['user_id'];

// GET: listar ou buscar por ID
if ($_SERVER['REQUEST_METHOD']==='GET' && !$action) {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    if ($id) {
        $os = db_find('ordens_servico', $id);
        if (!$os) { jsonResponse(['error'=>'Não encontrado'],404); }
        $emp  = $os['empresa_id']     ? db_find('empresas',(int)$os['empresa_id'])    : null;
        $resp = $os['responsavel_id'] ? db_find('users',(int)$os['responsavel_id'])   : null;
        $prop = $os['proposta_id']    ? db_find('propostas',(int)$os['proposta_id'])  : null;
        $os['empresa_nome_ref']  = $emp['nome']    ?? '–';
        $os['responsavel_nome']  = $resp['nome']   ?? '–';
        $os['proposta_numero']   = $prop['numero'] ?? '–';
        // Lançamentos financeiros vinculados
        $os['lancamentos'] = db_where('financeiro',['os_id'=>$id]);
        jsonResponse($os);
    }
    $lista = db_all('ordens_servico','id DESC');
    foreach ($lista as &$o) {
        $emp  = $o['empresa_id']     ? db_find('empresas',(int)$o['empresa_id'])  : null;
        $resp = $o['responsavel_id'] ? db_find('users',(int)$o['responsavel_id']) : null;
        $o['empresa_nome_ref'] = $emp['nome']  ?? '–';
        $o['responsavel_nome'] = $resp['nome'] ?? '–';
    }
    jsonResponse($lista);
}

function camposOS(array $in): array {
    return [
        'empresa_id'           => (int)($in['empresa_id']         ?? 0) ?: null,
        'empresa_cnpj'         => $in['empresa_cnpj']             ?? '',
        'contato_empresa'      => $in['contato_empresa']          ?? '',
        'email_contato'        => $in['email_contato']            ?? '',
        'telefone_contato'     => $in['telefone_contato']         ?? '',
        'proposta_id'          => (int)($in['proposta_id']        ?? 0) ?: null,
        'proposta_ref'         => $in['proposta_ref']             ?? '',
        'num_contrato'         => $in['num_contrato']             ?? '',
        'titulo'               => trim($in['titulo']              ?? ''),
        'tipo_servico'         => $in['tipo_servico']             ?? '',
        'descricao'            => $in['descricao']                ?? '',
        'responsavel_id'       => (int)($in['responsavel_id']     ?? 0) ?: null,
        'prioridade'           => $in['prioridade']               ?? 'media',
        'status'               => $in['status']                   ?? 'aberta',
        'data_abertura'        => $in['data_abertura']            ?? date('Y-m-d'),
        'data_prazo'           => $in['data_prazo']               ?? null ?: null,
        'data_conclusao'       => $in['data_conclusao']           ?? null ?: null,
        'horas_estimadas'      => (float)($in['horas_estimadas']  ?? 0) ?: null,
        'horas_realizadas'     => (float)($in['horas_realizadas'] ?? 0) ?: null,
        'valor_hora'           => (float)($in['valor_hora']       ?? 0),
        'local_execucao'       => $in['local_execucao']           ?? 'remoto',
        'endereco'             => $in['endereco']                 ?? '',
        'custo_mao_obra'       => (float)($in['custo_mao_obra']       ?? 0),
        'custo_materiais'      => (float)($in['custo_materiais']      ?? 0),
        'custo_deslocamento'   => (float)($in['custo_deslocamento']   ?? 0),
        'custo_equipamentos'   => (float)($in['custo_equipamentos']   ?? 0),
        'custo_terceiros'      => (float)($in['custo_terceiros']      ?? 0),
        'custo_software'       => (float)($in['custo_software']       ?? 0),
        'custo_alimentacao'    => (float)($in['custo_alimentacao']    ?? 0),
        'custo_hospedagem'     => (float)($in['custo_hospedagem']     ?? 0),
        'custo_outros'         => (float)($in['custo_outros']         ?? 0),
        'desconto_pct'         => (float)($in['desconto_pct']         ?? 0),
        'desconto_fixo'        => (float)($in['desconto_fixo']        ?? 0),
        'motivo_desconto'      => $in['motivo_desconto']          ?? '',
        'iss_pct'              => (float)($in['iss_pct']              ?? 0),
        'pis_cofins_pct'       => (float)($in['pis_cofins_pct']       ?? 0),
        'csll_pct'             => (float)($in['csll_pct']             ?? 0),
        'irpj_pct'             => (float)($in['irpj_pct']             ?? 0),
        'outros_impostos_pct'  => (float)($in['outros_impostos_pct']  ?? 0),
        'margem_lucro_pct'     => (float)($in['margem_lucro_pct']     ?? 0),
        'forma_cobranca'       => $in['forma_cobranca']           ?? 'unico',
        'condicoes_pagamento'  => $in['condicoes_pagamento']      ?? '',
        'data_vencimento'      => $in['data_vencimento']          ?? null ?: null,
        'observacoes'          => $in['observacoes']              ?? '',
        'garantia'             => $in['garantia']                 ?? '',
    ];
}

// Calcula valor total com impostos e descontos
function calcValorOS(array $campos): float {
    $subtotal = array_sum([
        $campos['custo_mao_obra'],$campos['custo_materiais'],$campos['custo_deslocamento'],
        $campos['custo_equipamentos'],$campos['custo_terceiros'],$campos['custo_software'],
        $campos['custo_alimentacao'],$campos['custo_hospedagem'],$campos['custo_outros']
    ]);
    // Margem de lucro
    $subtotal = $subtotal * (1 + $campos['margem_lucro_pct']/100);
    // Desconto %
    $subtotal = $subtotal * (1 - $campos['desconto_pct']/100);
    // Desconto fixo
    $subtotal -= $campos['desconto_fixo'];
    // Impostos
    $imp_total = $campos['iss_pct']+$campos['pis_cofins_pct']+$campos['csll_pct']+$campos['irpj_pct']+$campos['outros_impostos_pct'];
    $subtotal  = $subtotal * (1 + $imp_total/100);
    return max(0, round($subtotal, 2));
}

switch ($action) {

case 'create':
    $campos = camposOS($body);
    if (!$campos['titulo'])     { jsonResponse(['error'=>'Título obrigatório'],422); }
    if (!$campos['empresa_id']) { jsonResponse(['error'=>'Empresa obrigatória'],422); }

    $campos['numero'] = proximoNumero('OS','ordens_servico');
    $id = db_insert('ordens_servico', $campos);
    registrarLog($uid,'CREATE','ordens_servico',$id,"OS {$campos['numero']} criada");
    jsonResponse(['success'=>true,'id'=>$id,'numero'=>$campos['numero']]);

case 'update':
    $id = (int)($body['id']??0);
    if (!$id) { jsonResponse(['error'=>'ID inválido'],422); }
    $campos = camposOS($body);
    db_update('ordens_servico',$id,$campos);
    registrarLog($uid,'UPDATE','ordens_servico',$id,"OS #{$id} atualizada");
    jsonResponse(['success'=>true]);

case 'update_status':
    $id     = (int)($body['id']??0);
    $status = $body['status']??'';
    $validos = ['aberta','em_andamento','aguardando','concluida','cancelada'];
    if (!in_array($status,$validos)) { jsonResponse(['error'=>'Status inválido'],400); }
    $upd = ['status'=>$status];
    if ($status==='concluida') $upd['data_conclusao'] = date('Y-m-d');
    db_update('ordens_servico',$id,$upd);
    registrarLog($uid,'UPDATE','ordens_servico',$id,"Status → $status");
    jsonResponse(['success'=>true]);

case 'gerar_financeiro':
    // Integração OS → Financeiro
    $id = (int)($body['id']??0);
    $os = db_find('ordens_servico',$id);
    if (!$os) { jsonResponse(['error'=>'OS não encontrada'],404); }
    if ($os['financeiro_gerado']) { jsonResponse(['error'=>'Lançamento financeiro já gerado para esta OS'],400); }

    $valor = calcValorOS([
        'custo_mao_obra'      => $os['custo_mao_obra'],
        'custo_materiais'     => $os['custo_materiais'],
        'custo_deslocamento'  => $os['custo_deslocamento'],
        'custo_equipamentos'  => $os['custo_equipamentos'],
        'custo_terceiros'     => $os['custo_terceiros'],
        'custo_software'      => $os['custo_software'],
        'custo_alimentacao'   => $os['custo_alimentacao'],
        'custo_hospedagem'    => $os['custo_hospedagem'],
        'custo_outros'        => $os['custo_outros'],
        'margem_lucro_pct'    => $os['margem_lucro_pct'],
        'desconto_pct'        => $os['desconto_pct'],
        'desconto_fixo'       => $os['desconto_fixo'],
        'iss_pct'             => $os['iss_pct'],
        'pis_cofins_pct'      => $os['pis_cofins_pct'],
        'csll_pct'            => $os['csll_pct'],
        'irpj_pct'            => $os['irpj_pct'],
        'outros_impostos_pct' => $os['outros_impostos_pct'],
    ]);

    $emp = $os['empresa_id'] ? db_find('empresas',(int)$os['empresa_id']) : null;
    $fin_id = db_insert('financeiro',[
        'os_id'          => $id,
        'empresa_id'     => $os['empresa_id'],
        'usuario_id'     => $uid,
        'descricao'      => ($emp['nome']??'') . ' — ' . $os['titulo'] . ' (' . $os['numero'] . ')',
        'tipo'           => 'receita',
        'categoria'      => 'Serviços / OS',
        'valor'          => $valor > 0 ? $valor : 1,
        'data_vencimento'=> $os['data_vencimento'] ?? date('Y-m-d', strtotime('+30 days')),
        'data_pagamento' => null,
        'status'         => 'pendente',
        'forma_pagamento'=> 'pix',
        'observacao'     => 'Gerado automaticamente da OS ' . $os['numero'],
    ]);
    db_update('ordens_servico',$id,['financeiro_gerado'=>1]);
    registrarLog($uid,'CREATE','financeiro',$fin_id,"Lançamento gerado da OS {$os['numero']}");
    jsonResponse(['success'=>true,'financeiro_id'=>$fin_id,'valor'=>$valor]);

case 'delete':
    $id = (int)($body['id']??0);
    db_delete('ordens_servico',$id);
    jsonResponse(['success'=>true]);
}

jsonResponse(['error'=>'Ação inválida'],400);
