<?php include_once_path('application/core/admin');
/** CSV 다운로드: 기간 내 전체 데이터(페이징 무관), BOM으로 한글 깨짐 방지, 한글 파일명 */
include_once_path('application/model/stats_model');
$s = view_stats((string) input('from', date('Y-m-d', strtotime('-29 days'))), (string) input('to', date('Y-m-d')), !empty($_GET['dedupe']) && $_GET['dedupe'] !== '0');
$name = str_replace('-', '', $s['from']) . '_' . str_replace('-', '', $s['to']) . '_게시글통계.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="stats.csv"; filename*=UTF-8\'\'' . rawurlencode($name));
$o = fopen('php://output', 'w');
fwrite($o, "\xEF\xBB\xBF");
fputcsv($o, ['[일별 조회수]']); fputcsv($o, ['일자', '조회수'], ',', '"', '');
foreach ($s['days'] as $d) fputcsv($o, [$d['label'], $d['count']], ',', '"', '');
fputcsv($o, []); fputcsv($o, ['[시간대별 조회수]']); fputcsv($o, ['시간', '조회수'], ',', '"', '');
foreach ($s['hours'] as $h) fputcsv($o, [$h['label'], $h['count']], ',', '"', '');
fputcsv($o, []); fputcsv($o, ['[조회수 TOP ' . $s['topN'] . ']']); fputcsv($o, ['순위', '게시글번호', '제목', '조회수'], ',', '"', '');
foreach ($s['top'] as $i => $t) fputcsv($o, [$i + 1, $t['board_id'], $t['title'], $t['c']], ',', '"', '');
fclose($o);
exit;
