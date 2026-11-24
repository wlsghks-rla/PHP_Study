<?php include_once_path('application/core/admin');
$id = (int) input('id');
if ($id === user()['id']) back_with('error', '본인 계정은 삭제할 수 없습니다.');
q_exec('delete from users where id=?', [$id]);
back_with('ok', '회원이 삭제되었습니다.');
