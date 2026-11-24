<?php include_once_path('application/core/admin');
$qid = (int) input('qid');
q_exec('delete from question_options where question_id=?', [$qid]);
q_exec('delete from survey_answers where question_id=?', [$qid]);
q_exec('delete from questions where id=? and survey_id=?', [$qid, (int) input('survey_id')]);
back_with('ok', '문항이 삭제되었습니다.');
