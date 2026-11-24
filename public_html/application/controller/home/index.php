<?php
$popup = [];
// 메인 진입 시(세션당 1회) 공지사항을 레이어 팝업으로 노출
if (empty($_SESSION['notice_shown'])) {
    $_SESSION['notice_shown'] = true;
    $n = now_str();
    $rows = q_all("select b.id, b.title, b.menu_id, b.notice_end from boards b join menus m on m.id=b.menu_id
        where b.deleted=0 and b.is_notice=1 and b.notice_start<=? and b.notice_end>=? and m.deleted=0 order by b.id desc", [$n, $n]);
    $popup = array_values(array_filter($rows, fn ($b) => can_access((int) $b['menu_id'])));
}
render('home/index', ['popup' => $popup]);
