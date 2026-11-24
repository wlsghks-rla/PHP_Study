<?php
/** 기간 내 본인이 작성한 설문을 다시 수정 */
include_once_path('application/model/survey_model');
$s = survey_find((int) input('id'));
if (survey_block($s) || !user()) fail(403, '수정할 수 없습니다.');
$log = survey_log($s, false);
if (!$log || $log['status'] !== 'DONE') fail(404, '완료된 설문이 없습니다.');
q_update('survey_logs', ['status' => 'IN_PROGRESS', 'last_page' => 1, 'updated_at' => now_str()], 'id=?', [$log['id']]);
redirect("/survey/page?id={$s['id']}&n=1");
