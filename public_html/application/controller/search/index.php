<?php
/** 통합 검색(제목): 게시글/설문 항목별 최대 3건, 항목별 페이징 */
$q = trim((string) input('q', ''));
$posts = $surveys = null;
if ($q !== '') {
    $lk = like($q);
    $posts = paginate("select b.id, b.title, b.menu_id, b.created_at, m.name menu_name, u.login_id writer_name from boards b
        join menus m on m.id=b.menu_id join users u on u.id=b.writer where b.deleted=0 and m.deleted=0 and m.use_yn=1 and b.title like ? escape '\\' order by b.id desc", [$lk], (int) input('pp', 1), 3);
    foreach ($posts['rows'] as &$r) $r['allowed'] = can_access((int) $r['menu_id']);
    unset($r);
    $surveys = paginate("select * from surveys where use_yn=1 and title like ? escape '\\' order by id desc", [$lk], (int) input('sp', 1), 3);
}
render('search/index', ['title' => '통합 검색', 'q' => $q, 'posts' => $posts, 'surveys' => $surveys]);
