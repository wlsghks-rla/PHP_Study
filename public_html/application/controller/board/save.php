<?php
/** 글 등록(id 없음) / 수정(id 있음) */
include_once_path('application/model/board_model');
$m = menu_guard();
require_login();
$id = (int) input('id', 0);
$b = $id ? post_find($m, $id) : null;
if ($b && !post_can_edit($b)) fail(403, '수정 권한이 없습니다.');
[$errs, $title, $tags] = post_validate($m, $b);
$isNotice = !empty($_POST['is_notice']) && is_super() && $m['type'] === 'BOARD' && !input('parent_id');
$nd = post_notice_dates($isNotice);
if (is_string($nd)) $errs[] = $nd;
if ($errs) json_out(['ok' => false, 'errors' => $errs], 422);
$n = now_str();
db()->beginTransaction();
try {
    if ($b) {
        q_update('boards', ['title' => $title, 'content' => html_clean(input('content')), 'is_notice' => (int) $isNotice, 'notice_start' => $nd[0], 'notice_end' => $nd[1], 'updated_at' => $n], 'id=?', [$id]);
        q_exec('delete from hashtags where board_id=?', [$id]);
        post_save_tags($id, $tags);
        // 삭제 표시한 첨부는 '저장' 시점에만 반영
        foreach (array_map('intval', (array) ($_POST['delete_ids'] ?? [])) as $d) q_exec('update attachments set deleted=1 where id=? and board_id=?', [$d, $id]);
        post_save_files($m, $id, (int) input('thumb_index', -1), input('thumb_id'));
        $msg = '수정되었습니다.';
    } else {
        $parent = (input('parent_id') && $m['type'] === 'BOARD') ? post_find($m, (int) input('parent_id')) : null;
        $id = q_insert('boards', ['menu_id' => $m['id'], 'writer' => user()['id'], 'parent_id' => $parent['id'] ?? null, 'title' => $title, 'content' => html_clean(input('content')),
            'is_notice' => (int) $isNotice, 'notice_start' => $nd[0], 'notice_end' => $nd[1], 'depth' => $parent ? $parent['depth'] + 1 : 0, 'created_at' => $n, 'updated_at' => $n]);
        $pad = str_pad((string) $id, 10, '0', STR_PAD_LEFT);
        q_update('boards', ['group_id' => $parent ? $parent['group_id'] : $id, 'path' => $parent ? $parent['path'] . '/' . $pad : $pad], 'id=?', [$id]);
        post_save_tags($id, $tags);
        post_save_files($m, $id, (int) input('thumb_index', 0), null);
        $msg = '등록되었습니다.';
    }
    db()->commit();
} catch (Throwable $e) { db()->rollBack(); fail(500, '저장 중 오류가 발생했습니다.'); }
json_out(['ok' => true, 'id' => $id, 'msg' => $msg]);
