<?php
include_once_path('application/model/board_model');
$menu = menu_guard(null, true);
if (!in_array($menu['type'], ['BOARD', 'GALLERY'])) fail(404, '게시판이 아닙니다.');
$sizes = page_sizes();
$size = (int) input('size', $sizes[0]); if (!in_array($size, $sizes)) $size = $sizes[0];
$field = in_array(input('field'), ['title', 'content', 'writer']) ? input('field') : 'all';
$kw = trim((string) input('kw', '')); $tag = trim((string) input('tag', ''));
$sort = input('sort') === 'likes' ? 'likes' : 'latest';
$mine = !empty($_GET['mine']) && user();
$gallery = $menu['type'] === 'GALLERY';

$sel = "select b.*, u.login_id writer_name,
  (select count(*) from board_likes l where l.board_id=b.id) likes,
  (select count(*) from comments c where c.board_id=b.id and c.deleted=0) comment_cnt,
  (select count(*) from attachments a where a.board_id=b.id and a.deleted=0 and a.is_thumbnail=0) file_cnt"
  . ($gallery ? ",(select stored_name from attachments a where a.board_id=b.id and a.deleted=0 and a.is_thumbnail=1 limit 1) thumb,
  (select count(*) from attachments a where a.board_id=b.id and a.deleted=0) img_cnt" : '')
  . " from boards b join users u on u.id=b.writer where b.menu_id=? and b.deleted=0";
$w = ''; $p = [$menu['id']];
if ($kw !== '') {
    $or = []; $lk = like($kw);
    if (in_array($field, ['all', 'title'])) { $or[] = "b.title like ? escape '\\'"; $p[] = $lk; }
    if (in_array($field, ['all', 'content'])) { $or[] = "b.content like ? escape '\\'"; $p[] = $lk; }
    if (in_array($field, ['all', 'writer'])) { $or[] = "u.login_id like ? escape '\\'"; $p[] = $lk; }
    $w .= ' and (' . implode(' or ', $or) . ')';
}
if ($tag !== '') { $w .= ' and exists (select 1 from hashtags h where h.board_id=b.id and h.title=?)'; $p[] = $tag; }
if ($mine) { $w .= ' and b.writer=?'; $p[] = user()['id']; }

// 공지: 기간 내 공지는 최상단에 별도 노출 (검색/태그/내글 보기 때는 일반 목록에 포함)
$notices = [];
$now = now_str();
if (!$gallery && $kw === '' && $tag === '' && !$mine) {
    $notices = q_all("$sel and b.is_notice=1 and b.notice_start<=? and b.notice_end>=? order by b.id desc", [$menu['id'], $now, $now]);
    $w .= ' and (b.is_notice=0 or b.notice_end<? or b.notice_start>?)'; array_push($p, $now, $now);
}
$order = $sort === 'likes' ? 'likes desc, b.id desc' : (!$gallery ? 'b.group_id desc, b.path asc' : 'b.id desc');
$posts = paginate("$sel$w order by $order", $p, (int) input('page', 1), $size);
$ids = array_merge(array_column($posts['rows'], 'id'), array_column($notices, 'id'));
$tags = [];
if ($ids) foreach (q_all('select * from hashtags where board_id in (' . implode(',', array_map('intval', $ids)) . ')') as $t) $tags[$t['board_id']][] = $t['title'];

$head = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.snow.css">';
$scripts = '<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.min.js"></script><script src="/js/board.js"></script>';
render('board/list', compact('menu', 'posts', 'notices', 'tags', 'sizes', 'size', 'field', 'kw', 'tag', 'sort', 'mine', 'gallery', 'head', 'scripts') + ['title' => $menu['name'], 'curMenu' => $menu]);
