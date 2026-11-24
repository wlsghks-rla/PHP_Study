<?php
/** 첨부 다운로드: 추측 불가한 UUID 이름 + 메뉴 권한 검사, 원본 파일명으로 내려줌 */
include_once_path('application/model/board_model');
$f = attachment_guard((string) input('f'));
q_insert('download_logs', ['attachment_id' => $f['id'], 'user_id' => user()['id'] ?? null, 'downloaded_at' => now_str()]);
$path = UPLOAD_DIR . '/' . $f['stored_name'];
header('Content-Type: application/octet-stream');
header("Content-Disposition: attachment; filename=\"" . rawurlencode($f['origin_name']) . "\"; filename*=UTF-8''" . rawurlencode($f['origin_name']));
header('Content-Length: ' . filesize($path));
readfile($path);
exit;
