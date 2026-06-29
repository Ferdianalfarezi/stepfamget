<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Family Gathering PT STEP 2026</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<link rel="icon" type="image/png" href="{{ asset('images/logostep31.png') }}">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}

:root{
  --green:#3B6D11;
  --green-light:#639922;
  --green-pale:#EAF3DE;
  --green-mid:#97C459;
  --green-dark:#27500A;
  --green-xdark:#173404;
  --accent:#C0DD97;
  --black:#111;
  --white:#fff;
  --off:#f7faf3;
  --text-dark:#1a2a10;
  --text-muted:#6b7c5a;
  --border:rgba(99,153,34,0.15);
}

html{scroll-behavior:smooth}
body{font-family:'DM Sans',sans-serif;background:var(--white);color:var(--text-dark);overflow-x:hidden}

/* ── ANIMATIONS ── */
@keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(.75)}}
@keyframes fadeUp{from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:translateY(0)}}
@keyframes marquee{from{transform:translateX(0)}to{transform:translateX(-50%)}}
@keyframes bmarquee{from{transform:translateX(0)}to{transform:translateX(-50%)}}

/* ── LOGIN MODAL ── */
.login-overlay{
  position:fixed;inset:0;z-index:500;
  background:rgba(0,0,0,.78);
  backdrop-filter:blur(8px);
  display:flex;align-items:center;justify-content:center;padding:24px 16px;
  opacity:0;pointer-events:none;
  transition:opacity .28s ease;
}
.login-overlay.open{opacity:1;pointer-events:all}
.lo-bg{position:absolute;inset:0;background:radial-gradient(ellipse 80% 60% at 50% -10%,rgba(61,122,71,.38) 0%,transparent 70%);pointer-events:none}
.lo-dots{position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,.04) 1px,transparent 1px);background-size:28px 28px;pointer-events:none}
.lo-orb{position:absolute;border-radius:50%;filter:blur(70px);opacity:.16;pointer-events:none}
.lo-orb.o1{width:340px;height:340px;background:#3d7a47;top:-80px;right:-60px}
.lo-orb.o2{width:240px;height:240px;background:#f59e0b;bottom:-50px;left:-50px}

.lc-card{
  position:relative;z-index:1;
  background:rgba(15,35,10,.82);
  backdrop-filter:blur(28px);
  border:1px solid rgba(151,196,89,.25);
  border-radius:24px;
  width:100%;max-width:400px;
  padding:38px 34px;text-align:center;
  transform:translateY(20px) scale(.97);
  transition:transform .3s cubic-bezier(.22,.61,.36,1);
}
.login-overlay.open .lc-card{transform:translateY(0) scale(1)}

.lc-close{
  position:absolute;top:14px;right:16px;
  background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);
  border-radius:50%;width:32px;height:32px;
  display:flex;align-items:center;justify-content:center;
  color:rgba(255,255,255,.65);cursor:pointer;font-size:.8rem;
  transition:background .2s,color .2s;
}
.lc-close:hover{background:rgba(255,255,255,.18);color:#fff}

.lc-logo-ring{
  display:inline-flex;align-items:center;justify-content:center;
  width:68px;height:68px;border-radius:50%;
  background:linear-gradient(135deg,#3d7a47,#1a3320);
  box-shadow:0 8px 28px rgba(61,122,71,.45);
  margin-bottom:16px;
}
.lc-logo-ring i{font-size:26px;color:#fff}
.lc-brand{font-family:'Montserrat',sans-serif;font-size:1.35rem;font-weight:800;color:#fff;letter-spacing:-.3px}
.lc-tagline{font-size:.75rem;color:rgba(255,255,255,.5);margin-top:5px;margin-bottom:28px;letter-spacing:.3px}

.lc-divider{display:flex;align-items:center;gap:10px;margin-bottom:16px}
.lc-divider span{font-size:.7rem;color:rgba(255,255,255,.4);white-space:nowrap}
.lc-divider::before,.lc-divider::after{content:'';flex:1;height:1px;background:rgba(255,255,255,.12)}

.btn-role{
  display:flex;align-items:center;gap:14px;width:100%;
  background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);
  border-radius:14px;padding:15px 16px;cursor:pointer;
  text-decoration:none;margin-bottom:10px;text-align:left;
  transition:all .22s ease;
}
.btn-role:last-of-type{margin-bottom:0}
.btn-role:hover{background:rgba(255,255,255,.14);border-color:rgba(151,196,89,.45);transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.35)}

.br-icon{width:44px;height:44px;border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:18px}
.br-icon.admin{background:rgba(245,158,11,.2);color:#f59e0b}
.br-icon.guest{background:rgba(61,122,71,.32);color:#7ec88a}
.br-title{font-size:.9rem;font-weight:700;color:#fff;display:block}
.br-sub{font-size:.7rem;color:rgba(255,255,255,.45);margin-top:3px;display:block}
.br-arrow{color:rgba(255,255,255,.3);font-size:.75rem}
.lc-footer{margin-top:24px;font-size:.66rem;color:rgba(255,255,255,.22);line-height:1.7}

/* ── NAVBAR ── */
nav{
  position:fixed;top:0;left:0;right:0;z-index:100;
  display:flex;align-items:center;justify-content:space-between;
  padding:1rem 2.5rem;
  background:rgba(8,8,8,.85);backdrop-filter:blur(18px);
  border-bottom:1px solid rgba(99,153,34,.2);
  transition:padding .3s;
}
.nav-logo{
  font-family:'Montserrat',sans-serif;font-weight:800;font-size:1.05rem;
  color:var(--green-mid);letter-spacing:.06em;
  display:flex;align-items:center;gap:8px;text-decoration:none;
}
.nav-logo .dot{width:8px;height:8px;background:var(--green-mid);border-radius:50%;animation:pulse 2s infinite}
.nav-links{display:flex;gap:1.75rem;list-style:none}
.nav-links a{font-size:.82rem;color:rgba(255,255,255,.6);text-decoration:none;letter-spacing:.04em;transition:color .2s}
.nav-links a:hover{color:var(--green-mid)}
.nav-right{display:flex;align-items:center;gap:10px}

/* ── SHARED BUTTONS ── */
.btn-green{
  background:var(--green-light);color:#fff;border:none;cursor:pointer;
  font-family:'DM Sans',sans-serif;font-size:.9rem;font-weight:600;
  padding:.8rem 1.75rem;border-radius:100px;
  display:inline-flex;align-items:center;gap:8px;
  text-decoration:none;transition:background .2s,transform .15s;
}
.btn-green:hover{background:var(--green);transform:translateY(-2px)}
.btn-green .arrow-circle{width:26px;height:26px;background:rgba(255,255,255,.2);border-radius:50%;display:flex;align-items:center;justify-content:center}

.btn-ghost-dark{
  background:transparent;color:rgba(255,255,255,.65);
  border:1px solid rgba(255,255,255,.18);cursor:pointer;
  font-family:'DM Sans',sans-serif;font-size:.9rem;
  padding:.8rem 1.75rem;border-radius:100px;
  text-decoration:none;display:inline-flex;align-items:center;gap:6px;
  transition:border-color .2s,color .2s;
}
.btn-ghost-dark:hover{border-color:var(--green-mid);color:var(--green-mid)}

.nav-cta{
  background:var(--green-light);color:#fff;border:none;cursor:pointer;
  font-family:'DM Sans',sans-serif;font-size:.82rem;font-weight:600;
  padding:.5rem 1.25rem;border-radius:100px;
  display:inline-flex;align-items:center;gap:6px;
  transition:background .2s,transform .15s;
}
.nav-cta:hover{background:var(--green);transform:scale(1.03)}

/* ── HAMBURGER ── */
.hamburger{display:none;flex-direction:column;gap:5px;background:none;border:none;cursor:pointer;padding:4px}
.hamburger span{display:block;width:22px;height:2px;background:rgba(255,255,255,.8);border-radius:2px;transition:all .3s}
.hamburger.open span:nth-child(1){transform:translateY(7px) rotate(45deg)}
.hamburger.open span:nth-child(2){opacity:0}
.hamburger.open span:nth-child(3){transform:translateY(-7px) rotate(-45deg)}

/* ── MOBILE MENU ── */
.mobile-menu{
  position:fixed;top:64px;left:0;right:0;z-index:99;
  background:rgba(6,6,6,.97);backdrop-filter:blur(20px);
  padding:1.25rem 1.5rem 1.75rem;
  border-bottom:1px solid rgba(99,153,34,.2);
  flex-direction:column;gap:0;
  display:flex;transform:translateY(-12px);opacity:0;pointer-events:none;
  transition:all .25s ease;
}
.mobile-menu.open{transform:translateY(0);opacity:1;pointer-events:all}
.mobile-menu a{
  display:flex;align-items:center;gap:10px;padding:.85rem 0;
  color:rgba(255,255,255,.72);text-decoration:none;font-size:.95rem;
  border-bottom:1px solid rgba(255,255,255,.06);transition:color .2s;
}
.mobile-menu a:hover{color:var(--green-mid)}
.mobile-menu a i{width:18px;color:var(--green-mid);font-size:.85rem}
.mobile-menu-divider{height:1px;background:rgba(99,153,34,.2);margin:1rem 0 .75rem}
.mobile-masuk-btn{
  display:flex;align-items:center;justify-content:center;gap:8px;width:100%;
  background:var(--green-light);color:#fff;border:none;cursor:pointer;
  font-family:'DM Sans',sans-serif;font-size:.88rem;font-weight:600;
  padding:.75rem 1.25rem;border-radius:100px;transition:background .2s;margin-top:.25rem;
}
.mobile-masuk-btn:hover{background:var(--green)}

/* ── HERO ── */
.hero{
  min-height:100vh;
  display:flex;align-items:center;gap:2rem;
  padding:10rem 2.5rem 4rem;
  position:relative;overflow:hidden;
  background:var(--black);color:var(--white);
}
.hero-bg{
  position:absolute;inset:0;pointer-events:none;
  background:
    radial-gradient(ellipse 65% 65% at 65% 40%,rgba(59,109,17,.42) 0%,transparent 65%),
    radial-gradient(ellipse 40% 40% at 15% 85%,rgba(99,153,34,.18) 0%,transparent 60%);
}
.hero-grid{
  position:absolute;inset:0;
  background-image:linear-gradient(rgba(99,153,34,.07) 1px,transparent 1px),linear-gradient(90deg,rgba(99,153,34,.07) 1px,transparent 1px);
  background-size:55px 55px;
}

/* hero left */
.hero-content-left{flex:1;position:relative;z-index:1}
.hero h1{
  font-family:'Montserrat',sans-serif;
  font-size:clamp(3.2rem,8.5vw,8rem);
  font-weight:800;line-height:.9;letter-spacing:-.03em;
  max-width:820px;margin-bottom:1.25rem;
  animation:fadeUp .65s ease both .2s;
}
.hero h1 .outline{-webkit-text-stroke:2px var(--green-mid);color:transparent}
.hero-meta{display:flex;flex-direction:column;gap:5px;margin-bottom:1rem}
.hero-meta .big{font-family:'Montserrat',sans-serif;font-size:1rem;font-weight:700;color:var(--green-mid)}
.hero-meta .small{font-size:.7rem;color:rgba(255,255,255,.35);letter-spacing:.12em;text-transform:uppercase}
.hero-sub{display:flex;align-items:flex-end;gap:3rem;margin-top:1.5rem;flex-wrap:wrap;animation:fadeUp .65s ease both .38s}
.hero-desc{font-size:.95rem;color:rgba(255,255,255,.5);max-width:320px;line-height:1.75;font-weight:300}

/* hero right — logo */
.hero-logo-area{
  position:relative;z-index:1;
  flex-shrink:0;
  display:flex;align-items:center;justify-content:center;
  border-left:1px solid rgba(255,255,255,.1);
  padding-left:2.5rem;
}
.hero-logo-img{
  width:300px;height:auto;
  max-width:100%;
  object-fit:contain;
  filter:drop-shadow(0 0 24px rgba(151,196,89,.25));
}

/* ── MARQUEE STRIP ── */
.marquee-strip{background:var(--green-light);padding:.85rem 0;overflow:hidden;white-space:nowrap}
.marquee-track{display:inline-flex;animation:marquee 22s linear infinite}
.mq-item{font-family:'Montserrat',sans-serif;font-size:.78rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#fff;padding:0 1.75rem}
.mq-dot{color:rgba(255,255,255,.35)}

/* ── SECTION COMMONS ── */
.section-label{font-size:.7rem;letter-spacing:.18em;text-transform:uppercase;color:var(--green-light);font-weight:600;display:block;margin-bottom:.6rem}
.section-h{font-family:'Montserrat',sans-serif;font-size:clamp(1.9rem,3.5vw,3.2rem);font-weight:800;letter-spacing:-.02em;line-height:1.05;color:var(--text-dark)}
.see-link{font-size:.8rem;color:var(--green-light);text-decoration:none;border-bottom:1px solid rgba(99,153,34,.3);padding-bottom:2px;transition:border-color .2s}
.see-link:hover{border-color:var(--green-light)}

/* ── INTRO / ABOUT ── */
.intro-section{display:grid;grid-template-columns:1fr 1fr;gap:3.5rem;padding:5.5rem 2.5rem;background:var(--white)}
.intro-left{display:flex;flex-direction:column;gap:1.25rem}
.intro-left p{color:var(--text-muted);font-size:.95rem;line-height:1.8;font-weight:300;max-width:370px}
.stats-row{display:flex;gap:2.5rem;margin-top:.5rem;flex-wrap:wrap}
.stat-item{display:flex;flex-direction:column;gap:4px}
.stat-item .n{font-family:'Montserrat',sans-serif;font-size:2.2rem;font-weight:800;color:var(--green-light);line-height:1}
.stat-item .l{font-size:.72rem;color:var(--text-muted);letter-spacing:.05em}
.intro-right{display:grid;grid-template-columns:1fr 1fr;gap:.9rem;align-content:start}
.info-card{background:var(--off);border:1px solid var(--border);border-radius:14px;padding:1.25rem;transition:border-color .3s,transform .3s,box-shadow .3s}
.info-card:hover{border-color:rgba(99,153,34,.38);transform:translateY(-4px);box-shadow:0 10px 28px rgba(59,109,17,.09)}
.info-card:first-child{grid-column:span 2;background:var(--green-pale);border-color:rgba(99,153,34,.28)}
.info-card .ic-icon{font-size:1.4rem;margin-bottom:.6rem}
.info-card h3{font-family:'Montserrat',sans-serif;font-size:.95rem;font-weight:700;margin-bottom:.4rem;color:var(--text-dark)}
.info-card p{font-size:.82rem;color:var(--text-muted);line-height:1.6}

/* ── FEATURE SPLIT ── */
.feature-split{display:grid;grid-template-columns:1fr 1fr;min-height:480px}
.feat-content{background:var(--green-xdark);padding:4.5rem 3.5rem;display:flex;flex-direction:column;justify-content:center;gap:1.25rem;color:#fff}
.feat-tag{display:inline-block;background:rgba(151,196,89,.18);color:var(--green-mid);font-size:.68rem;letter-spacing:.18em;text-transform:uppercase;padding:.32rem .85rem;border-radius:100px;width:fit-content}
.feat-content h2{font-family:'Montserrat',sans-serif;font-size:clamp(1.7rem,2.8vw,2.6rem);font-weight:800;line-height:1.08;letter-spacing:-.02em;color:#fff}
.feat-content p{color:rgba(255,255,255,.52);font-size:.9rem;line-height:1.8;font-weight:300;max-width:360px}
.feat-points{display:flex;flex-direction:column;gap:.65rem}
.feat-point{display:flex;align-items:center;gap:9px;font-size:.85rem;color:rgba(255,255,255,.72)}
.feat-point .dp{width:5px;height:5px;background:var(--green-mid);border-radius:50%;flex-shrink:0}
.feat-visual{background:#eef4e6;display:grid;grid-template-columns:1fr 1fr;gap:8px;padding:1.75rem;align-content:center}
.fv-img{border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:2.2rem;opacity:.85}
.fv-img:nth-child(1){aspect-ratio:1;background:linear-gradient(135deg,#1a3d07,#3B6D11)}
.fv-img:nth-child(2){aspect-ratio:1;background:linear-gradient(135deg,#2d5a0e,#639922)}
.fv-img:nth-child(3){grid-column:span 2;height:160px;background:linear-gradient(135deg,#27500A,#97C459)}

/* ── BIG MARQUEE ── */
.big-marquee{padding:3.5rem 0;overflow:hidden;background:var(--white);border-top:1px solid var(--border);border-bottom:1px solid var(--border)}
.bm-track{display:flex;white-space:nowrap;animation:bmarquee 26s linear infinite}
.bm-word{font-family:'Montserrat',sans-serif;font-size:clamp(2.5rem,5vw,4.5rem);font-weight:800;letter-spacing:-.02em;padding:0 1.25rem;display:inline-flex;align-items:center;gap:1.25rem;color:var(--text-dark)}
.bm-word.out{-webkit-text-stroke:1.5px rgba(59,109,17,.38);color:transparent}
.bm-sep{font-size:.9rem;color:var(--green-mid)}

/* ── GALLERY ── */
.gallery-section{padding:5.5rem 2.5rem;background:var(--white)}
.section-top{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:2.5rem}
.gallery-mosaic{display:grid;grid-template-columns:repeat(4,1fr);grid-template-rows:repeat(2,190px);gap:.9rem}
.g-tile{border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:2.8rem;cursor:pointer;border:1px solid var(--border);overflow:hidden;transition:transform .3s,opacity .3s}
.g-tile:hover{transform:scale(.975);opacity:.9}
.g-tile:nth-child(1){grid-column:span 2;background:linear-gradient(135deg,#1a4a08,#3B6D11)}
.g-tile:nth-child(2){background:linear-gradient(135deg,#2d5a0e,#97C459)}
.g-tile:nth-child(3){background:linear-gradient(135deg,#173404,#639922)}
.g-tile:nth-child(4){background:linear-gradient(135deg,#27500A,#3B6D11)}
.g-tile:nth-child(5){grid-column:span 2;background:linear-gradient(135deg,#3B6D11,#C0DD97)}

/* ── CTA ── */
.cta-section{padding:7rem 2.5rem;background:var(--green-dark);text-align:center;position:relative;overflow:hidden;color:#fff}
.cta-bg{position:absolute;inset:0;background:radial-gradient(ellipse 60% 80% at 50% 50%,rgba(151,196,89,.14) 0%,transparent 70%)}
.cta-section .section-label{color:var(--green-mid)}
.cta-section h2{font-family:'Montserrat',sans-serif;font-size:clamp(2.2rem,5.5vw,4.5rem);font-weight:800;line-height:.95;letter-spacing:-.03em;margin:.75rem 0 1.25rem;color:#fff;position:relative}
.cta-section p{color:rgba(255,255,255,.52);font-size:.98rem;max-width:400px;margin:0 auto 2.25rem;line-height:1.8;font-weight:300;position:relative}
.cta-stats{display:flex;gap:2.5rem;justify-content:center;flex-wrap:wrap;margin-top:3.5rem;position:relative;padding-top:2.5rem;border-top:1px solid rgba(255,255,255,.1)}
.cs{text-align:center}
.cs .n{font-family:'Montserrat',sans-serif;font-size:2.2rem;font-weight:800;color:var(--green-mid);display:block}
.cs .l{font-size:.72rem;color:rgba(255,255,255,.38);letter-spacing:.1em;text-transform:uppercase}

/* ── FOOTER ── */
footer{background:var(--off);padding:2.5rem;display:flex;align-items:center;justify-content:space-between;border-top:1px solid var(--border);flex-wrap:wrap;gap:1rem}
.f-logo{font-family:'Montserrat',sans-serif;font-weight:800;font-size:1rem;color:var(--green-light)}
.f-nav{display:flex;gap:1.75rem;flex-wrap:wrap}
.f-nav a{font-size:.78rem;color:var(--text-muted);text-decoration:none;transition:color .2s}
.f-nav a:hover{color:var(--green-light)}
.f-copy{font-size:.72rem;color:#aab4a0}

/* ── RESPONSIVE ≤ 900px ── */
@media(max-width:900px){
  nav{padding:.85rem 1.5rem}
  .nav-links,.nav-right{display:none}
  .hamburger{display:flex}
  .hero{padding:9rem 1.5rem 3.5rem;flex-direction:column;align-items:flex-start}
  .hero-logo-area{
    width:100%;border-left:none;border-top:1px solid rgba(255,255,255,.1);
    padding:2rem 0 0;justify-content:center;
  }
  .hero-logo-img{width:min(280px, 80vw)}
  .hero-meta{flex-direction:row;align-items:center;gap:12px;margin-top:.5rem}
  .hero-meta .small{display:none}
  .intro-section{grid-template-columns:1fr;padding:4rem 1.5rem}
  .intro-left p{max-width:100%}
  .feature-split{grid-template-columns:1fr}
  .feat-visual{min-height:240px}
  .gallery-section{padding:4rem 1.5rem}
  .gallery-mosaic{grid-template-columns:1fr 1fr;grid-template-rows:auto}
  .g-tile:nth-child(1),.g-tile:nth-child(5){grid-column:span 2}
  .cta-section{padding:5rem 1.5rem}
  footer{padding:2rem 1.5rem}
}

/* ── RESPONSIVE ≤ 580px ── */
@media(max-width:580px){
  nav{padding:.75rem 1rem}
  .mobile-menu{top:58px}
  .hero{padding:7.5rem 1.25rem 3rem}
  .hero h1{font-size:clamp(2.4rem,11vw,3.8rem);line-height:1.05;margin-bottom:.5rem}
  .hero h1 .outline{-webkit-text-stroke:1.5px var(--green-mid)}
  .hero-sub{flex-direction:column;align-items:flex-start;gap:1.25rem;margin-top:1rem}
  .hero-desc{font-size:.88rem;max-width:100%}
  .stats-row{gap:1.5rem}
  .intro-right{grid-template-columns:1fr}
  .info-card:first-child{grid-column:span 1}
  .feat-content{padding:3rem 1.5rem}
  .bm-word{font-size:clamp(2rem,10vw,3rem)}
  .gallery-mosaic{grid-template-columns:1fr;grid-template-rows:auto}
  .g-tile:nth-child(1),.g-tile:nth-child(5){grid-column:span 1}
  .g-tile{height:160px}
  .cta-stats{gap:1.5rem}
  footer{flex-direction:column;align-items:flex-start}
  footer .f-nav{gap:1.25rem}
  .lc-card{padding:30px 22px;border-radius:20px}
}
</style>
</head>
<body>

<!-- ═══ LOGIN MODAL ═══ -->
<div class="login-overlay" id="loginOverlay">
  <div class="lo-bg"></div>
  <div class="lo-dots"></div>
  <div class="lo-orb o1"></div>
  <div class="lo-orb o2"></div>
  <div class="lc-card">
    <button class="lc-close" id="closeModal" aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>
    <div class="lc-logo-ring"><i class="fa-solid fa-people-roof"></i></div>
    <div class="lc-brand">FAMGET 2026</div>
    <div class="lc-tagline">Family Gathering PT STEP</div>
    <div class="lc-divider"><span>Masuk sebagai</span></div>
    <a href="{{ route('login') }}" class="btn-role">
      <div class="br-icon admin"><i class="fa-solid fa-shield-halved"></i></div>
      <div class="br-info"><span class="br-title">Administrator</span><span class="br-sub">Kelola data karyawan &amp; sistem</span></div>
      <i class="fa-solid fa-chevron-right br-arrow"></i>
    </a>
    <a href="{{ route('login.guest') }}" class="btn-role">
      <div class="br-icon guest"><i class="fa-solid fa-id-card"></i></div>
      <div class="br-info"><span class="br-title">Karyawan</span><span class="br-sub">Lihat profil &amp; konfirmasi kehadiran</span></div>
      <i class="fa-solid fa-chevron-right br-arrow"></i>
    </a>
    <div class="lc-footer">&copy; 2026 PT STEP &mdash; step.co.id<br>Semua hak dilindungi</div>
  </div>
</div>

<!-- ═══ NAVBAR ═══ -->
<nav id="mainNav">
  <a href="#" class="nav-logo"><span class="dot"></span>FAMGET 2026</a>
  <ul class="nav-links">
    <li><a href="#about">Tentang</a></li>
    <li><a href="#gallery">Gallery</a></li>
  </ul>
  <div class="nav-right">
    <button onclick="openLoginModal()" class="nav-cta">
      <i class="fa-solid fa-right-to-bracket" style="font-size:.75rem"></i>Masuk
    </button>
  </div>
  <button class="hamburger" id="hamburger" aria-label="Menu">
    <span></span><span></span><span></span>
  </button>
</nav>

<!-- ═══ MOBILE MENU ═══ -->
<div class="mobile-menu" id="mobileMenu">
  <a href="#about" class="mob-link"><i class="fa-solid fa-circle-info"></i>Tentang Acara</a>
  <a href="#gallery" class="mob-link"><i class="fa-solid fa-images"></i>Gallery</a>
  <div class="mobile-menu-divider"></div>
  <button class="mobile-masuk-btn" onclick="openLoginModal()">
    <i class="fa-solid fa-right-to-bracket"></i>Masuk
  </button>
</div>

<!-- ═══ HERO ═══ -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-grid"></div>

  <div class="hero-content-left">
    <h1>Family<br><span class="outline">Gathering</span></h1>
    <div class="hero-meta">
      <span class="big"><i class="fa-regular fa-calendar" style="margin-right:5px"></i>COMING SOON!!</span>
      <span class="small">The Jungle Waterpark, Bogor</span>
    </div>
    <div class="hero-sub">
      <p class="hero-desc">Rayakan kebersamaan bersama seluruh keluarga besar PT STEP. Ciptakan momen tak terlupakan, penuh tawa dan kenangan indah.</p>
      <button onclick="openLoginModal()" class="btn-green" style="border:none;cursor:pointer;margin-top:1.5rem;">
        Masuk
        <span class="arrow-circle"><i class="fa-solid fa-arrow-right" style="font-size:.7rem"></i></span>
      </button>
    </div>
  </div>

  <div class="hero-logo-area">
    <img src="/images/logostep31.png" alt="Logo PT STEP" class="hero-logo-img">
  </div>
</section>

<!-- ═══ MARQUEE ═══ -->
<div class="marquee-strip">
  <div class="marquee-track">
    <span class="mq-item">Family Gathering</span><span class="mq-item mq-dot">◆</span>
    <span class="mq-item">PT STEP 2026</span><span class="mq-item mq-dot">◆</span>
    <span class="mq-item">Kebersamaan</span><span class="mq-item mq-dot">◆</span>
    <span class="mq-item">Fun &amp; Games</span><span class="mq-item mq-dot">◆</span>
    <span class="mq-item">Togetherness</span><span class="mq-item mq-dot">◆</span>
    <span class="mq-item">Family Gathering</span><span class="mq-item mq-dot">◆</span>
    <span class="mq-item">PT STEP 2026</span><span class="mq-item mq-dot">◆</span>
    <span class="mq-item">Kebersamaan</span><span class="mq-item mq-dot">◆</span>
    <span class="mq-item">Fun &amp; Games</span><span class="mq-item mq-dot">◆</span>
    <span class="mq-item">Togetherness</span><span class="mq-item mq-dot">◆</span>
  </div>
</div>

<!-- ═══ ABOUT ═══ -->
<section class="intro-section" id="about">
  <div class="intro-left">
    <span class="section-label">Tentang Acara</span>
    <h2 class="section-h">Momen Istimewa Untuk Keluarga Besar Kita</h2>
    <p>Family Gathering tahunan yang paling ditunggu-tunggu! Rayakan kebersamaan, bangun kenangan manis, dan pererat ikatan antar keluarga PT STEP.</p>
    <div class="stats-row">
      <div class="stat-item"><span class="n">1900+</span><span class="l">Peserta</span></div>
      <div class="stat-item"><span class="n">10+</span><span class="l">Kegiatan</span></div>
    </div>
  </div>
  <div class="intro-right">
    <div class="info-card">
      <div class="ic-icon">🎊</div>
      <h3>Kenapa Harus Ikut?</h3>
      <p>Bergabunglah bersama ratusan keluarga PT STEP dalam perayaan kebersamaan yang penuh kehangatan dan keceriaan.</p>
    </div>
  </div>
</section>

<!-- ═══ FEATURE SPLIT ═══ -->
<section class="feature-split">
  <div class="feat-content">
    <span class="feat-tag">Highlight Acara</span>
    <h2>Nyalakan Semangat Kebersamaan di Family Day</h2>
    <p>Rangkaian kegiatan seru yang dirancang untuk mempererat kebersamaan seluruh anggota keluarga, dari si kecil hingga orang tua.</p>
    <div class="feat-points">
      <div class="feat-point"><span class="dp"></span>Doorprize berhadiah menarik</div>
      <div class="feat-point"><span class="dp"></span>Pentas seni &amp; hiburan live</div>
    </div>
  </div>
  <div class="feat-visual">
    <div class="fv-img">🎪</div>
    <div class="fv-img">🏆</div>
    <div class="fv-img">🎶</div>
  </div>
</section>

<!-- ═══ BIG MARQUEE ═══ -->
<div class="big-marquee">
  <div class="bm-track">
    <span class="bm-word">Keluarga <span class="bm-sep">✦</span></span>
    <span class="bm-word out">Kebersamaan <span class="bm-sep">✦</span></span>
    <span class="bm-word">Ceria <span class="bm-sep">✦</span></span>
    <span class="bm-word out">Harmonis <span class="bm-sep">✦</span></span>
    <span class="bm-word">Kenangan <span class="bm-sep">✦</span></span>
    <span class="bm-word out">Bahagia <span class="bm-sep">✦</span></span>
    <span class="bm-word">Keluarga <span class="bm-sep">✦</span></span>
    <span class="bm-word out">Kebersamaan <span class="bm-sep">✦</span></span>
    <span class="bm-word">Ceria <span class="bm-sep">✦</span></span>
    <span class="bm-word out">Harmonis <span class="bm-sep">✦</span></span>
    <span class="bm-word">Kenangan <span class="bm-sep">✦</span></span>
    <span class="bm-word out">Bahagia <span class="bm-sep">✦</span></span>
  </div>
</div>

<!-- ═══ GALLERY ═══ -->
<section class="gallery-section" id="gallery">
  <div class="section-top">
    <div>
      <span class="section-label">Momen Terbaik</span>
      <h2 class="section-h">Gallery</h2>
    </div>
    <a href="#" class="see-link">Lihat Semua →</a>
  </div>
  <div class="gallery-mosaic">
    <div class="g-tile">👨‍👩‍👧‍👦</div>
    <div class="g-tile">🎉</div>
    <div class="g-tile">🌿</div>
    <div class="g-tile">🏅</div>
    <div class="g-tile">🎊</div>
  </div>
</section>

<!-- ═══ CTA ═══ -->
<section class="cta-section">
  <div class="cta-bg"></div>
  <span class="section-label">Jangan Sampai Ketinggalan</span>
  <h2>Amankan<br>Tempatmu</h2>
  <p>Tempat terbatas! Segera daftarkan seluruh anggota keluargamu dan dapatkan merchandise eksklusif.</p>
  <div class="cta-stats">
    <div class="cs"><span class="n">1900+</span><span class="l">Peserta</span></div>
    <div class="cs"><span class="n">98%</span><span class="l">Kepuasan Tahun Lalu</span></div>
    <div class="cs"><span class="n">100+</span><span class="l">Doorprize</span></div>
    <div class="cs"><span class="n">10+</span><span class="l">Kegiatan Seru</span></div>
  </div>
</section>

<!-- ═══ FOOTER ═══ -->
<footer>
  <div class="f-logo">● FAMGET 2026</div>
  <nav class="f-nav">
    <a href="#">Home</a>
    <a href="#about">Tentang</a>
    <a href="#gallery">Gallery</a>
  </nav>
  <span class="f-copy">&copy; 2026 Family Gathering PT STEP. All rights reserved.</span>
</footer>

<script>
const hamburger = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobileMenu');
const overlay    = document.getElementById('loginOverlay');
hamburger.addEventListener('click', () => {
  hamburger.classList.toggle('open');
  mobileMenu.classList.toggle('open');
});
document.querySelectorAll('.mob-link').forEach(a =>
  a.addEventListener('click', () => {
    hamburger.classList.remove('open');
    mobileMenu.classList.remove('open');
  })
);

// ── login modal ──
function openLoginModal() {
  overlay.classList.add('open');
  document.body.style.overflow = 'hidden';
  hamburger.classList.remove('open');
  mobileMenu.classList.remove('open');
}
function closeLoginModal() {
  overlay.classList.remove('open');
  document.body.style.overflow = '';
}
document.getElementById('closeModal').addEventListener('click', closeLoginModal);
overlay.addEventListener('click', e => { if (e.target === overlay) closeLoginModal(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLoginModal(); });

</script>
</body>
</html>