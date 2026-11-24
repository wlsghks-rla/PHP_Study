<?php
include_once_path('application/model/board_model');
$m = menu_guard();
require_login();
$b = null; $files = []; $tags = []; $parent = null;
if (input('id')) {
    $b = post_find($m, (int) input('id'));
    if (!post_can_edit($b)) fail(403, '수정 권한이 없습니다.');
    $files = q_all('select * from attachments where board_id=? and deleted=0', [$b['id']]);
    $tags = array_column(q_all('select title from hashtags where board_id=?', [$b['id']]), 'title');
} elseif (input('parent') && $m['type'] === 'BOARD') {
    $parent = post_find($m, (int) input('parent'));
}
partial('board/form', ['m' => $m, 'b' => $b, 'files' => $files, 'tags' => $tags, 'parent' => $parent, 'cfg' => post_limits($m)]);
