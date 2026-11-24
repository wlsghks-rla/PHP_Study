<?php include_once_path('application/core/admin');
$sid = (int) input('survey_id'); $qid = (int) input('qid', 0); $text = trim((string) input('text')); $type = input('type') === 'TEXT' ? 'TEXT' : 'CHOICE';
$errs = [];
if ($text === '') $errs[] = '문항 질문을 입력하세요.'; elseif (mb_strlen($text) > 1000) $errs[] = '문항 질문은 1000자 이하로 입력하세요.';
$lines = array_values(array_filter(array_map('trim', preg_split('/\R/u', (string) input('options', ''))), 'strlen'));
if ($type === 'CHOICE' && count($lines) < 2) $errs[] = '선택형 문항은 선택지를 2개 이상 입력하세요.';
if ($errs) back_with('errors', implode("\n", $errs));
$row = ['survey_id' => $sid, 'text' => $text, 'type' => $type, 'category_id' => input('category_id') ?: null, 'sort' => (int) input('sort', 0), 'max_count' => max(1, (int) input('max_count', 1)),
    'required' => (int) !empty($_POST['required']), 'use_yn' => $qid ? (int) !empty($_POST['use_yn']) : 1];
db()->beginTransaction();
if ($qid) q_update('questions', $row, 'id=? and survey_id=?', [$qid, $sid]); else $qid = q_insert('questions', $row);
if ($type === 'CHOICE') {
    // 이미 응답이 있는 선택지는 id를 유지하도록 텍스트 기준 동기화
    $exist = []; foreach (q_all('select * from question_options where question_id=?', [$qid]) as $o) $exist[$o['text']] = $o;
    $keep = [];
    foreach ($lines as $i => $t) {
        $etc = str_starts_with($t, '[기타]'); $none = str_starts_with($t, '[없음]');
        $clean = trim(preg_replace('/^\[(기타|없음)\]/u', '', $t)) ?: $t;
        $vals = ['text' => $clean, 'sort' => $i + 1, 'is_etc' => (int) $etc, 'is_none' => (int) $none];
        if (isset($exist[$clean])) { q_update('question_options', $vals, 'id=?', [$exist[$clean]['id']]); $keep[] = (int) $exist[$clean]['id']; }
        else $keep[] = q_insert('question_options', $vals + ['question_id' => $qid]);
    }
    q_exec('delete from question_options where question_id=? and id not in (' . implode(',', $keep) . ')', [$qid]);
} else q_exec('delete from question_options where question_id=?', [$qid]);
db()->commit();
back_with('ok', '문항이 저장되었습니다.');
