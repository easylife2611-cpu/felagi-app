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


/* ==========================================================
   FELAGI BOTTOM NAVIGATION — L349 Polish
   ========================================================== */
:root {
    --felagi-primary: #003366;
    --felagi-primary-dark: #00264d;
    --felagi-accent: #FF9933;
    --felagi-surface: #ffffff;
    --felagi-line: #dfe7ee;
    --felagi-ink: #132238;
    --felagi-ink-muted: #586675;
    --felagi-ink-inactive: #8fa0b3;

    --felagi-nav-height: 64px;
    --felagi-nav-icon: 24px;
    --felagi-nav-touch: 44px;
    --felagi-fab-size: 56px;
}

.felagi-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: var(--felagi-nav-height);
    padding-bottom: env(safe-area-inset-bottom, 0);
    background: rgba(255, 255, 255, 0.98);
    border-top: 1px solid var(--felagi-line);
    box-shadow: 0 -1px 3px rgba(0, 38, 77, 0.06);
    display: flex;
    align-items: stretch;
    z-index: 30;
    -webkit-backdrop-filter: blur(8px);
    backdrop-filter: blur(8px);
}

.felagi-nav__item {
    position: relative;
    flex: 1 1 0;
    min-width: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 2px;
    min-height: var(--felagi-nav-touch);
    padding: 6px 4px;
    color: var(--felagi-ink-inactive);
    text-decoration: none;
    font-family: "Noto Sans Ethiopic", "Noto Sans", system-ui, -apple-system, sans-serif;
    font-size: 11px;
    font-weight: 500;
    letter-spacing: -0.005em;
    line-height: 1;
    transition: color 150ms ease;
    -webkit-tap-highlight-color: transparent;
}

.felagi-nav__item:hover {
    color: var(--felagi-ink-muted);
}

.felagi-nav__item.is-active {
    color: var(--felagi-primary);
}

.felagi-nav__item.is-active .felagi-nav__label {
    font-weight: 700;
}

.felagi-nav__item.is-active .felagi-nav__icon svg {
    stroke-width: 2.25;
}

.felagi-nav__indicator {
    position: absolute;
    top: 0;
    left: 50%;
    width: 32px;
    height: 3px;
    background: var(--felagi-primary);
    border-radius: 0 0 3px 3px;
    transform: translateX(-50%) scaleX(0);
    transform-origin: center;
    transition: transform 200ms cubic-bezier(0.4, 0, 0.2, 1);
}

.felagi-nav__item.is-active .felagi-nav__indicator {
    transform: translateX(-50%) scaleX(1);
}

.felagi-nav__icon {
    width: var(--felagi-nav-icon);
    height: var(--felagi-nav-icon);
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.felagi-nav__icon svg {
    width: 100%;
    height: 100%;
    stroke: currentColor;
    fill: none;
    stroke-width: 1.75;
}

.felagi-nav__label {
    display: block;
    max-width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.felagi-fab {
    position: fixed;
    right: 16px;
    bottom: calc(var(--felagi-nav-height) + 16px + env(safe-area-inset-bottom, 0));
    width: var(--felagi-fab-size);
    height: var(--felagi-fab-size);
    border-radius: 50%;
    background: var(--felagi-accent);
    color: #17202b;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    box-shadow:
        0 4px 12px rgba(255, 153, 51, 0.35),
        0 2px 4px rgba(0, 51, 102, 0.12);
    z-index: 31;
    transition: transform 150ms ease, box-shadow 150ms ease;
    -webkit-tap-highlight-color: transparent;
}

.felagi-fab svg {
    width: 28px;
    height: 28px;
    stroke: currentColor;
    fill: none;
}

.felagi-fab:hover {
    transform: scale(1.05);
    box-shadow:
        0 6px 16px rgba(255, 153, 51, 0.45),
        0 3px 6px rgba(0, 51, 102, 0.15);
}

.felagi-fab:active {
    transform: scale(0.96);
}

.felagi-fab:focus-visible {
    outline: 3px solid var(--felagi-primary);
    outline-offset: 3px;
}

body {
    padding-bottom: calc(var(--felagi-nav-height) + env(safe-area-inset-bottom, 0));
}

@media (min-width: 960px) {
    .felagi-nav {
        top: 0;
        bottom: 0;
        right: auto;
        width: 240px;
        height: 100vh;
        flex-direction: column;
        border-top: none;
        border-right: 1px solid var(--felagi-line);
        box-shadow: 1px 0 3px rgba(0, 38, 77, 0.04);
        padding-top: 96px;
        padding-bottom: 16px;
    }
    .felagi-nav__item {
        flex: 0 0 auto;
        flex-direction: row;
        justify-content: flex-start;
        padding: 14px 24px;
        font-size: 14px;
        gap: 14px;
    }
    .felagi-nav__item.is-active {
        background: rgba(0, 51, 102, 0.05);
        border-right: 3px solid var(--felagi-primary);
    }
    .felagi-nav__indicator {
        display: none;
    }
    .felagi-fab {
        right: auto;
        left: 232px;
        bottom: 24px;
    }
    body {
        padding-bottom: 0;
        padding-left: 240px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .felagi-nav__item,
    .felagi-nav__indicator,
    .felagi-fab {
        transition: none;
    }
}


/* ═══════════════════════════════════════════════════════════
   TOP BAR — NOTIFICATIONS (L350 addition)
   Moved from bottom nav to top bar with badge
   ═══════════════════════════════════════════════════════════ */
.felagi-topbar-alerts {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    color: #003366;
    background: transparent;
    border: none;
    border-radius: 50%;
    text-decoration: none;
    cursor: pointer;
    transition: background 150ms ease;
    -webkit-tap-highlight-color: transparent;
}

.felagi-topbar-alerts:hover {
    background: rgba(0, 51, 102, 0.08);
}

.felagi-topbar-alerts svg {
    width: 22px;
    height: 22px;
    stroke: currentColor;
    fill: none;
}

.felagi-topbar-badge {
    position: absolute;
    top: 4px;
    right: 4px;
    min-width: 16px;
    height: 16px;
    padding: 0 4px;
    border-radius: 8px;
    background: #dc2626;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    line-height: 16px;
    text-align: center;
    border: 2px solid #fff;
}

/* White variant for dark headers (browse) */
.site-header .felagi-topbar-alerts {
    color: #fff;
}

.site-header .felagi-topbar-alerts:hover {
    background: rgba(255, 255, 255, 0.15);
}

@media (prefers-reduced-motion: reduce) {
    .felagi-topbar-alerts { transition: none; }
}

</style>
