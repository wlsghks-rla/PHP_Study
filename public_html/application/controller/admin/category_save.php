<?php include_once_path('application/core/admin');
$id = (int) input('id', 0); $nm = trim((string) input('name'));
if ($nm === '' || mb_strlen($nm) > 50) back_with('error', '분류명을 1~50자로 입력하세요.');
if ($id) q_update('question_categories', ['name' => $nm], 'id=?', [$id]); else q_insert('question_categories', ['name' => $nm, 'use_yn' => 1]);
back_with('ok', '분류가 저장되었습니다.');
