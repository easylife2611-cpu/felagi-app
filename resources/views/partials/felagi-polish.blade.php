<style>
:root{--fg-navy:#003366;--fg-navy-strong:#00264d;--fg-orange:#ff9933;--fg-ink:#132238;--fg-muted:#5f7185;--fg-canvas:#f5f7fa;--fg-surface:#fff;--fg-line:#e3e9ef;--fg-shadow:0 10px 28px rgba(0,35,70,.08);--fg-radius:16px}
*{box-sizing:border-box}
html{background:var(--fg-canvas)}
body{font-family:"Noto Sans Ethiopic","Noto Sans",system-ui,-apple-system,"Segoe UI",sans-serif;background:var(--fg-canvas);color:var(--fg-ink);letter-spacing:-.01em}
header{background:linear-gradient(120deg,var(--fg-navy-strong),var(--fg-navy));box-shadow:0 4px 18px rgba(0,38,77,.18)}
header .brand{letter-spacing:-.03em}
main{max-width:1180px;padding:24px clamp(16px,3vw,40px)}
.card,.offer-card,.skeleton{border:1px solid var(--fg-line);border-radius:var(--fg-radius);box-shadow:var(--fg-shadow)}
.card{padding:24px;margin-bottom:20px}
.card h3{font-size:14px;letter-spacing:.01em}
.need-title{font-size:clamp(22px,3vw,34px);letter-spacing:-.035em;line-height:1.2}
.need-desc,.card p{color:var(--fg-muted);font-size:15px;line-height:1.8}
.meta-grid{gap:14px}
.meta-item{background:#f8fafc;border:1px solid #edf1f5;border-radius:12px;padding:14px 16px}
.meta-item .lbl{color:#708196;font-size:10px;letter-spacing:.08em}
.meta-item .val{color:var(--fg-ink);font-size:15px;margin-top:4px}
.btn{min-height:44px;border-radius:11px;box-shadow:0 3px 10px rgba(0,38,77,.08);transition:transform .15s,box-shadow .15s,background .15s}
.btn:hover:not(:disabled){transform:translateY(-1px);box-shadow:0 7px 16px rgba(0,38,77,.14)}
.btn.primary{background:var(--fg-navy)}
.btn.primary:hover:not(:disabled){background:var(--fg-navy-strong)}
.btn.success{background:#147a3d}
.badge{padding:5px 11px;font-size:10px;letter-spacing:.07em}
.need-cat{padding:6px 12px;background:#eaf2fb;color:var(--fg-navy);border:1px solid #d5e5f5}
.requester{background:#f8fafc;border:1px solid #edf1f5;border-radius:12px;padding:14px}
.avatar{background:var(--fg-navy);box-shadow:0 0 0 4px #eaf2fb}
.offer-card{padding:16px;background:#fff}
.offer-card .offer-price,.budget{color:#147a3d}
.state{padding:96px 24px;color:var(--fg-muted)}
.state h3{font-size:22px;letter-spacing:-.025em}
.empty .ic,.state .ic{color:var(--fg-orange)}
.offline-banner,.pending{border-radius:12px}
.adslot{min-height:72px;border:1px dashed #cbd8e5;background:#f9fbfd;border-radius:12px;color:#7890a7}
.fab{background:var(--fg-orange);color:#17202b;box-shadow:0 10px 24px rgba(255,153,51,.35);font-weight:800}
.bn{box-shadow:0 -6px 24px rgba(0,35,70,.08)}
:focus-visible{outline:3px solid var(--fg-orange);outline-offset:3px}
@media(max-width:599px){main{padding:16px}.card{padding:18px;border-radius:14px}.meta-grid{grid-template-columns:1fr}.meta-item.full{grid-column:auto}.need-title{font-size:24px}}
@media(min-width:1200px){main{padding-top:32px}.feed{gap:18px}.card{border-radius:18px}}
@media(prefers-reduced-motion:reduce){*,*:before,*:after{transition:none!important;animation:none!important}}
</style>
