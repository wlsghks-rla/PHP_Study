<?php
/** 경로 기준 include (ROOT 기준 상대경로, 확장자 .php 생략) */
function include_once_path(string $rel)
{
    $file = ROOT . '/' . ltrim($rel, '/') . '.php';
    if (!is_file($file)) { http_response_code(500); exit("include 대상 없음: $rel"); }
    return include_once $file;
}

function e($v): string { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); }
function now_str(): string { return date('Y-m-d H:i:s'); }
function ajax(): bool { return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest'; }
function input(string $k, $def = null) { return $_POST[$k] ?? $_GET[$k] ?? $def; }
function redirect(string $url): void { header('Location: ' . $url); exit; }
function json_out($data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
/** 오류 응답: AJAX면 JSON, 아니면 에러 화면 */
function fail(int $code, string $msg): void
{
    if (ajax() || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'json')) json_out(['ok' => false, 'msg' => $msg, 'errors' => [$msg]], $code);
    http_response_code($code);
    if (function_exists('render') && defined('DB_FILE')) { render('layout/error', ['title' => '오류', 'code' => $code, 'msg' => $msg]); exit; }
    exit($msg);
}
function flash(string $k, ?string $v = null)
{
    if ($v !== null) { $_SESSION['flash'][$k] = $v; return null; }
    $x = $_SESSION['flash'][$k] ?? null; unset($_SESSION['flash'][$k]); return $x;
}
function csrf(): string { return $_SESSION['csrf'] ?? ''; }

/** 에디터(HTML) 본문 정화 */
function html_clean(?string $html): string
{
    $html = strip_tags((string) $html, '<p><br><strong><b><em><i><u><s><ul><ol><li><blockquote><pre><code><h1><h2><h3><h4><a><img><span><div><table><thead><tbody><tr><td><th>');
    $html = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html);
    return preg_replace('/(href|src)\s*=\s*(["\'])\s*(javascript|data|vbscript):[^"\']*\2/i', '$1=$2#$2', $html);
}
function html_text(?string $html): string { return trim(html_entity_decode(strip_tags((string) $html))); }
function mark(string $text, ?string $kw): string
{
    $x = e($text);
    return ($kw === null || $kw === '') ? $x : preg_replace('/(' . preg_quote(e($kw), '/') . ')/iu', '<mark>$1</mark>', $x);
}
function uuid(): string
{
    $d = random_bytes(16); $d[6] = chr(ord($d[6]) & 0x0f | 0x40); $d[8] = chr(ord($d[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($d), 4));
}
/** 안전한 LIKE 패턴 */
function like(string $kw): string { return '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $kw) . '%'; }
