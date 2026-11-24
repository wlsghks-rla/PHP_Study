<?php
$id = preg_replace('/\s+/', '', (string) input('login_id'));
if (!preg_match(ID_REGEX, $id)) json_out(['ok' => false, 'msg' => '영문·숫자 4~20자(특수문자/공백 불가)로 입력하세요.']);
$dup = (bool) q_val('select 1 from users where login_id=?', [$id]);
json_out(['ok' => !$dup, 'msg' => $dup ? '이미 사용 중인 아이디입니다.' : '사용 가능한 아이디입니다.']);
