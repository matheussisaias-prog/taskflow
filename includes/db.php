<?php
// includes/db.php — Conexão MySQL PDO + helpers
require_once __DIR__ . '/../config.php';

// ── Conexão singleton ────────────────────────────────────
function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    try {
        $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        die(json_encode(['error' => 'Banco de dados indisponível: ' . $e->getMessage()]));
    }
    return $pdo;
}

// ── Helpers CRUD ─────────────────────────────────────────

function db_find(string $table, int $id): ?array {
    $st = db()->prepare("SELECT * FROM `$table` WHERE id = ?");
    $st->execute([$id]);
    $r = $st->fetch();
    return $r ?: null;
}

function db_all(string $table, string $order = 'id DESC'): array {
    return db()->query("SELECT * FROM `$table` ORDER BY $order")->fetchAll();
}

function db_where(string $table, array $conds = [], string $order = 'id DESC'): array {
    if (empty($conds)) return db_all($table, $order);
    $where = implode(' AND ', array_map(fn($k) => "`$k` = ?", array_keys($conds)));
    $st = db()->prepare("SELECT * FROM `$table` WHERE $where ORDER BY $order");
    $st->execute(array_values($conds));
    return $st->fetchAll();
}

function db_where_raw(string $table, string $sql, array $params = [], string $order = 'id DESC'): array {
    $st = db()->prepare("SELECT * FROM `$table` WHERE $sql ORDER BY $order");
    $st->execute($params);
    return $st->fetchAll();
}

function db_insert(string $table, array $data): int {
    $cols = implode(',', array_map(fn($k) => "`$k`", array_keys($data)));
    $phs  = implode(',', array_fill(0, count($data), '?'));
    $st = db()->prepare("INSERT INTO `$table` ($cols) VALUES ($phs)");
    $st->execute(array_values($data));
    return (int)db()->lastInsertId();
}

function db_update(string $table, int $id, array $data): bool {
    if (empty($data)) return false;
    $set = implode(',', array_map(fn($k) => "`$k` = ?", array_keys($data)));
    $st = db()->prepare("UPDATE `$table` SET $set WHERE id = ?");
    return $st->execute([...array_values($data), $id]);
}

function db_delete(string $table, int $id): bool {
    $st = db()->prepare("DELETE FROM `$table` WHERE id = ?");
    return $st->execute([$id]);
}

function db_count(string $table, array $conds = []): int {
    if (empty($conds)) {
        return (int)db()->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
    }
    $where = implode(' AND ', array_map(fn($k) => "`$k` = ?", array_keys($conds)));
    $st = db()->prepare("SELECT COUNT(*) FROM `$table` WHERE $where");
    $st->execute(array_values($conds));
    return (int)$st->fetchColumn();
}

function db_sum(string $table, string $col, array $conds = []): float {
    if (empty($conds)) {
        return (float)db()->query("SELECT COALESCE(SUM(`$col`),0) FROM `$table`")->fetchColumn();
    }
    $where = implode(' AND ', array_map(fn($k) => "`$k` = ?", array_keys($conds)));
    $st = db()->prepare("SELECT COALESCE(SUM(`$col`),0) FROM `$table` WHERE $where");
    $st->execute(array_values($conds));
    return (float)$st->fetchColumn();
}

// ── Utilitários ──────────────────────────────────────────

function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function hoje(): string { return date('Y-m-d'); }
function agora(): string { return date('Y-m-d H:i:s'); }

function moeda(float $v): string {
    return 'R$ ' . number_format($v, 2, ',', '.');
}

function registrarLog(int $uid, string $acao, string $tabela, int $reg_id, string $desc): void {
    try {
        db_insert('logs', [
            'usuario_id'  => $uid,
            'acao'        => $acao,
            'tabela'      => $tabela,
            'registro_id' => $reg_id,
            'descricao'   => $desc,
            'ip'          => $_SERVER['REMOTE_ADDR'] ?? '',
        ]);
    } catch (Throwable $e) { /* silencioso */ }
}

function proximoNumero(string $prefix, string $table): string {
    $ano = date('Y');
    $st = db()->prepare("SELECT numero FROM `$table` WHERE numero LIKE ? ORDER BY id DESC LIMIT 1");
    $st->execute(["$prefix-$ano-%"]);
    $last = $st->fetchColumn();
    $seq  = $last ? ((int)substr($last, strrpos($last,'-')+1) + 1) : 1;
    return "$prefix-$ano-" . str_pad($seq, 4, '0', STR_PAD_LEFT);
}
