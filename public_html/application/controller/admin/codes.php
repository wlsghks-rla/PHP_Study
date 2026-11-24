<?php include_once_path('application/core/admin');
render('admin/codes', ['title' => '공통코드', 'codes' => q_all('select * from common_codes order by code_kind, code_key+0')]);
