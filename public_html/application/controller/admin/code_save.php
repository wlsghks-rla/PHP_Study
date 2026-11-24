<?php include_once_path('application/core/admin');
$id = (int) input('id', 0); $k = trim((string) input('code_kind')); $key = trim((string) input('code_key')); $nm = trim((string) input('code_name'));
if ($k === '' || $key === '' || $nm === '' || mb_strlen($k) > 50 || mb_strlen($key) > 50 || mb_strlen($nm) > 100) back_with('error', '분류/코드값/코드명을 올바르게 입력하세요.');
if (q_val('select 1 from common_codes where code_kind=? and code_key=? and id<>?', [$k, $key, $id])) back_with('error', '이미 존재하는 코드입니다.');
$row = ['code_kind' => $k, 'code_key' => $key, 'code_name' => $nm];
if ($id) q_update('common_codes', $row, 'id=?', [$id]); else q_insert('common_codes', $row + ['is_admin' => 0]);
back_with('ok', '저장되었습니다.');
