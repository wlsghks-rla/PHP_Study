<?php
/** view/{name}.php 를 레이아웃과 함께 출력 */
function render(string $name, array $data = [], bool $layout = true): void
{
    extract($data);
    if ($layout) include VIEW_PATH . '/layout/header.php';
    include VIEW_PATH . '/' . $name . '.php';
    if ($layout) include VIEW_PATH . '/layout/footer.php';
}
/** 레이아웃 없는 부분 화면(AJAX/팝업용) */
function partial(string $name, array $data = []): void { render($name, $data, false); }

/** 페이징 링크 (현재 검색 조건 유지) */
function pager(array $p, string $param = 'page'): string
{
    if ($p['pages'] <= 1) return '';
    $q = $_GET; $url = fn ($n) => '?' . http_build_query(array_merge($q, [$param => $n]));
    $h = '<div class="pager">';
    $h .= $p['page'] > 1 ? '<a href="' . e($url($p['page'] - 1)) . '">‹</a>' : '<span class="dis">‹</span>';
    for ($i = max(1, $p['page'] - 4); $i <= min($p['pages'], $p['page'] + 4); $i++)
        $h .= $i == $p['page'] ? "<span class=\"cur\">$i</span>" : '<a href="' . e($url($i)) . "\">$i</a>";
    $h .= $p['page'] < $p['pages'] ? '<a href="' . e($url($p['page'] + 1)) . '">›</a>' : '<span class="dis">›</span>';
    return $h . '</div>';
}

function token_field(): string { return '<input type="hidden" name="_token" value="' . e(csrf()) . '">'; }
function sel($cond): string { return $cond ? 'selected' : ''; }
function chk($cond): string { return $cond ? 'checked' : ''; }
