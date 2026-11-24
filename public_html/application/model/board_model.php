<?php
const MAX_FILES = 5;
const DOC_EXT = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
const IMG_EXT = ['jpg', 'jpeg', 'gif', 'webp', 'svg'];

function post_find(array $menu, int $id): array
{
    $b = q_one('select b.*, u.login_id writer_name from boards b join users u on u.id=b.writer where b.id=? and b.menu_id=? and b.deleted=0', [$id, $menu['id']]);
    if (!$b) fail(404, '게시글을 찾을 수 없습니다.');
    return $b;
}
function post_can_edit(array $b): bool { return user() && (is_super() || user()['id'] === (int) $b['writer']); }
function post_limits(array $menu): array
{
    return $menu['type'] === 'GALLERY' ? ['ext' => IMG_EXT, 'mb' => 10, 'label' => '이미지'] : ['ext' => DOC_EXT, 'mb' => 5, 'label' => '문서'];
}

/** 조회 로그는 매번 기록, 게시글 조회수는 아이디(비로그인은 세션)별 하루 1회만 증가 */
function post_count_view(array $b): void
{
    $uid = user()['id'] ?? null;
    q_insert('board_views', ['board_id' => $b['id'], 'user_id' => $uid, 'viewed_at' => now_str()]);
    $key = 'viewed_' . $b['id'] . '_' . date('Ymd');
    if ($uid) $first = (int) q_val('select count(*) from board_views where board_id=? and user_id=? and viewed_at>=?', [$b['id'], $uid, date('Y-m-d 00:00:00')]) === 1;
    else { $first = empty($_SESSION[$key]); $_SESSION[$key] = true; }
    if ($first) q_exec('update boards set view_count=view_count+1 where id=?', [$b['id']]);
}

/** 입력 검증 → [errors, title, tags] */
function post_validate(array $menu, ?array $b): array
{
    $cfg = post_limits($menu); $errs = [];
    $tags = array_values(array_unique(array_filter(preg_split('/[\s,#]+/u', (string) input('hashtags', '')), 'strlen')));
    $title = trim((string) input('title', ''));
    $content = (string) input('content', '');
    $text = html_text($content);
    if ($title === '') $errs[] = '제목을 입력하세요.'; elseif (mb_strlen($title) > 100) $errs[] = '제목은 100자 이하로 입력하세요.';
    if ($text === '' && !str_contains($content, '<img')) $errs[] = '내용을 입력하세요.'; elseif (mb_strlen($text) > 4000) $errs[] = '내용은 4000자 이하로 입력하세요.';
    if (count($tags) > 5) $errs[] = '해시태그는 최대 5개까지 등록할 수 있습니다.';
    foreach ($tags as $t) if (!preg_match('/^[\p{L}\p{N}_]{1,20}$/u', $t)) { $errs[] = "해시태그 '$t' 는 특수문자 없이 20자 이하로 입력하세요."; break; }
    $del = array_map('intval', (array) ($_POST['delete_ids'] ?? []));
    $keep = 0;
    if ($b) { $rows = q_all('select id from attachments where board_id=? and deleted=0', [$b['id']]); $keep = count(array_filter($rows, fn ($r) => !in_array((int) $r['id'], $del))); }
    $files = uploaded_files();
    if ($keep + count($files) > MAX_FILES) $errs[] = '첨부파일은 최대 ' . MAX_FILES . '개까지 등록할 수 있습니다.';
    if ($menu['type'] === 'GALLERY' && $keep + count($files) < 1) $errs[] = '갤러리는 이미지를 1개 이상 첨부해야 합니다.';
    foreach ($files as $f) {
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $cfg['ext'])) { $errs[] = "{$f['name']}: 허용되지 않는 형식입니다. (" . implode(', ', $cfg['ext']) . ')'; continue; }
        if ($f['size'] > $cfg['mb'] * 1048576) $errs[] = "{$f['name']}: 파일은 {$cfg['mb']}MB 이하만 가능합니다.";
        if ($menu['type'] === 'GALLERY' && $ext !== 'svg' && @getimagesize($f['tmp_name']) === false) $errs[] = "{$f['name']}: 올바른 이미지 파일이 아닙니다.";
    }
    return [$errs, $title, $tags];
}

