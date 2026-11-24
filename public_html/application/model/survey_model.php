<?php
function survey_find(int $id): array
{
    $s = q_one('select * from surveys where id=? and use_yn=1', [$id]);
    if (!$s) fail(404, '설문조사를 찾을 수 없습니다.');
    return $s;
}
/** 접근 예외처리: 참여기간, 로그인 */
function survey_block(array $s): ?string
{
    $n = now_str();
    if ($n < $s['start_dt']) return '아직 설문조사 참여 기간이 아닙니다. (시작: ' . substr($s['start_dt'], 0, 16) . ')';
    if ($n > $s['end_dt']) return '설문조사 참여 기간이 종료되었습니다. (마감: ' . substr($s['end_dt'], 0, 16) . ')';
    if ($s['login_required'] && !user()) return '로그인한 회원만 참여할 수 있는 설문조사입니다.';
    return null;
}
function survey_log(array $s, bool $create = true): ?array
{
    $key = 'survey_log_' . $s['id'];
    if (user()) { $w = 'survey_id=? and user_id=?'; $p = [$s['id'], user()['id']]; }
    else { $w = 'survey_id=? and user_id is null and id=?'; $p = [$s['id'], (int) ($_SESSION[$key] ?? 0)]; }
    $ip = q_one("select * from survey_logs where $w and status='IN_PROGRESS' order by id desc", $p);
    if ($ip) return $ip;
    $done = q_one("select * from survey_logs where $w and status='DONE' order by id desc", $p);
    if ($done && !$s['allow_duplicate']) return $done;
    if (!$create) return $done;
    $id = q_insert('survey_logs', ['survey_id' => $s['id'], 'user_id' => user()['id'] ?? null, 'submit_count' => 0, 'status' => 'IN_PROGRESS', 'created_at' => now_str(), 'updated_at' => now_str()]);
    if (!user()) $_SESSION[$key] = $id;
    return q_one('select * from survey_logs where id=?', [$id]);
}
function survey_questions(array $s): array
{
    $rows = q_all('select q.*, c.name category_name from questions q left join question_categories c on c.id=q.category_id where q.survey_id=? and q.use_yn=1 order by q.sort, q.id', [$s['id']]);
    foreach ($rows as $i => &$q) $q['no'] = $i + 1;
    return $rows;
}
function survey_options(array $qids): array
{
    $o = [];
    if ($qids) foreach (q_all('select * from question_options where question_id in (' . implode(',', array_map('intval', $qids)) . ') order by sort', []) as $r) $o[$r['question_id']][] = $r;
    return $o;
}
function survey_answers(int $logId): array
{
    $a = [];
    foreach (q_all('select * from survey_answers where log_id=?', [$logId]) as $r) $a[$r['question_id']][] = $r;
    return $a;
}
function survey_validate_page(array $qs, array $input, array $opts): ?array
{
    foreach ($qs as $q) {
        $a = $input[$q['id']] ?? []; $text = trim((string) ($a['text'] ?? '')); $no = $q['no'];
        if ($q['type'] === 'TEXT') {
            if ($q['required'] && $text === '') return ['msg' => "{$no}문항에 선택해주세요.", 'qid' => $q['id']];
            if (mb_strlen($text) > 1000) return ['msg' => "{$no}문항은 1000자 이하로 입력하세요.", 'qid' => $q['id']];
            continue;
        }
        $sel = array_values(array_unique(array_map('intval', (array) ($a['opt'] ?? []))));
        $own = []; foreach ($opts[$q['id']] ?? [] as $o) $own[(int) $o['id']] = $o;
        foreach ($sel as $oid) if (!isset($own[$oid])) return ['msg' => "{$no}문항의 선택지가 올바르지 않습니다.", 'qid' => $q['id']];
        if ($q['required'] && !$sel) return ['msg' => "{$no}문항에 선택해주세요.", 'qid' => $q['id']];
        if (!$sel) continue;
        if (count($sel) > max(1, (int) $q['max_count'])) return ['msg' => "{$no}문항은 최대 {$q['max_count']}개까지 선택할 수 있습니다.", 'qid' => $q['id']];
        $none = array_filter($sel, fn ($o) => $own[$o]['is_none']); $etc = array_filter($sel, fn ($o) => $own[$o]['is_etc']);
        if ($none && count($sel) > 1) return ['msg' => "{$no}문항: '없음'은 다른 항목과 함께 선택할 수 없습니다.", 'qid' => $q['id']];
        if ($etc && $text === '') return ['msg' => "{$no}문항: '기타' 선택 시 내용을 입력해주세요.", 'qid' => $q['id']];
        if (mb_strlen($text) > 255) return ['msg' => "{$no}문항: 기타 내용은 255자 이하로 입력하세요.", 'qid' => $q['id']];
    }
    return null;
}
function survey_store_answers(array $log, array $qs, array $input, array $opts): void
{
    foreach ($qs as $q) {
        q_exec('delete from survey_answers where log_id=? and question_id=?', [$log['id'], $q['id']]);
        $a = $input[$q['id']] ?? []; $text = trim((string) ($a['text'] ?? ''));
        if ($q['type'] === 'TEXT') { if ($text !== '') q_insert('survey_answers', ['log_id' => $log['id'], 'question_id' => $q['id'], 'answer_text' => mb_substr($text, 0, 1000)]); continue; }
        $own = []; foreach ($opts[$q['id']] ?? [] as $o) $own[(int) $o['id']] = $o;
        $sel = array_slice(array_values(array_unique(array_map('intval', (array) ($a['opt'] ?? [])))), 0, max(1, (int) $q['max_count']));
        foreach ($sel as $oid) if (isset($own[$oid])) q_insert('survey_answers', ['log_id' => $log['id'], 'question_id' => $q['id'], 'option_id' => $oid, 'answer_text' => $own[$oid]['is_etc'] ? mb_substr($text, 0, 255) : null]);
    }
}
