<?php
include_once_path('application/model/board_model');
$f = attachment_guard((string) input('f'));
$path = UPLOAD_DIR . '/' . $f['stored_name'];
$mime = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'webp' => 'image/webp', 'svg' => 'image/svg+xml'][strtolower(pathinfo($f['stored_name'], PATHINFO_EXTENSION))] ?? 'application/octet-stream';
header("Content-Type: $mime");
header("Content-Security-Policy: default-src 'none'; style-src 'unsafe-inline'; sandbox");
header('X-Content-Type-Options: nosniff');
readfile($path);
exit;
