<?php include_once_path('application/core/admin');
q_exec('delete from common_codes where id=?', [(int) input('id')]);
back_with('ok', '삭제되었습니다.');
