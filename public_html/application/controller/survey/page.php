<?php
include_once_path('application/model/survey_model');
$s = survey_find((int) input('id'));
if ($msg = survey_block($s)) { render('survey/blocked', ['title' => $s['title'], 's' => $s, 'msg' => $msg]); return; }
$log = survey_log($s);
$all = survey_questions($s);
$opts = survey_options(array_column($all, 'id'));
$ans = survey_answers((int) $log['id']);
if ($log['status'] === 'DONE') {
    render('survey/done', ['title' => $s['title'], 's' => $s, 'all' => $all, 'opts' => $opts, 'ans' => $ans, 'editable' => !survey_block($s) && user()]);
    return;
}
$per = max(1, (int) $s['per_page']);
$pages = max(1, (int) ceil(count($all) / $per));
$n = max(1, min((int) input('n', 1), $pages));
$qs = array_slice($all, ($n - 1) * $per, $per);
$pageIds = array_column($qs, 'id');
$req = array_filter($all, fn ($q) => $q['required']);
$reqDoneOther = count(array_filter($req, fn ($q) => isset($ans[$q['id']]) && !in_array($q['id'], $pageIds)));
render('survey/page', ['title' => $s['title'], 's' => $s, 'qs' => $qs, 'opts' => $opts, 'ans' => $ans, 'n' => $n, 'pages' => $pages, 'reqTotal' => count($req), 'reqDoneOther' => $reqDoneOther,
    'scripts' => '<script src="/js/survey.js"></script>']);