/** $_FILES['files'] 를 평탄화 (업로드 성공 건만) */
function uploaded_files(): array
{
    $out = [];
    $f = $_FILES['files'] ?? null;
    if (!$f || !is_array($f['name'])) return $out;
    foreach ($f['name'] as $i => $name) if ($f['error'][$i] === UPLOAD_ERR_OK) $out[] = ['name' => $name, 'tmp_name' => $f['tmp_name'][$i], 'size' => $f['size'][$i], 'type' => $f['type'][$i]];
    return $out;
}

function post_notice_dates(bool $isNotice)
{
    if (!$isNotice) return [null, null];
    $s = input('notice_start'); $e = input('notice_end');
    if (!$s || !$e) return '공지사항은 시작일시와 종료일시를 지정해야 합니다.';
    $s = date('Y-m-d H:i:s', strtotime($s)); $e = date('Y-m-d H:i:s', strtotime($e));
    return $s > $e ? '공지 종료일시는 시작일시 이후여야 합니다.' : [$s, $e];
}

function post_save_tags(int $id, array $tags): void { foreach ($tags as $t) q_insert('hashtags', ['board_id' => $id, 'title' => $t]); }

/** 실제 파일은 storage, 메타데이터는 DB. 서버 저장명은 UUID.확장자 */
function post_save_files(array $menu, int $boardId, int $thumbIndex, $thumbId): void
{
    $new = [];
    foreach (uploaded_files() as $i => $f) {
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        $stored = uuid() . '.' . $ext;
        move_uploaded_file($f['tmp_name'], UPLOAD_DIR . '/' . $stored) || rename($f['tmp_name'], UPLOAD_DIR . '/' . $stored);
        $new[$i] = q_insert('attachments', ['board_id' => $boardId, 'origin_name' => $f['name'], 'stored_name' => $stored, 'file_size' => $f['size'],
            'file_type' => $f['type'], 'is_thumbnail' => 0, 'created_at' => now_str(), 'updated_at' => now_str()]);
    }
    if ($menu['type'] !== 'GALLERY') return;
    // 썸네일: 선택 없으면 첫 번째 이미지 고정
    $pick = null;
    if ($thumbId && q_val('select 1 from attachments where id=? and board_id=? and deleted=0', [$thumbId, $boardId])) $pick = (int) $thumbId;
    elseif (isset($new[$thumbIndex])) $pick = $new[$thumbIndex];
    $cur = q_val('select id from attachments where board_id=? and deleted=0 and is_thumbnail=1', [$boardId]);
    if (!$pick && $cur) return;
    if (!$pick) $pick = q_val('select id from attachments where board_id=? and deleted=0 order by id limit 1', [$boardId]);
    q_exec('update attachments set is_thumbnail=0 where board_id=?', [$boardId]);
    if ($pick) q_exec('update attachments set is_thumbnail=1 where id=?', [$pick]);
}

/** 댓글을 계층(깊이 포함) 순서로 */
function comment_rows(int $boardId): array
{
    $rows = q_all('select c.*, u.login_id writer_name from comments c join users u on u.id=c.writer where c.board_id=? order by c.id', [$boardId]);
    $by = [];
    foreach ($rows as $r) $by[$r['parent_id'] ?? 0][] = $r;
    $out = [];
    $walk = function ($pid, $depth) use (&$walk, $by, &$out) {
        foreach ($by[$pid] ?? [] as $c) { $c['depth'] = $depth; $out[] = $c; $walk($c['id'], $depth + 1); }
    };
    $walk(0, 0);
    return $out;
}
function comment_own(array $menu, int $id, int $cid): array
{
    post_find($menu, $id);
    $c = q_one('select * from comments where id=? and board_id=? and deleted=0', [$cid, $id]);
    if (!$c) fail(404, '댓글을 찾을 수 없습니다.');
    if (!(user() && (is_super() || user()['id'] === (int) $c['writer']))) fail(403, '권한이 없습니다.');
    return $c;
}
function attachment_guard(string $stored): array
{
    $f = preg_match('/^[0-9a-f\-]{36}\.[a-z0-9]+$/', $stored) ? q_one('select * from attachments where stored_name=? and deleted=0', [$stored]) : null;
    if (!$f) fail(404, '파일을 찾을 수 없습니다.');
    $b = q_one('select * from boards where id=? and deleted=0', [$f['board_id']]);
    if (!$b || !can_access((int) $b['menu_id'])) fail(403, '접근 권한이 없습니다.');
    return $f;
}
