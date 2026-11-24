(() => {
  const root = document.getElementById('board');
  const menu = root.dataset.menu, base = '/board/list?menu=' + menu, M = '?menu=' + menu;
  let currentId = null;
  const form = o => { const f = new FormData(); Object.entries(o).forEach(([k, v]) => f.append(k, v)); return f; };

  // ---- 목록 이벤트 ----
  root.addEventListener('click', e => {
    const tag = e.target.closest('[data-tag]');
    if (tag) { e.preventDefault(); e.stopPropagation(); location.href = base + '?tag=' + encodeURIComponent(tag.dataset.tag); return; }
    if (e.target.closest('.chk')) return;
    const item = e.target.closest('.gitem, tr[data-id] .open');
    if (item) {
      e.preventDefault();
      const id = (item.closest('[data-id]') || item).dataset.id;
      openDetail(id);
    }
  });
  const w = document.getElementById('btnWrite'); if (w) w.onclick = () => openForm('');
  const d = document.getElementById('btnDel');
  if (d) d.onclick = async () => {
    const ids = [...root.querySelectorAll('.chk:checked')].map(c => c.value);
    if (!ids.length) { alert('삭제할 게시글을 선택하세요.'); return; }
    if (!confirm(ids.length + '건을 삭제하시겠습니까? (본인 글만 삭제되며 관리자는 전체 삭제)')) return;
    const fd = new FormData(); ids.forEach(i => fd.append('ids[]', i));
    const r = await api('/board/delete' + M, {method: 'POST', body: fd});
    alert(r.ok ? r.body.msg : errText(r)); if (r.ok) location.reload();
  };
  if (root.dataset.open) openDetail(root.dataset.open);

  // ---- 상세 레이어 팝업 ----
  async function openDetail(id) {
    const r = await api('/board/detail' + M + '&id=' + id, {headers: {Accept: 'text/html'}});
    if (!r.ok) { alert(errText(r) || '게시글을 불러올 수 없습니다.'); return; }
    currentId = id;
    const m = openModal(r.body);
    bindDetail(m, id);
  }
  async function reloadDetail(id) {
    const r = await api('/board/detail' + M + '&id=' + id, {headers: {Accept: 'text/html'}});
    if (r.ok) { const m = document.querySelector('#modalOv .modal'); m.innerHTML = r.body; m.querySelectorAll('[data-close]').forEach(b => b.onclick = () => { closeModal(); }); bindDetail(m, id); }
  }
  function bindDetail(m, id) {
    m.querySelectorAll('[data-tag]').forEach(t => t.onclick = () => { closeModal(); location.href = base + '?tag=' + encodeURIComponent(t.dataset.tag); });
    const like = m.querySelector('#btnLike');
    like.onclick = async () => {
      const r = await api('/board/like' + M + '&id=' + id, {method: 'POST'});
      if (!r.ok) { alert(errText(r)); return; }
      like.dataset.liked = r.body.liked ? 1 : 0; like.innerHTML = (r.body.liked ? '♥' : '♡') + ' 좋아요 <b>' + r.body.likes + '</b>';
      alert(r.body.liked ? '좋아요를 눌렀습니다.' : '좋아요를 취소했습니다.');
    };
    const ed = m.querySelector('#btnEdit'); if (ed) ed.onclick = () => openForm('id=' + id);
    const rp = m.querySelector('#btnReply'); if (rp) rp.onclick = () => openForm('parent=' + id);
    const dl = m.querySelector('#btnDelOne');
    if (dl) dl.onclick = async () => {
      if (!confirm('이 게시글을 삭제하시겠습니까?')) return;
      const fd = new FormData(); fd.append('ids[]', id);
      const r = await api('/board/delete' + M, {method: 'POST', body: fd});
      alert(r.ok ? r.body.msg : errText(r)); if (r.ok) location.reload();
    };
    // 댓글
    const cb = m.querySelector('#btnComment');
    if (cb) cb.onclick = async () => {
      const t = m.querySelector('#newComment').value.trim();
      if (!t) { alert('댓글 내용을 입력하세요.'); return; }
      if (t.length > 500) { alert('댓글은 500자 이하로 입력하세요.'); return; }
      await sendComment(id, {content: t});
    };
    m.querySelectorAll('.cm').forEach(c => {
      const cid = c.dataset.id;
      const re = c.querySelector('.c-reply'), ee = c.querySelector('.c-edit'), de = c.querySelector('.c-del');
      if (re) re.onclick = ev => { ev.preventDefault(); inlineBox(c, '', async text => sendComment(id, {content: text, parent_id: cid})); };
      if (ee) ee.onclick = ev => { ev.preventDefault(); inlineBox(c, c.querySelector('.ctext').textContent, async text => {
        const r = await api('/board/comment_save' + M + '&id=' + id + '&cid=' + cid, {method: 'POST', body: form({content: text})});
        alert(r.ok ? r.body.msg : errText(r)); if (r.ok) reloadDetail(id); }); };
      if (de) de.onclick = async ev => { ev.preventDefault(); if (!confirm('댓글을 삭제하시겠습니까?')) return;
        const r = await api('/board/comment_delete' + M + '&id=' + id + '&cid=' + cid, {method: 'POST'}); alert(r.ok ? r.body.msg : errText(r)); if (r.ok) reloadDetail(id); };
    });
  }
  function inlineBox(c, val, onOk) {
    const old = c.querySelector('.ibox'); if (old) { old.remove(); return; }
    const b = document.createElement('div'); b.className = 'ibox'; b.style.marginTop = '6px';
    b.innerHTML = '<textarea rows="2" maxlength="500"></textarea><button class="btn sm">확인</button> <button class="btn sm gray" type="button">취소</button>';
    b.querySelector('textarea').value = val;
    b.querySelector('button.btn:not(.gray)').onclick = () => { const t = b.querySelector('textarea').value.trim(); if (!t) { alert('댓글 내용을 입력하세요.'); return; } if (t.length > 500) { alert('댓글은 500자 이하로 입력하세요.'); return; } onOk(t); };
    b.querySelector('.gray').onclick = () => b.remove();
    c.appendChild(b);
  }
  async function sendComment(id, data) {
    const r = await api('/board/comment_save' + M + '&id=' + id, {method: 'POST', body: form(data)});
    if (!r.ok) { alert(errText(r)); return; }
    alert(r.body.msg); reloadDetail(id);
  }

  // ---- 작성/수정 폼 ----
  async function openForm(q) {
    const r = await api('/board/form' + M + (q ? '&' + q : ''), {headers: {Accept: 'text/html'}});
    if (!r.ok) { alert(errText(r)); return; }
    const m = openModal(r.body);
    initForm(m);
  }
  function initForm(m) {
    const f = m.querySelector('#postForm');
    const quill = new Quill(m.querySelector('#editor'), {theme: 'snow', modules: {toolbar: {container: [['bold', 'italic', 'underline'], [{list: 'ordered'}, {list: 'bullet'}], ['link', 'image'], ['clean']],
      handlers: {image: () => {
        const i = document.createElement('input'); i.type = 'file'; i.accept = 'image/*';
        i.onchange = async () => {
          const fd = new FormData(); fd.append('image', i.files[0]);
          const r = await api('/upload-image', {method: 'POST', body: fd});
          if (!r.ok) { alert(errText(r)); return; }
          const at = (quill.getSelection(true) || {index: quill.getLength()}).index; quill.insertEmbed(at, 'image', r.body.url);
        }; i.click();
      }}}}});
    const allowed = f.dataset.ext.split(','), maxMb = +f.dataset.mb, gallery = f.dataset.gallery === '1';
    let files = [], removed = new Set();
    const list = m.querySelector('#fileList'), inp = m.querySelector('#fileInput');
    const keepCount = () => list.querySelectorAll('.exist').length - removed.size;
    function renderNew() {
      list.querySelectorAll('.newf').forEach(n => n.remove());
      files.forEach((file, i) => {
        const d = document.createElement('div'); d.className = 'newf'; d.style.cssText = 'display:flex;gap:8px;align-items:center;margin:4px 0';
        let pre = '';
        if (gallery) pre = '<img style="width:60px;height:46px;object-fit:cover;border-radius:4px"><label><input type="radio" name="thumb_pick" value="n' + i + '"> 대표</label>';
        d.innerHTML = pre + '<span></span><button type="button" class="btn sm gray">삭제</button>';
        d.querySelector('span').textContent = file.name + ' (' + (file.size / 1048576).toFixed(2) + 'MB)';
        if (gallery) d.querySelector('img').src = URL.createObjectURL(file);
        d.querySelector('button').onclick = () => { files.splice(i, 1); renderNew(); };
        list.appendChild(d);
      });
    }
    inp.onchange = () => {
      const errs = [];
      for (const file of inp.files) {
        const ext = file.name.split('.').pop().toLowerCase();
        if (!allowed.includes(ext)) { errs.push(file.name + ': 허용되지 않는 형식입니다.'); continue; }
        if (file.size > maxMb * 1048576) { errs.push(file.name + ': ' + maxMb + 'MB 이하만 가능합니다.'); continue; }
        if (keepCount() + files.length >= 5) { errs.push('첨부파일은 최대 5개까지 가능합니다.'); break; }
        files.push(file);
      }
      inp.value = ''; renderNew(); if (errs.length) alert(errs.join('\n'));
    };
    list.querySelectorAll('.rm-exist').forEach(b => b.onclick = () => {
      const row = b.closest('.exist'); removed.add(row.dataset.id); row.style.display = 'none';
      const rad = row.querySelector('input[type=radio]'); if (rad) rad.checked = false;
    });
    const nt = m.querySelector('#isNotice');
    if (nt) nt.onchange = () => m.querySelector('#noticeDates').style.display = nt.checked ? '' : 'none';

    f.addEventListener('submit', async e => {
      e.preventDefault();
      const title = f.title.value.trim(), html = quill.root.innerHTML, text = quill.getText().trim();
      const tags = (f.hashtags.value.match(/[^\s,#]+/g) || []).filter((v, i, a) => a.indexOf(v) === i);
      const errs = [];
      if (!title) errs.push('제목을 입력하세요.'); else if (title.length > 100) errs.push('제목은 100자 이하로 입력하세요.');
      if (!text && !html.includes('<img')) errs.push('내용을 입력하세요.'); else if (text.length > 4000) errs.push('내용은 4000자 이하로 입력하세요. (현재 ' + text.length + '자)');
      if (tags.length > 5) errs.push('해시태그는 최대 5개까지 등록할 수 있습니다.');
      if (tags.some(t => !/^[\p{L}\p{N}_]{1,20}$/u.test(t))) errs.push('해시태그는 특수문자 없이 20자 이하로 입력하세요.');
      if (gallery && keepCount() + files.length < 1) errs.push('갤러리는 이미지를 1개 이상 첨부해야 합니다.');
      if (nt && nt.checked) { if (!f.notice_start.value || !f.notice_end.value) errs.push('공지 시작/종료일시를 지정하세요.'); else if (f.notice_start.value > f.notice_end.value) errs.push('공지 종료일시는 시작일시 이후여야 합니다.'); }
      if (errs.length) { alert(errs.join('\n')); return; }
      const fd = new FormData();
      fd.append('title', title); fd.append('content', html); fd.append('hashtags', tags.join(' '));
      const pid = f.querySelector('[name=parent_id]'); if (pid) fd.append('parent_id', pid.value);
      if (nt && nt.checked) { fd.append('is_notice', 1); fd.append('notice_start', f.notice_start.value); fd.append('notice_end', f.notice_end.value); }
      files.forEach(x => fd.append('files[]', x)); removed.forEach(i => fd.append('delete_ids[]', i));
      const pick = f.querySelector('input[name=thumb_pick]:checked');
      if (pick) { if (pick.value[0] === 'n') fd.append('thumb_index', pick.value.slice(1)); else fd.append('thumb_id', pick.value.slice(1)); }
      const btn = f.querySelector('button[type=submit]'); btn.disabled = true;
      if (f.dataset.id) fd.append('id', f.dataset.id);
      const r = await api(f.dataset.action, {method: 'POST', body: fd});
      btn.disabled = false;
      if (!r.ok) { alert(errText(r)); return; }
      alert(r.body.msg); location.reload();
    });
  }
})();
