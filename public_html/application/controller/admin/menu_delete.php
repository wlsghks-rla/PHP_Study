<?php include_once_path('application/core/admin');
// 삭제된 메뉴도 통계에서 확인할 수 있도록 논리 삭제
q_exec('update menus set deleted=1, use_yn=0 where id=?', [(int) input('id')]);
back_with('ok', '메뉴가 삭제되었습니다. (통계 데이터는 유지됩니다)');
