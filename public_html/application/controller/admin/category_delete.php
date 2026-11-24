<?php include_once_path('application/core/admin');
$id = (int) input('id');
q_exec('update questions set category_id=null where category_id=?', [$id]);
q_exec('delete from question_categories where id=?', [$id]);
back_with('ok', '분류가 삭제되었습니다.');
