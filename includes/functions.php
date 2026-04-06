<?php
// ============================================================
// FUNCTIONS.PHP — Funções auxiliares globais do TopoGest
// ============================================================

// -----------------------------------------------------------
// Busca por ID em array de dados
// -----------------------------------------------------------
function findById(array $arr, int $id): ?array {
    foreach ($arr as $item) {
        if ((int)$item['id'] === $id) return $item;
    }
    return null;
}

function findAllBy(array $arr, string $key, $value): array {
    return array_values(array_filter($arr, fn($item) => $item[$key] == $value));
}

// -----------------------------------------------------------
// Formatação de dados
// -----------------------------------------------------------
function formatMoney(float $value): string {
    return 'R$ ' . number_format($value, 2, ',', '.');
}

function formatDate(?string $date, string $format = 'd/m/Y'): string {
    if (!$date) return '—';
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if (!$d) {
        $d = DateTime::createFromFormat('Y-m-d H:i', $date);
    }
    return $d ? $d->format($format) : $date;
}

function formatDateTime(?string $date): string {
    if (!$date) return '—';
    $d = DateTime::createFromFormat('Y-m-d H:i', $date);
    return $d ? $d->format('d/m/Y \à\s H:i') : $date;
}

function timeAgo(string $datetime): string {
    $now  = new DateTime();
    $past = new DateTime($datetime);
    $diff = $now->diff($past);
    if ($diff->y > 0) return "há {$diff->y} ano" . ($diff->y > 1 ? 's' : '');
    if ($diff->m > 0) return "há {$diff->m} mês" . ($diff->m > 1 ? 'es' : '');
    if ($diff->d > 0) return "há {$diff->d} dia" . ($diff->d > 1 ? 's' : '');
    if ($diff->h > 0) return "há {$diff->h} hora" . ($diff->h > 1 ? 's' : '');
    return "há {$diff->i} min";
}

// -----------------------------------------------------------
// Status badges — OS
// -----------------------------------------------------------
function osStatusLabel(string $status): string {
    $map = [
        'aberta'       => 'Aberta',
        'em execucao'  => 'Em Execução',
        'em revisao'   => 'Em Revisão',
        'finalizada'   => 'Finalizada',
        'faturada'     => 'Faturada',
    ];
    return $map[$status] ?? ucfirst($status);
}

function osStatusClass(string $status): string {
    $map = [
        'aberta'       => 'badge-open',
        'em execucao'  => 'badge-exec',
        'em revisao'   => 'badge-review',
        'finalizada'   => 'badge-done',
        'faturada'     => 'badge-billed',
    ];
    return $map[$status] ?? 'badge-open';
}

function osStatusIcon(string $status): string {
    $map = [
        'aberta'       => '○',
        'em execucao'  => '▶',
        'em revisao'   => '⟳',
        'finalizada'   => '✓',
        'faturada'     => '◈',
    ];
    return $map[$status] ?? '○';
}

// -----------------------------------------------------------
// Status badges — Proposta
// -----------------------------------------------------------
function propostaStatusLabel(string $status): string {
    $map = [
        'pendente'  => 'Pendente',
        'aprovada'  => 'Aprovada',
        'recusada'  => 'Recusada',
    ];
    return $map[$status] ?? ucfirst($status);
}

function propostaStatusClass(string $status): string {
    $map = [
        'pendente' => 'badge-open',
        'aprovada' => 'badge-done',
        'recusada' => 'badge-danger',
    ];
    return $map[$status] ?? 'badge-open';
}

// -----------------------------------------------------------
// Tipo de custo
// -----------------------------------------------------------
function custoTipoLabel(string $tipo): string {
    $map = [
        'combustivel' => 'Combustível',
        'alimentacao' => 'Alimentação',
        'equipamento' => 'Equipamento',
        'outros'      => 'Outros',
    ];
    return $map[$tipo] ?? ucfirst($tipo);
}

function custoTipoIcon(string $tipo): string {
    $map = [
        'combustivel' => '⛽',
        'alimentacao' => '🍽',
        'equipamento' => '📡',
        'outros'      => '📎',
    ];
    return $map[$tipo] ?? '📎';
}

// -----------------------------------------------------------
// Tipo de arquivo
// -----------------------------------------------------------
function anexoTipoIcon(string $tipo): string {
    $map = [
        'pdf'    => '📄',
        'imagem' => '🖼',
        'dwg'    => '📐',
    ];
    return $map[$tipo] ?? '📎';
}

function anexoTipoClass(string $tipo): string {
    $map = [
        'pdf'    => 'tag-pdf',
        'imagem' => 'tag-img',
        'dwg'    => 'tag-dwg',
    ];
    return $map[$tipo] ?? '';
}

// -----------------------------------------------------------
// Histórico — ícone por tipo de ação
// -----------------------------------------------------------
function historicoIcon(string $tipo): string {
    $map = [
        'criacao'    => 'hist-create',
        'status'     => 'hist-status',
        'equipe'     => 'hist-team',
        'custo'      => 'hist-cost',
        'arquivo'    => 'hist-file',
        'financeiro' => 'hist-finance',
    ];
    return $map[$tipo] ?? 'hist-create';
}

// -----------------------------------------------------------
// Cálculos de OS
// -----------------------------------------------------------
function calcCustoEquipe(array $equipe_os, int $os_id): float {
    $total = 0;
    foreach ($equipe_os as $m) {
        if ((int)$m['os_id'] === $os_id) {
            $total += (float)$m['horas'] * (float)$m['custo_hora'];
        }
    }
    return $total;
}

function calcCustosDiretos(array $custos_os, int $os_id): float {
    $total = 0;
    foreach ($custos_os as $c) {
        if ((int)$c['os_id'] === $os_id) {
            $total += (float)$c['valor'];
        }
    }
    return $total;
}

function calcTotalReceitas(array $financeiro, int $os_id): float {
    $total = 0;
    foreach ($financeiro as $f) {
        if ((int)$f['os_id'] === $os_id && $f['tipo'] === 'receita') {
            $total += (float)$f['valor'];
        }
    }
    return $total;
}

function calcTotalDespesas(array $financeiro, int $os_id): float {
    $total = 0;
    foreach ($financeiro as $f) {
        if ((int)$f['os_id'] === $os_id && $f['tipo'] === 'despesa') {
            $total += (float)$f['valor'];
        }
    }
    return $total;
}

// -----------------------------------------------------------
// Contadores de dados relacionados à OS
// -----------------------------------------------------------
function countByOsId(array $arr, int $os_id): int {
    return count(findAllBy($arr, 'os_id', $os_id));
}

// -----------------------------------------------------------
// Gera badge HTML
// -----------------------------------------------------------
function badge(string $label, string $class): string {
    return "<span class=\"badge {$class}\">{$label}</span>";
}

// -----------------------------------------------------------
// URL helper
// -----------------------------------------------------------
function url(string $path, array $params = []): string {
    $query = $params ? '?' . http_build_query($params) : '';
    return $path . $query;
}
