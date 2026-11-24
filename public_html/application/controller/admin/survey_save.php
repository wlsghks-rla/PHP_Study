<?php include_once_path('application/core/admin');
$id = (int) input('id', 0); $title = trim((string) input('title')); $st = strtotime((string) input('start_dt')); $en = strtotime((string) input('end_dt')); $per = (int) input('per_page', 5);
$errs = [];
if ($title === '') $errs[] = '설문조사명을 입력하세요.'; elseif (mb_strlen($title) > 200) $errs[] = '설문조사명은 200자 이하로 입력하세요.';
if (!$st || !$en) $errs[] = '참여기간을 입력하세요.'; elseif ($en <= $st) $errs[] = '종료일시는 시작일시 이후여야 합니다.';
if ($per < 1 || $per > 50) $errs[] = '한 페이지 문항 수는 1~50 사이로 입력하세요.';
if ($errs) back_with('errors', implode("\n", $errs));
$row = ['title' => $title, 'description' => mb_substr((string) input('description'), 0, 4000), 'start_dt' => date('Y-m-d H:i:s', $st), 'end_dt' => date('Y-m-d H:i:s', $en), 'per_page' => $per,
    'login_required' => (int) !empty($_POST['login_required']), 'allow_duplicate' => (int) !empty($_POST['allow_duplicate']), 'use_yn' => $id ? (int) !empty($_POST['use_yn']) : 1, 'updated_at' => now_str()];
if ($id) q_update('surveys', $row, 'id=?', [$id]); else q_insert('surveys', $row + ['created_at' => now_str()]);
back_with('ok', '설문조사가 저장되었습니다.', '/admin/surveys');
