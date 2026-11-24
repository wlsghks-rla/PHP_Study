<?php
include_once_path('application/model/board_model');
$m = menu_guard();
$id = (int) input('id');
$b = post_find($m, $id);
post_count_view($b);
$b['view_count'] = q_val('select view_count from boards where id=?', [$id]);
$files = q_all('select a.*, (select count(*) from download_logs d where d.attachment_id=a.id) downloads from attachments a where a.board_id=? and a.deleted=0', [$id]);
$tags = array_column(q_all('select title from hashtags where board_id=?', [$id]), 'title');
$likes = (int) q_val('select count(*) from board_likes where board_id=?', [$id]);
$liked = user() && q_val('select 1 from board_likes where board_id=? and user_id=?', [$id, user()['id']]);
$comments = comment_rows($id);
$canEdit = post_can_edit($b);
partial('board/detail', compact('m', 'b', 'files', 'tags', 'likes', 'liked', 'comments', 'canEdit'));
