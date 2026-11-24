<?php
function user(): ?array { return $_SESSION['user'] ?? null; }
function is_super(): bool { return (user()['role'] ?? '') === 'SUPER'; }
function level(): string { return user()['role'] ?? 'GUEST'; }
function require_login(): void { if (!user()) fail(401, '로그인이 필요합니다.'); }
function require_super(): void { if (!is_super()) fail(403, '슈퍼관리자만 접근할 수 있습니다.'); }

/** 메뉴 권한 검사(Interceptor 역할) */
function can_access(int $menuId): bool
{
    if (level() === 'SUPER') return true;
    return (bool) q_val('select 1 from menu_permissions where menu_id=? and access_level=?', [$menuId, level()]);
}
/** 접근 통계 로그(AOP 역할) */
function menu_log(array $m): void
{
    q_insert('menu_logs', ['menu_id' => $m['id'], 'menu_name' => $m['name'], 'session_id' => session_id(), 'user_id' => user()['id'] ?? null, 'access_time' => now_str()]);
}
/** 메뉴 로드 + 권한 검사 + (화면 진입 시) 접근 로그 */
function menu_guard(?int $id = null, bool $log = false): array
{
    $id = $id ?? (int) input('menu', 0);
    $m = q_one('select * from menus where id=? and deleted=0', [$id]);
    if (!$m || !$m['use_yn']) fail(404, '메뉴를 찾을 수 없습니다.');
    if (!can_access($id)) {
        if (!user()) { flash('error', '로그인이 필요한 메뉴입니다.'); redirect('/auth/login'); }
        fail(403, '접근 권한이 없습니다.');
    }
    if ($log && $_SERVER['REQUEST_METHOD'] === 'GET' && !ajax()) menu_log($m);
    return $m;
}
function visible_menus(): array
{
    return array_values(array_filter(q_all('select * from menus where deleted=0 and use_yn=1 order by sort, id'), fn ($m) => can_access((int) $m['id'])));
}
