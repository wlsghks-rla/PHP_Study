<?php
/** 댓글/대댓글 등록(cid 없음) 및 수정(cid 있음) */
include_once_path('application/model/board_model');
$m = menu_guard();
require_login();
$id = (int) input('id');
$text = trim((string) input('content', ''));
if ($text === '') json_out(['ok' => false, 'errors' => ['댓글 내용을 입력하세요.']], 422);
if (mb_strlen($text) > 500) json_out(['ok' => false, 'errors' => ['댓글은 500자 이하로 입력하세요.']], 422);
if (input('cid')) {
    $c = comment_own($m, $id, (int) input('cid'));
    q_update('comments', ['content' => $text, 'updated_at' => now_str()], 'id=?', [$c['id']]);
    json_out(['ok' => true, 'msg' => '댓글이 수정되었습니다.']);
}
$b = post_find($m, $id);
$pid = input('parent_id') ? (int) input('parent_id') : null;
if ($pid && !q_val('select 1 from comments where id=? and board_id=?', [$pid, $b['id']])) fail(404, '부모 댓글을 찾을 수 없습니다.');
q_insert('comments', ['board_id' => $b['id'], 'parent_id' => $pid, 'writer' => user()['id'], 'content' => $text, 'created_at' => now_str(), 'updated_at' => now_str()]);
json_out(['ok' => true, 'msg' => '댓글이 등록되었습니다.']);
