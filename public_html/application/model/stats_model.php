<?php
/** 메뉴 접근 통계: 데이터가 없는 일/월/년/시간도 0으로 채워 반환 */
function menu_stats(string $type, int $year, int $month, bool $dedupe): array
{
    $menus = q_all('select id, name, deleted from menus order by sort, id');   // 삭제된 메뉴 포함
    $label = fn ($m) => $m['name'] . ($m['deleted'] ? ' (삭제됨)' : '');
    $thisYear = (int) date('Y'); $rows = [];
    if ($type === 'year') {
        $years = [$thisYear - 2, $thisYear - 1, $thisYear];
        // 연도별은 pivot(조건부 집계)으로 한 번의 쿼리 처리
        $sel = 'menu_id'; $bind = [];
        foreach ($years as $y) { $sel .= ", sum(case when substr(access_time,1,4)=? then 1 else 0 end) y$y"; $bind[] = (string) $y; }
        $agg = []; foreach (q_all("select $sel from menu_logs group by menu_id", $bind) as $r) $agg[$r['menu_id']] = $r;
        foreach ($menus as $m) { $row = ['menu' => $label($m)]; foreach ($years as $y) $row[(string) $y] = (int) ($agg[$m['id']]["y$y"] ?? 0); $rows[] = $row; }
        return ['type' => $type, 'columns' => array_map('strval', $years), 'rows' => $rows];
    }
    if ($type === 'month') { $cols = range(1, 12); $where = sprintf('%04d', $year); $len = 4; $start = 6; }
    elseif ($type === 'day') { $cols = range(1, (int) date('t', mktime(0, 0, 0, $month, 1, $year))); $where = sprintf('%04d-%02d', $year, $month); $len = 7; $start = 9; }
    else { $cols = range(0, 23); $where = sprintf('%04d-%02d', $year, $month); $len = 7; $start = 12; }
    $cnt = $type === 'hour' && $dedupe ? 'count(distinct session_id)' : 'count(*)';   // 시간대별만 중복 제거 토글
    $agg = [];
    foreach (q_all("select menu_id, substr(access_time,$start,2) k, $cnt c from menu_logs where substr(access_time,1,$len)=? group by menu_id, k", [$where]) as $r) $agg[$r['menu_id']][$r['k']] = (int) $r['c'];
    foreach ($menus as $m) { $row = ['menu' => $label($m)]; foreach ($cols as $c) $row[(string) $c] = $agg[$m['id']][sprintf('%02d', $c)] ?? 0; $rows[] = $row; }
    return ['type' => $type, 'columns' => array_map('strval', $cols), 'rows' => $rows];
}

/** 게시글 조회수 통계 (원천 데이터 = 조회 로그, 중복 제거 토글) */
function view_stats(string $from, string $to, bool $dedupe): array
{
    if (!strtotime($from) || !strtotime($to) || $from > $to) { $from = date('Y-m-d', strtotime('-29 days')); $to = date('Y-m-d'); }
    $topN = code_int('TOP_N', 5);
    $range = [$from . ' 00:00:00', $to . ' 23:59:59'];
    // 중복 제거 = 같은 회원(비회원은 로그별)이 같은 글을 같은 일/시간에 본 것은 1회로 계산
    $src = $dedupe
        ? 'select distinct board_id, substr(viewed_at,1,10) d, substr(viewed_at,12,2) h, coalesce(user_id, -id) u from board_views where viewed_at between ? and ?'
        : 'select board_id, substr(viewed_at,1,10) d, substr(viewed_at,12,2) h from board_views where viewed_at between ? and ?';
    $dayAgg = []; foreach (q_all("select d k, count(*) c from ($src) group by d", $range) as $r) $dayAgg[$r['k']] = (int) $r['c'];
    $hourAgg = []; foreach (q_all("select h k, count(*) c from ($src) group by h", $range) as $r) $hourAgg[$r['k']] = (int) $r['c'];
    $days = [];
    for ($t = strtotime($from); $t <= strtotime($to); $t += 86400) { $k = date('Y-m-d', $t); $days[] = ['label' => $k, 'count' => $dayAgg[$k] ?? 0]; }
    $hours = [];
    for ($h = 0; $h < 24; $h++) { $k = sprintf('%02d', $h); $hours[] = ['label' => $k . '시', 'count' => $hourAgg[$k] ?? 0]; }
    $top = q_all("select s.board_id, b.title, count(*) c from ($src) s join boards b on b.id=s.board_id group by s.board_id, b.title order by c desc, s.board_id limit $topN", $range);
    return compact('from', 'to', 'dedupe', 'topN', 'days', 'hours', 'top');
}
