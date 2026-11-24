<?php
function db(): PDO
{
    static $pdo;
    if (!$pdo) {
        $pdo = new PDO('sqlite:' . DB_FILE);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
    return $pdo;
}
function q_all(string $sql, array $p = []): array { $s = db()->prepare($sql); $s->execute($p); return $s->fetchAll(); }
function q_one(string $sql, array $p = []): ?array { $s = db()->prepare($sql); $s->execute($p); return $s->fetch() ?: null; }
function q_val(string $sql, array $p = []) { $s = db()->prepare($sql); $s->execute($p); $r = $s->fetch(PDO::FETCH_NUM); return $r ? $r[0] : null; }
function q_exec(string $sql, array $p = []): int { $s = db()->prepare($sql); $s->execute($p); return $s->rowCount(); }
function q_insert(string $table, array $row): int
{
    $cols = implode(',', array_keys($row));
    q_exec("insert into $table ($cols) values (" . implode(',', array_fill(0, count($row), '?')) . ')', array_values($row));
    return (int) db()->lastInsertId();
}
function q_update(string $table, array $row, string $where, array $wp = []): int
{
    $set = implode(',', array_map(fn ($c) => "$c=?", array_keys($row)));
    return q_exec("update $table set $set where $where", array_merge(array_values($row), $wp));
}
/** 페이징: ['rows','total','pages','page','size'] */
function paginate(string $sql, array $p, int $page, int $size): array
{
    $total = (int) q_val("select count(*) from ($sql)", $p);
    $pages = max(1, (int) ceil($total / $size));
    $page = max(1, min($page, $pages));
    $rows = q_all("$sql limit $size offset " . (($page - 1) * $size), $p);
    return compact('rows', 'total', 'pages', 'page', 'size');
}
/** 공통코드 */
function code_list(string $kind): array
{
    $o = [];
    foreach (q_all('select code_key, code_name from common_codes where code_kind=? order by code_key+0', [$kind]) as $r) $o[$r['code_key']] = $r['code_name'];
    return $o;
}
function page_sizes(): array { return array_map('intval', array_keys(code_list('PAGE_SIZE'))) ?: [5, 10, 50]; }
function code_int(string $kind, int $def): int { $k = array_key_first(code_list($kind)); return $k === null ? $def : (int) $k; }
