<?php render('survey/list', ['title' => '설문조사', 'surveys' => q_all('select * from surveys where use_yn=1 order by id desc')]);
