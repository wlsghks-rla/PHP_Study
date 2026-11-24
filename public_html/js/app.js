window.csrf = () => document.querySelector('meta[name=csrf-token]').content;
window.api = async (url, opt = {}) => {
  opt.headers = Object.assign({'X-CSRF-TOKEN': csrf(), 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'}, opt.headers || {});
  const res = await fetch(url, opt);
  const ct = res.headers.get('content-type') || '';
  const body = ct.includes('json') ? await res.json() : await res.text();
  return {ok: res.ok, status: res.status, body};
};
window.errText = r => {
  if (r.status === 401) return '로그인이 필요합니다.';
  if (r.status === 403) return '권한이 없습니다.';
  if (r.status === 419) return '세션이 만료되었습니다. 새로고침 후 다시 시도하세요.';
  const b = r.body;
  if (typeof b === 'string') return r.status === 404 ? '대상을 찾을 수 없습니다.' : '처리 중 오류가 발생했습니다.';
  if (b && b.errors) return Array.isArray(b.errors) ? b.errors.join('\n') : Object.values(b.errors).flat().join('\n');
  return (b && (b.msg || b.message)) || '처리 중 오류가 발생했습니다.';
};
window.openModal = html => {
  closeModal();
  const o = document.createElement('div'); o.className = 'overlay'; o.id = 'modalOv';
  o.innerHTML = '<div class="modal">' + html + '</div>';
  o.addEventListener('mousedown', e => { if (e.target === o && !o.querySelector('#postForm')) closeModal(); });
  document.body.appendChild(o);
  o.querySelectorAll('[data-close]').forEach(b => b.onclick = closeModal);
  return o.firstChild;
};
window.closeModal = () => { const o = document.getElementById('modalOv'); if (o) o.remove(); };
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
