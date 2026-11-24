<?php
/** 관리자 공통: 슈퍼관리자 검사, 저장 후 되돌아가기 */
require_super();
function back_with(string $kind, string $msg, string $to = ''): void
{
    flash($kind, $msg);
    redirect($to !== '' ? $to : ($_SERVER['HTTP_REFERER'] ?? '/'));
}
function dt(?string $v): string { return $v ? str_replace(' ', 'T', substr($v, 0, 16)) : ''; }
