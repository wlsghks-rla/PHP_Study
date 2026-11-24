<?php
/** 회원 입력 검증 (회원가입/관리자 등록·수정 공용) → 오류 메시지 배열 */
function validate_member(string $id, string $pw, bool $pwRequired, ?int $ignoreId = null): array
{
    $e = [];
    if ($id === '') $e[] = '아이디를 입력하세요.';
    elseif (!preg_match(ID_REGEX, $id)) $e[] = '아이디는 공백/특수문자 없이 영문·숫자 4~20자로 입력하세요.';
    elseif (q_val('select 1 from users where login_id=? and id<>?', [$id, $ignoreId ?? 0])) $e[] = '이미 사용 중인 아이디입니다.';
    if ($pw === '' && $pwRequired) $e[] = '비밀번호를 입력하세요.';
    elseif ($pw !== '' && (mb_strlen($pw) < 4 || mb_strlen($pw) > 50)) $e[] = '비밀번호는 4~50자로 입력하세요.';
    return $e;
}
