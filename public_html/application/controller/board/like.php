<?php
include_once_path('application/model/board_model');
$m = menu_guard();
require_login();
$b = post_find($m, (int) input('id'));
if (q_val('select 1 from board_likes where board_id=? and user_id=?', [$b['id'], user()['id']])) { q_exec('delete from board_likes where board_id=? and user_id=?', [$b['id'], user()['id']]); $liked = false; }
else { q_insert('board_likes', ['board_id' => $b['id'], 'user_id' => user()['id'], 'created_at' => now_str()]); $liked = true; }
json_out(['ok' => true, 'liked' => $liked, 'likes' => (int) q_val('select count(*) from board_likes where board_id=?', [$b['id']])]);
