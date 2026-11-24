<?php
/**
 * 단일 진입점(Front Controller)
 *   /{폴더}/{파일}  →  application/controller/{폴더}/{파일}.php  (실행)
 *                      view/{폴더}/{파일}.php                    (화면)
 *   /               →  home/index
 */
if (PHP_SAPI === 'cli-server') {           // 내장 서버: 정적 파일은 그대로 서비스
    $f = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($f) && !preg_match('#/(application|view|storage)/#', $f)) return false;
}
define('ROOT', __DIR__);
ini_set('display_errors', '0');   // 화면에 오류 출력 금지(응답/CSV 오염 방지), 로그로만 기록
require_once ROOT . '/application/core/common.php';   // include_once_path() 등 공통 함수

include_once_path('application/config/config');
include_once_path('application/core/db');
include_once_path('application/core/auth');
include_once_path('application/core/view');

session_name('APPSESS');
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS'])]);   // JS에서 세션쿠키 접근 차단
session_start();
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));

$path = trim((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$seg = $path === '' ? [] : explode('/', $path);
$folder = $seg[0] ?? 'home';
$file = $seg[1] ?? 'index';
if (!preg_match('/^[a-z_]+$/', $folder) || !preg_match('/^[a-z_]+$/', $file) || count($seg) > 2) fail(404, '페이지를 찾을 수 없습니다.');

// POST 요청은 CSRF 토큰 필수
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    $t = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['_token'] ?? '');
    if (!hash_equals($_SESSION['csrf'], (string) $t)) fail(419, '세션이 만료되었습니다. 새로고침 후 다시 시도하세요.');
}
$ctl = ROOT . "/application/controller/$folder/$file.php";
if (!is_file($ctl)) fail(404, '페이지를 찾을 수 없습니다.');
$ROUTE = "$folder/$file";
require $ctl;
