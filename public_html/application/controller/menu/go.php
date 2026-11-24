<?php
/** LINK형 메뉴: 접근 통계를 남기고 내부/외부 주소로 이동 */
$m = menu_guard(null, true);
if ($m['type'] !== 'LINK' || !$m['url']) fail(404, '링크형 메뉴가 아닙니다.');
$url = $m['url'];
if (!preg_match('#^(https?://|/)#i', $url)) $url = 'https://' . $url;
if (str_starts_with($url, '//')) $url = 'https:' . $url;
redirect($url);
