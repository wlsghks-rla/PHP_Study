<?php include_once_path('application/core/admin');
include_once_path('application/model/survey_model');
$s = q_one('select * from surveys where id=?', [(int) input('id')]) ?: fail(404, '설문조사를 찾을 수 없습니다.');
$qs = q_all('select * from questions where survey_id=? order by sort, id', [$s['id']]);
render('admin/questions', ['title' => '문항 관리', 's' => $s, 'qs' => $qs, 'opts' => survey_options(array_column($qs, 'id')), 'cats' => q_all('select * from question_categories where use_yn=1')]);
