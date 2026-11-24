<?php include_once_path('application/core/admin');
render('admin/surveys', ['title' => '설문 관리', 'surveys' => q_all('select * from surveys order by id desc'), 'cats' => q_all('select * from question_categories')]);
