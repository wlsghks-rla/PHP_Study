<?php
/** 에디터 이미지 업로드 → /image/UUID.ext */
require_login();
$f = $_FILES['image'] ?? null;
if (!$f || $f['error'] !== UPLOAD_ERR_OK) json_out(['ok' => false, 'msg' => '이미지가 없습니다.'], 422);
$ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']) || $f['size'] > 10 * 1048576 || @getimagesize($f['tmp_name']) === false)
    json_out(['ok' => false, 'msg' => '10MB 이하의 jpg, png, gif, webp 이미지만 업로드할 수 있습니다.'], 422);
$name = uuid() . '.' . $ext;
move_uploaded_file($f['tmp_name'], EDITOR_IMAGE_DIR . '/' . $name) || rename($f['tmp_name'], EDITOR_IMAGE_DIR . '/' . $name);
json_out(['ok' => true, 'url' => '/image/' . $name]);
