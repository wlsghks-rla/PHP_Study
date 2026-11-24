<?php
include_once_path('application/model/board_model');
$m = menu_guard();
$c = comment_own($m, (int) input('id'), (int) input('cid'));
// 하위 댓글은 유지하고 "삭제된 댓글입니다"로 표시
q_exec('update comments set deleted=1 where id=?', [$c['id']]);
json_out(['ok' => true, 'msg' => '댓글이 삭제되었습니다.']);
