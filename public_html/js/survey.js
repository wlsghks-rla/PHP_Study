(() => {
  const f = document.getElementById('sv');
  const qs = [...f.querySelectorAll('.q')];
  const reqTotal = +f.dataset.reqTotal, reqOther = +f.dataset.reqOther;

  const collect = q => {
    const o = {opt: [], text: ''};
    q.querySelectorAll('input[type=radio]:checked,input[type=checkbox]:checked').forEach(i => o.opt.push(i.value));
    const t = q.querySelector('[data-text]'); if (t) o.text = t.value;
    return o;
  };
  const answered = q => { const a = collect(q); return q.dataset.type === 'TEXT' ? a.text.trim() !== '' : a.opt.length > 0; };

  function progress() {
    const done = reqOther + qs.filter(q => q.dataset.req === '1' && answered(q)).length;
    const pct = reqTotal ? Math.round(done * 100 / reqTotal) : 100;
    document.getElementById('barFill').style.width = pct + '%'; document.getElementById('barTxt').textContent = pct;
  }

  // 클라이언트 유효성: 없음 단독, 최대 선택 수, 기타 텍스트 필수
  f.addEventListener('change', e => {
    const q = e.target.closest('.q'); if (!q) return;
    const i = e.target;
    if (i.type === 'checkbox') {
      if (i.checked && i.dataset.none === '1') q.querySelectorAll('input[type=checkbox]').forEach(x => { if (x !== i) x.checked = false; });
      else if (i.checked) q.querySelectorAll('input[data-none="1"]').forEach(x => x.checked = false);
      const max = +q.dataset.max;
      if (q.querySelectorAll('input[type=checkbox]:checked').length > max) { i.checked = false; alert(q.dataset.no + '문항은 최대 ' + max + '개까지 선택할 수 있습니다.'); }
    }
    progress();
  });
  f.addEventListener('input', progress);

  function validate() {
    for (const q of qs) {
      q.classList.remove('bad');
      const a = collect(q), no = q.dataset.no;
      let msg = null;
      if (q.dataset.req === '1' && !answered(q)) msg = no + '문항에 선택해주세요.';
      else if (q.dataset.type === 'CHOICE' && a.opt.length) {
        const etc = [...q.querySelectorAll('input[data-etc="1"]:checked')].length;
        if (etc && !a.text.trim()) msg = no + "문항: '기타' 선택 시 내용을 입력해주세요.";
      }
      if (msg) { q.classList.add('bad'); q.scrollIntoView({behavior: 'smooth', block: 'center'}); alert(msg); return false; }
    }
    return true;
  }

  async function send(dir) {
    if (dir !== 'prev' && !validate()) return;
    const body = {dir, id: +f.dataset.id, n: +f.dataset.n, a: {}};
    qs.forEach(q => body.a[q.dataset.qid] = collect(q));
    const r = await fetch('/survey/save', {method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json'}, body: JSON.stringify(body)});
    const j = await r.json().catch(() => ({}));
    if (!r.ok) {
      alert(j.msg || j.message || '처리 중 오류가 발생했습니다.');
      if (j.page && j.qid) { location.href = '/survey/page?id=' + f.dataset.id + '&n=' + j.page + '#q' + j.qid; return; }
      const q = f.querySelector('[data-qid="' + j.qid + '"]'); if (q) { q.classList.add('bad'); q.scrollIntoView({behavior: 'smooth', block: 'center'}); }
      return;
    }
    if (dir === 'done') alert(j.msg);
    location.href = j.redirect;
  }
  const bn = document.getElementById('btnNext'), bp = document.getElementById('btnPrev'), bd = document.getElementById('btnDone');
  if (bn) bn.onclick = () => send('next'); if (bp) bp.onclick = () => send('prev'); if (bd) bd.onclick = () => { if (confirm('설문을 완료하시겠습니까?')) send('done'); };
  progress();
  if (location.hash.startsWith('#q')) { const q = f.querySelector('[data-qid="' + location.hash.slice(2) + '"]'); if (q) { q.classList.add('bad'); q.scrollIntoView({block: 'center'}); } }
})();
