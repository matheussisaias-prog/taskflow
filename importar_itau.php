<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['user_id'])) { jsonResponse(['error'=>'Não autorizado'],401); }

$uid = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error'=>'Método inválido'],405);
}

$action = $_POST['action'] ?? '';

// ── PREVIEW: parse o CSV e devolve os lançamentos sem salvar ──────────────
if ($action === 'preview') {
    if (empty($_FILES['csv']['tmp_name'])) {
        jsonResponse(['error'=>'Nenhum arquivo enviado'],400);
    }
    $result = parsearExtratoItau($_FILES['csv']['tmp_name']);
    jsonResponse($result);
}

// ── IMPORT: salva os lançamentos selecionados ────────────────────────────
if ($action === 'import') {
    $itens_json = $_POST['itens'] ?? '[]';
    $itens = json_decode($itens_json, true);
    if (!is_array($itens) || empty($itens)) {
        jsonResponse(['error'=>'Nenhum item para importar'],400);
    }
    $salvos = 0;
    $erros  = [];
    foreach ($itens as $item) {
        $desc  = trim($item['descricao'] ?? '');
        $valor = abs((float)($item['valor'] ?? 0));
        $data  = $item['data'] ?? date('Y-m-d');
        $tipo  = ((float)($item['valor'] ?? 0)) < 0 ? 'despesa' : 'receita';
        if (!$desc || $valor <= 0) continue;
        // converte data dd/mm/yyyy → yyyy-mm-dd
        if (preg_match('#^(\d{2})/(\d{2})/(\d{4})$#', $data, $m)) {
            $data = "{$m[3]}-{$m[2]}-{$m[1]}";
        }
        try {
            db_insert('financeiro', [
                'proposta_id'     => null,
                'os_id'           => null,
                'empresa_id'      => null,
                'usuario_id'      => $uid,
                'descricao'       => $desc,
                'tipo'            => $tipo,
                'categoria'       => $tipo === 'despesa' ? 'Importado Itaú' : 'Importado Itaú',
                'valor'           => $valor,
                'data_vencimento' => $data,
                'data_pagamento'  => $data,
                'status'          => 'pago',
                'forma_pagamento' => detectarForma($desc),
                'observacao'      => 'Importado via extrato Itaú',
            ]);
            $salvos++;
        } catch (Exception $e) {
            $erros[] = $desc;
        }
    }
    registrarLog($uid, 'CREATE', 'financeiro', 0, "Importação Itaú: $salvos lançamentos");
    jsonResponse(['success'=>true,'salvos'=>$salvos,'erros'=>$erros]);
}

jsonResponse(['error'=>'Ação inválida'],400);

// ── Helpers ───────────────────────────────────────────────────────────────

function parsearExtratoItau(string $tmpFile): array {
    $content = file_get_contents($tmpFile);
    // detecta encoding
    if (!mb_check_encoding($content, 'UTF-8')) {
        $content = mb_convert_encoding($content, 'UTF-8', 'ISO-8859-1');
    }
    // remove BOM
    $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
    $lines   = preg_split('/\r?\n/', trim($content));

    $lancamentos  = [];
    $headerFound  = false;
    $saldoAnterior = null;
    $saldoFinal    = null;
    $periodo       = '';
    $conta         = '';
    $nome          = '';

    foreach ($lines as $line) {
        $line = trim($line);
        if (!$line) continue;

        $cols = array_map('trim', str_getcsv($line, ';'));

        // Metadados do cabeçalho
        if (!$headerFound) {
            if (isset($cols[0], $cols[1])) {
                if (mb_strtolower($cols[0]) === 'nome:')      $nome    = $cols[1];
                if (mb_strtolower($cols[0]) === 'conta:')     $conta   = $cols[1];
                if (mb_strtolower($cols[0]) === 'periodo:')   $periodo = $cols[1];
                if (mb_strtolower($cols[0]) === 'data' && mb_strtolower($cols[1]) === 'lançamento') {
                    $headerFound = true;
                    continue;
                }
            }
            continue;
        }

        // Linha de dados
        // Formato: Data;Lançamento;Razão Social;CPF/CNPJ;Valor (R$);Saldo (R$)
        if (count($cols) < 5) continue;

        $data    = $cols[0];
        $lancto  = $cols[1];
        $empresa = $cols[2] ?? '';
        $docto   = $cols[3] ?? '';
        $valorStr= str_replace(['.', ','], ['', '.'], $cols[4]);
        $saldoStr= isset($cols[5]) ? str_replace(['.', ','], ['', '.'], $cols[5]) : '';

        // ignora linhas de saldo
        $lancLow = mb_strtolower($lancto);
        if (
            str_contains($lancLow, 'saldo em conta') ||
            str_contains($lancLow, 'saldo anterior') ||
            str_contains($lancLow, 'saldo total')
        ) {
            if (str_contains($lancLow, 'anterior') && $saldoStr !== '') {
                $saldoAnterior = (float)$saldoStr;
            }
            if (str_contains($lancLow, 'saldo em conta') && $saldoStr !== '') {
                $saldoFinal = (float)$saldoStr;
            }
            continue;
        }

        if (!$data || !$lancto || $valorStr === '') continue;
        $valor = (float)$valorStr;

        $lancamentos[] = [
            'data'      => $data,
            'descricao' => $lancto . ($empresa ? ' — ' . $empresa : ''),
            'empresa'   => $empresa,
            'documento' => $docto,
            'valor'     => $valor,
            'tipo'      => $valor < 0 ? 'despesa' : 'receita',
            'forma'     => detectarForma($lancto),
        ];
    }

    return [
        'ok'             => true,
        'nome'           => $nome,
        'conta'          => $conta,
        'periodo'        => $periodo,
        'saldo_anterior' => $saldoAnterior,
        'saldo_final'    => $saldoFinal,
        'total'          => count($lancamentos),
        'lancamentos'    => $lancamentos,
    ];
}

function detectarForma(string $desc): string {
    $d = mb_strtolower($desc);
    if (str_contains($d, 'pix'))    return 'pix';
    if (str_contains($d, 'ted'))    return 'ted';
    if (str_contains($d, 'doc'))    return 'ted';
    if (str_contains($d, 'boleto')) return 'boleto';
    if (str_contains($d, 'cartão') || str_contains($d, 'cartao')) return 'cartao';
    return 'outro';
}
