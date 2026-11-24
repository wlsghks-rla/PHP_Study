<?php include_once_path('application/core/admin');
$id = (int) input('id');
q_exec('delete from question_options where question_id in (select id from questions where survey_id=?)', [$id]);
q_exec('delete from survey_answers where log_id in (select id from survey_logs where survey_id=?)', [$id]);
q_exec('delete from survey_logs where survey_id=?', [$id]);
q_exec('delete from questions where survey_id=?', [$id]);
q_exec('delete from surveys where id=?', [$id]);
back_with('ok', '설문조사가 삭제되었습니다.');
