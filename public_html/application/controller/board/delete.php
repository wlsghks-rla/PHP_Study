<?php
$m = menu_guard();
require_login();
$ids = array_map('intval', (array) ($_POST['ids'] ?? []));
if (!$ids) json_out(['ok' => false, 'errors' => ['삭제할 게시글을 선택하세요.']], 422);
$in = implode(',', $ids);
$n = is_super()
    ? q_exec("update boards set deleted=1 where menu_id=? and deleted=0 and id in ($in)", [$m['id']])
    : q_exec("update boards set deleted=1 where menu_id=? and deleted=0 and writer=? and id in ($in)", [$m['id'], user()['id']]);
json_out(['ok' => true, 'msg' => "{$n}건 삭제되었습니다."]);
