<?php
/** 페이지 이동(다음/이전) 시 현재 페이지 저장 + 유효성 검사, 완료 시 전체 검사 */
include_once_path('application/model/survey_model');
$body = json_decode(file_get_contents('php://input'), true) ?: [];
$sid = (int) ($body['id'] ?? 0); $n = (int) ($body['n'] ?? 1);
$s = survey_find($sid);
if ($msg = survey_block($s)) json_out(['ok' => false, 'msg' => $msg], 403);
$log = survey_log($s);
if ($log['status'] === 'DONE') json_out(['ok' => false, 'msg' => '이미 완료된 설문입니다.'], 409);
$all = survey_questions($s);
$per = max(1, (int) $s['per_page']);
$pages = max(1, (int) ceil(count($all) / $per));
$qs = array_slice($all, ($n - 1) * $per, $per);
$input = (array) ($body['a'] ?? []); $dir = $body['dir'] ?? 'next';
$opts = survey_options(array_column($all, 'id'));
$err = $dir === 'prev' ? null : survey_validate_page($qs, $input, $opts);   // 이전 이동은 검증 없이 임시 저장
if ($err) json_out(['ok' => false] + $err, 422);
survey_store_answers($log, $qs, $input, $opts);
if ($dir === 'done') {
    $saved = survey_answers((int) $log['id']);
    foreach ($all as $q) if ($q['required'] && !isset($saved[$q['id']]))
        json_out(['ok' => false, 'msg' => "{$q['no']}문항에 선택해주세요.", 'qid' => $q['id'], 'page' => (int) floor(($q['no'] - 1) / $per) + 1], 422);
    q_update('survey_logs', ['status' => 'DONE', 'submit_count' => $log['submit_count'] + 1, 'submitted_at' => now_str(), 'updated_at' => now_str()], 'id=?', [$log['id']]);
    json_out(['ok' => true, 'redirect' => "/survey/page?id={$s['id']}&n=1", 'msg' => '설문이 완료되었습니다.']);
}
$to = $dir === 'prev' ? max(1, $n - 1) : min($pages, $n + 1);
q_update('survey_logs', ['last_page' => $to, 'updated_at' => now_str()], 'id=?', [$log['id']]);
json_out(['ok' => true, 'redirect' => "/survey/page?id={$s['id']}&n=$to"]);
