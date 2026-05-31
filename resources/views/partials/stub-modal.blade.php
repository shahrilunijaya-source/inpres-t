{{-- ============================================================
     Prototype stub modal — shown when a citizen taps a module that
     is not built in this prototype. Triggered by any element with
     data-stub="Module name". Pure CSS/JS, no deps.
     ============================================================ --}}
<div class="stub-overlay" id="stubModal" hidden>
    <div class="stub-card" role="dialog" aria-modal="true" aria-labelledby="stubTitle">
        <button class="stub-x" type="button" aria-label="Tutup" data-stub-close>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
        <span class="stub-ico">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="8" rx="1"/><path d="M17 14v7M7 14v7M9 6 8 2M15 6l1-4M12 6V2M2 10h20"/></svg>
        </span>
        <span class="eyebrow">Prototaip<span class="e-dot orange"></span></span>
        <h3 id="stubTitle">Modul ini belum tersedia</h3>
        <p class="stub-msg">
            <b data-stub-name>Modul ini</b> belum disertakan dalam prototaip ini.
            Demonstrasi ini meliputi <b>Kelahiran</b>, <b>Perkahwinan</b> dan
            <b>Kad Pengenalan (MyKad)</b> sahaja. Modul penuh akan disambungkan
            dalam versi sebenar.
        </p>
        <div class="stub-actions">
            <a href="{{ url('/apply') }}" class="btn btn-teal">Cuba Modul Sedia Ada</a>
            <button type="button" class="btn btn-ghost" data-stub-close>Tutup</button>
        </div>
    </div>
</div>

<style>
.stub-overlay{position:fixed;inset:0;z-index:1000;display:flex;align-items:center;justify-content:center;padding:24px;background:rgba(0,18,17,0.55);backdrop-filter:blur(4px);animation:stubFade .18s ease-out;}
.stub-overlay[hidden]{display:none;}
.stub-card{position:relative;width:100%;max-width:440px;background:#fff;border-radius:20px;padding:36px 32px 30px;box-shadow:0 24px 60px -12px rgba(0,18,17,0.45);text-align:center;animation:stubPop .22s cubic-bezier(.2,.9,.3,1.2);}
.stub-x{position:absolute;top:16px;right:16px;width:34px;height:34px;border:0;border-radius:10px;background:var(--ai-bg-2,#f3f4f6);color:var(--ai-text-mute,#6B7280);cursor:pointer;display:inline-flex;align-items:center;justify-content:center;transition:background .15s;}
.stub-x:hover{background:var(--ai-border-hi,#e5e7eb);}
.stub-x svg{width:18px;height:18px;display:block;}
.stub-ico{display:inline-flex;align-items:center;justify-content:center;width:64px;height:64px;border-radius:18px;background:var(--ai-amber,#FF6B35);color:#fff;margin-bottom:18px;}
.stub-ico svg{width:30px;height:30px;display:block;}
.stub-card .eyebrow{justify-content:center;}
.stub-card h3{margin:8px 0 12px;font:700 24px/1.2 var(--font-sans);color:var(--ai-text,#003D3A);letter-spacing:-0.02em;}
.stub-msg{margin:0 0 24px;color:var(--ai-text-dim,#4B5563);font-size:15px;line-height:1.65;}
.stub-actions{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;}
@keyframes stubFade{from{opacity:0;}to{opacity:1;}}
@keyframes stubPop{from{opacity:0;transform:translateY(12px) scale(.96);}to{opacity:1;transform:none;}}
</style>

<script>
(function(){
  var modal=document.getElementById('stubModal');
  if(!modal)return;
  var nameEl=modal.querySelector('[data-stub-name]');
  function open(name){
    if(name&&nameEl)nameEl.textContent=name;
    modal.hidden=false;
    document.body.style.overflow='hidden';
    window.lucide&&lucide.createIcons();
  }
  function close(){modal.hidden=true;document.body.style.overflow='';}
  document.addEventListener('click',function(e){
    var t=e.target.closest('[data-stub]');
    if(t){e.preventDefault();open(t.getAttribute('data-stub'));return;}
    if(e.target.closest('[data-stub-close]')||e.target===modal){close();}
  });
  document.addEventListener('keydown',function(e){if(e.key==='Escape'&&!modal.hidden)close();});
})();
</script>
