<?php
/** CLI 전용: php application/config/install.php  → DB 생성 + 샘플 데이터 */
if (PHP_SAPI !== 'cli') exit('CLI only');
define('ROOT', dirname(__DIR__, 2));
require ROOT . '/application/core/common.php';
include_once_path('application/config/config');
include_once_path('application/core/db');
foreach ([dirname(DB_FILE), UPLOAD_DIR, EDITOR_IMAGE_DIR] as $d) if (!is_dir($d)) mkdir($d, 0775, true);
@unlink(DB_FILE);
// 초기 계정 비밀번호는 코드에 두지 않고 실행 시 무작위 생성 (또는 인자로 지정: php install.php 관리자PW 일반회원PW)
$adminPw = $argv[1] ?? bin2hex(random_bytes(6));
$userPw = $argv[2] ?? bin2hex(random_bytes(6));
db()->exec(file_get_contents(ROOT . '/application/config/schema.sql'));
$n = now_str();
q_insert('users', ['login_id' => 'admin', 'password' => password_hash($adminPw, PASSWORD_DEFAULT), 'role' => 'SUPER', 'created_at' => $n, 'updated_at' => $n]);
q_insert('users', ['login_id' => 'user1', 'password' => password_hash($userPw, PASSWORD_DEFAULT), 'role' => 'USER', 'created_at' => $n, 'updated_at' => $n]);
foreach ([5, 10, 50] as $s) q_insert('common_codes', ['code_kind' => 'PAGE_SIZE', 'code_key' => (string) $s, 'code_name' => "{$s}개씩 보기", 'is_admin' => 1]);
q_insert('common_codes', ['code_kind' => 'TOP_N', 'code_key' => '5', 'code_name' => '조회수 TOP N', 'is_admin' => 1]);
$all = ['GUEST', 'USER', 'SUPER'];
$menus = [
    ['게시판', 'BOARD', null, 0, $all], ['갤러리', 'GALLERY', null, 0, $all], ['회원 전용 게시판', 'BOARD', null, 0, ['USER', 'SUPER']],
    ['설문조사', 'LINK', '/survey/list', 0, $all], ['회사소개', 'LINK', '/intro/index', 0, $all], ['네이버', 'LINK', 'https://naver.com', 1, $all],
];
foreach ($menus as $i => [$name, $type, $url, $nw, $lv]) {
    $id = q_insert('menus', ['name' => $name, 'type' => $type, 'url' => $url, 'new_window' => $nw, 'sort' => $i + 1, 'created_at' => $n, 'updated_at' => $n]);
    foreach ($lv as $l) q_insert('menu_permissions', ['menu_id' => $id, 'access_level' => $l]);
}
foreach (['소프트웨어', '서비스', '만족도'] as $c) q_insert('question_categories', ['name' => $c]);
$sid = q_insert('surveys', ['title' => '서비스 이용 만족도 조사', 'description' => "서비스 개선을 위한 설문조사입니다.\n문항은 5개씩 나누어 표시되며, 필수 문항에 응답해야 다음으로 이동할 수 있습니다.",
    'start_dt' => date('Y-m-d 00:00:00', strtotime('-1 day')), 'end_dt' => date('Y-m-d 23:59:59', strtotime('+3 months')), 'login_required' => 1, 'allow_duplicate' => 0, 'per_page' => 5, 'created_at' => $n, 'updated_at' => $n]);
$qs = [
    ['이용하시는 서비스를 선택해 주십시오.(2개 선택)', 'CHOICE', 2, 1, 1, ['수업설계자료', '연구자료', '디지털교과서', '교원연수', '[없음]없음']],
    ['서비스 이용 빈도는 어느 정도입니까?', 'CHOICE', 1, 1, 2, ['매일', '주 2~3회', '월 1~2회', '거의 안 함']],
    ['선호하는 교육정책을 선택해 주십시오.(2개 선택)', 'CHOICE', 2, 1, 3, ['학생평가', '자유학기제', '고교학점제', '[없음]없음']],
    ['기타 이용 서비스를 선택해 주십시오.(2개 선택)', 'CHOICE', 2, 0, 3, ['공지사항', '마이페이지', '고객센터', '[기타]기타', '[없음]없음']],
    ['서비스에 대한 전반적인 만족도는?', 'CHOICE', 1, 1, 2, ['매우 만족', '만족', '보통', '불만족']],
    ['개선되었으면 하는 점을 자유롭게 적어 주세요.', 'TEXT', 1, 0, 3, []],
    ['추천 의향이 있으십니까?', 'CHOICE', 1, 1, 2, ['예', '아니오']],
];
foreach ($qs as $i => [$t, $type, $max, $req, $cat, $opts]) {
    $qid = q_insert('questions', ['survey_id' => $sid, 'category_id' => $cat, 'type' => $type, 'text' => $t, 'required' => $req, 'sort' => $i + 1, 'max_count' => $max]);
    foreach ($opts as $j => $o) q_insert('question_options', ['question_id' => $qid, 'text' => preg_replace('/^\[(기타|없음)\]/u', '', $o), 'sort' => $j + 1,
        'is_etc' => (int) str_starts_with($o, '[기타]'), 'is_none' => (int) str_starts_with($o, '[없음]')]);
}
$b = q_insert('boards', ['menu_id' => 1, 'writer' => 1, 'title' => '게시판 오픈 안내', 'content' => '<p>게시판이 오픈되었습니다. 많은 이용 바랍니다.</p>', 'is_notice' => 1,
    'notice_start' => date('Y-m-d H:i:s', strtotime('-1 day')), 'notice_end' => date('Y-m-d H:i:s', strtotime('+1 month')), 'created_at' => $n, 'updated_at' => $n]);
q_update('boards', ['group_id' => $b, 'path' => str_pad((string) $b, 10, '0', STR_PAD_LEFT)], 'id=?', [$b]);
q_insert('hashtags', ['board_id' => $b, 'title' => '공지']);
echo "설치 완료: " . DB_FILE . "\n";
echo "초기 계정(한 번만 표시됩니다. 로그인 후 변경하세요)\n  admin (슈퍼관리자) / $adminPw\n  user1 (일반회원)   / $userPw\n";
