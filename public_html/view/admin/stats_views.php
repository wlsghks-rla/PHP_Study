<h2>게시글 조회수 통계</h2>
<div class="toolbar"><input type="date" id="from" value="<?= date('Y-m-d', strtotime('-29 days')) ?>"> ~ <input type="date" id="to" value="<?= date('Y-m-d') ?>">
<label><input type="checkbox" id="dedupe"> 중복 제거(동일 회원·동일 글·동일 일/시간 1회)</label><button class="btn" id="go">조회</button><span class="sp"></span><a class="btn line" id="csv" href="#">CSV 다운로드</a></div>
<div class="card"><strong>일별 조회수</strong><div class="scroll-x"><div class="bars" id="days"></div><div class="bars-l" id="daysL"></div></div></div>
<div class="card"><strong>시간대별 조회수</strong><div class="bars" id="hours"></div><div class="bars-l" id="hoursL"></div></div>
<div class="card"><strong id="topT">조회수 TOP</strong><table><thead><tr><th width="60">순위</th><th>제목</th><th width="100">조회수</th></tr></thead><tbody id="top"></tbody></table></div>
<?php $scripts = <<<'JS'
<script>
const $=id=>document.getElementById(id);
const esc=s=>String(s).replace(/[&<>"]/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
const qs=()=>new URLSearchParams({from:$('from').value,to:$('to').value,dedupe:$('dedupe').checked?1:0});
function bars(el,lab,rows,short){const max=Math.max(1,...rows.map(r=>r.count));
 $(el).innerHTML=rows.map(r=>'<div title="'+r.label+': '+r.count+'" style="height:'+(r.count/max*100)+'%;min-width:'+(short?14:10)+'px"><span>'+(r.count||'')+'</span></div>').join('');
 $(lab).innerHTML=rows.map(r=>'<span style="min-width:'+(short?14:10)+'px">'+(short?r.label.slice(5):r.label.replace('시',''))+'</span>').join('');}
async function load(){
 const j=await (await fetch('/admin/stats_views_data?'+qs(),{headers:{Accept:'application/json'}})).json();
 bars('days','daysL',j.days,true); bars('hours','hoursL',j.hours,false);
 $('topT').textContent='조회수 TOP '+j.topN; $('top').innerHTML=j.top.length?j.top.map((t,i)=>'<tr><td>'+(i+1)+'</td><td>'+esc(t.title)+'</td><td>'+t.c+'</td></tr>').join(''):'<tr><td colspan="3">데이터가 없습니다.</td></tr>';
 $('csv').href='/admin/stats_views_csv?'+qs();
}
$('go').onclick=load; load();
</script>
JS; ?>
