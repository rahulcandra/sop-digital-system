<?php
session_start();
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: user/dashboard.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SOP Digital System — Sinergi Nusantara Integrasi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600;12..96,700;12..96,800&family=Instrument+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
/* ─── RESET ─────────────────────────────────────────────── */
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
html{scroll-behavior:smooth;-webkit-font-smoothing:antialiased}

/* ─── VARIABLES ──────────────────────────────────────────── */
:root{
  --bg:          #030812;
  --bg2:         #060f20;
  --surface:     rgba(8,18,42,0.75);
  --surface2:    rgba(12,24,54,0.6);
  --b1:          #1248d4;
  --b2:          #2563eb;
  --b3:          #5b8ef8;
  --b4:          #93b8fb;
  --gold:        #e8a020;
  --gold2:       #f4c45a;
  --green:       #0fbe84;
  --red:         #f04e5e;
  --tw:          #eef3ff;
  --tm:          #6882a8;
  --td:          #3a5070;
  --border:      rgba(82,140,255,0.14);
  --border2:     rgba(82,140,255,0.28);
  --glow:        rgba(37,99,235,0.22);
  --radius:      16px;
  --radius-sm:   10px;
  --trans:       all 0.35s cubic-bezier(0.23,1,0.32,1);
}
[data-theme="light"]{
  --bg:      #f2f6ff;
  --bg2:     #e8efff;
  --surface: rgba(255,255,255,0.88);
  --surface2:rgba(240,247,255,0.75);
  --b3:      #1d4ed8;
  --b4:      #1d4ed8;
  --tw:      #0c1e3a;
  --tm:      #3a5a8a;
  --td:      #7a9cc0;
  --border:  rgba(37,99,235,0.14);
  --border2: rgba(37,99,235,0.3);
  --glow:    rgba(37,99,235,0.12);
}

/* ─── BASE ───────────────────────────────────────────────── */
body{
  font-family:'Instrument Sans',sans-serif;
  background:var(--bg);
  color:var(--tw);
  overflow-x:hidden;
  min-height:100vh;
  transition:background 0.5s ease,color 0.5s ease;
  cursor:default;
}

/* ─── CANVAS / BG ────────────────────────────────────────── */
#bg-canvas{
  position:fixed;inset:0;z-index:0;
  pointer-events:none;overflow:hidden;
}
.noise{
  position:absolute;inset:0;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
  background-size:128px 128px;
  opacity:0.025;mix-blend-mode:overlay;
}
[data-theme="light"] .noise{opacity:0.012}
.mesh{
  position:absolute;inset:0;
  background:
    radial-gradient(ellipse 70% 60% at 15% 30%,rgba(18,72,212,0.2) 0%,transparent 55%),
    radial-gradient(ellipse 50% 70% at 85% 75%,rgba(232,160,32,0.07) 0%,transparent 50%),
    radial-gradient(ellipse 55% 45% at 65% 5%,rgba(91,142,248,0.11) 0%,transparent 55%),
    radial-gradient(ellipse 40% 50% at 50% 50%,rgba(12,24,54,0.5) 0%,transparent 80%);
}
[data-theme="light"] .mesh{
  background:
    radial-gradient(ellipse 70% 60% at 10% 20%,rgba(18,72,212,0.12) 0%,transparent 55%),
    radial-gradient(ellipse 50% 70% at 90% 80%,rgba(232,160,32,0.06) 0%,transparent 50%);
}

/* Animated grid */
.grid-lines{
  position:absolute;inset:0;
  background-image:
    linear-gradient(rgba(82,140,255,0.05) 1px,transparent 1px),
    linear-gradient(90deg,rgba(82,140,255,0.05) 1px,transparent 1px);
  background-size:60px 60px;
  mask-image:radial-gradient(ellipse 65% 65% at 50% 40%,black 20%,transparent 80%);
  -webkit-mask-image:radial-gradient(ellipse 65% 65% at 50% 40%,black 20%,transparent 80%);
  animation:gridShift 50s linear infinite;
}
@keyframes gridShift{
  from{background-position:0 0,0 0}
  to{background-position:60px 60px,60px 60px}
}

/* Floating orbs */
.orb{position:absolute;border-radius:50%;filter:blur(90px);will-change:transform}
.orb-1{width:700px;height:700px;background:rgba(18,72,212,0.12);top:-15%;left:-10%;
  animation:drift1 30s ease-in-out infinite alternate}
.orb-2{width:500px;height:500px;background:rgba(232,160,32,0.06);bottom:-10%;right:-5%;
  animation:drift2 25s ease-in-out infinite alternate}
.orb-3{width:350px;height:350px;background:rgba(91,142,248,0.08);top:35%;right:18%;
  animation:drift3 20s ease-in-out infinite alternate}
@keyframes drift1{from{transform:translate(0,0) scale(1)}to{transform:translate(60px,40px) scale(1.1)}}
@keyframes drift2{from{transform:translate(0,0) scale(1)}to{transform:translate(-40px,-30px) scale(1.08)}}
@keyframes drift3{from{transform:translate(0,0) scale(1)}to{transform:translate(30px,-25px) scale(1.05)}}

/* ─── CURSOR GLOW ────────────────────────────────────────── */
.cursor-glow{
  position:fixed;width:400px;height:400px;
  border-radius:50%;
  background:radial-gradient(circle,rgba(37,99,235,0.08) 0%,transparent 60%);
  pointer-events:none;z-index:1;
  transform:translate(-50%,-50%);
  transition:transform 0.12s ease;
}

/* ─── NAVBAR ─────────────────────────────────────────────── */
.navbar{
  position:fixed;top:0;left:0;right:0;z-index:200;
  height:68px;padding:0 5%;
  display:flex;align-items:center;justify-content:space-between;
  background:rgba(3,8,18,0);
  transition:var(--trans),background 0.4s ease,backdrop-filter 0.4s ease;
}
.navbar.scrolled{
  background:rgba(3,8,18,0.8);
  backdrop-filter:blur(24px) saturate(1.5);
  -webkit-backdrop-filter:blur(24px) saturate(1.5);
  border-bottom:1px solid var(--border);
}
[data-theme="light"] .navbar.scrolled{background:rgba(242,246,255,0.82)}

.nav-brand{
  display:flex;align-items:center;gap:12px;
  text-decoration:none;transition:var(--trans);
}
.nav-logo-img{
  height:36px;width:auto;max-width:190px;
  object-fit:contain;
  filter:brightness(1) drop-shadow(0 0 10px rgba(91,142,248,0.25));
  transition:filter 0.4s ease;
}
[data-theme="light"] .nav-logo-img{filter:brightness(0.88)}
.nav-brand:hover .nav-logo-img{filter:brightness(1.15) drop-shadow(0 0 18px rgba(91,142,248,0.55))}

.nav-wordmark{display:flex;flex-direction:column;line-height:1.15}
.nav-name{
  font-family:'Bricolage Grotesque',sans-serif;
  font-size:13.5px;font-weight:700;
  color:var(--tw);letter-spacing:0.4px;
}
.nav-sub{
  font-size:9.5px;font-weight:400;
  color:var(--b3);letter-spacing:2.5px;
  text-transform:uppercase;
}

.nav-links{display:flex;align-items:center;gap:4px;list-style:none}
.nav-links a{
  text-decoration:none;color:var(--tm);
  font-size:13.5px;font-weight:500;
  padding:7px 15px;border-radius:var(--radius-sm);
  display:flex;align-items:center;gap:7px;
  transition:var(--trans);
}
.nav-links a:hover,.nav-links a.active{color:var(--tw);background:rgba(91,142,248,0.1)}
.nav-links a.active{color:var(--b4)}

.nav-right{display:flex;align-items:center;gap:10px}

.search-pill{
  display:flex;align-items:center;gap:9px;
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:40px;padding:8px 16px;
  transition:var(--trans);cursor:text;
}
.search-pill input{
  background:none;border:none;outline:none;
  color:var(--tw);font-family:'Instrument Sans',sans-serif;
  font-size:13px;width:155px;
}
.search-pill input::placeholder{color:var(--td)}
.search-pill i{color:var(--td);font-size:13px}
.search-pill:focus-within{
  border-color:var(--b2);
  box-shadow:0 0 0 3px rgba(37,99,235,0.12),inset 0 0 12px rgba(37,99,235,0.04);
}

.icon-btn{
  width:38px;height:38px;border-radius:var(--radius-sm);
  background:var(--surface);border:1px solid var(--border);
  color:var(--tm);display:flex;align-items:center;justify-content:center;
  cursor:pointer;font-size:15px;
  transition:var(--trans);backdrop-filter:blur(10px);
}
.icon-btn:hover{color:var(--b4);border-color:var(--border2);box-shadow:0 0 20px var(--glow)}

.btn-cta-nav{
  display:flex;align-items:center;gap:8px;
  padding:9px 22px;
  background:linear-gradient(135deg,var(--b1) 0%,var(--b2) 100%);
  color:#fff;border-radius:40px;text-decoration:none;
  font-size:13px;font-weight:600;letter-spacing:0.3px;
  transition:var(--trans);
  box-shadow:0 4px 20px rgba(18,72,212,0.35),inset 0 1px 0 rgba(255,255,255,0.15);
  position:relative;overflow:hidden;
}
.btn-cta-nav::after{
  content:'';position:absolute;inset:0;
  background:linear-gradient(135deg,rgba(255,255,255,0.1),transparent);
  opacity:0;transition:opacity 0.3s;
}
.btn-cta-nav:hover{
  transform:translateY(-1px);
  box-shadow:0 8px 28px rgba(37,99,235,0.5),inset 0 1px 0 rgba(255,255,255,0.2);
}
.btn-cta-nav:hover::after{opacity:1}

/* ─── HERO ───────────────────────────────────────────────── */
.hero{
  position:relative;z-index:10;
  min-height:100vh;
  display:flex;align-items:center;
  padding:90px 5% 50px;
}
.hero-inner{
  width:100%;display:grid;
  grid-template-columns:1fr 1.15fr;
  gap:48px;align-items:center;
}
.hero-left{max-width:580px;width:100%}

/* Badge */
.eyebrow{
  display:inline-flex;align-items:center;gap:10px;
  background:rgba(18,72,212,0.12);
  border:1px solid rgba(91,142,248,0.28);
  border-radius:100px;padding:6px 18px 6px 12px;
  font-size:11.5px;font-weight:500;
  color:var(--b4);letter-spacing:1.8px;
  text-transform:uppercase;margin-bottom:28px;
  opacity:0;transform:translateY(14px);
  animation:riseIn 0.7s cubic-bezier(0.23,1,0.32,1) 0.15s forwards;
}
.pulse-dot{
  width:7px;height:7px;border-radius:50%;
  background:var(--b2);
  box-shadow:0 0 0 3px rgba(37,99,235,0.2);
  animation:pulse 2s ease infinite;
}
@keyframes pulse{
  0%,100%{box-shadow:0 0 0 3px rgba(37,99,235,0.2)}
  50%{box-shadow:0 0 0 6px rgba(37,99,235,0)}
}

/* Headline */
.hero-h1{
  font-family:'Bebas Neue',sans-serif;
  font-size:clamp(62px,7.5vw,108px);
  font-weight:400;
  line-height:0.95;
  letter-spacing:3px;
  margin-bottom:28px;
  opacity:0;transform:translateY(22px);
  animation:riseIn 0.85s cubic-bezier(0.23,1,0.32,1) 0.28s forwards;
}
.h1-line1{
  display:block;
  color:var(--tw);
}

.h1-line2{
  display:block;
  position:relative;
  background:linear-gradient(
    105deg,
    var(--b2)    0%,
    var(--b4)   28%,
    #c8daff     42%,
    var(--gold) 55%,
    var(--gold2) 62%,
    var(--b3)   75%,
    var(--b4)  100%
  );
  background-size:250% 100%;
  -webkit-background-clip:text;
  background-clip:text;
  -webkit-text-fill-color:transparent;
  animation:shimmerText 5s ease-in-out 1s infinite;
}
@keyframes shimmerText{
  0%  {background-position:0%   50%}
  50% {background-position:100% 50%}
  100%{background-position:0%   50%}
}
/* Glow layer behind line 2 */
.h1-line2::after{
  content:attr(data-text);
  position:absolute;left:0;top:0;
  font-family:'Bebas Neue',sans-serif;
  background:linear-gradient(105deg,var(--b2),var(--b4),var(--gold));
  -webkit-background-clip:text;background-clip:text;
  -webkit-text-fill-color:transparent;
  filter:blur(22px);
  opacity:0.55;
  z-index:-1;
  animation:glowPulse 3s ease-in-out infinite;
}
@keyframes glowPulse{
  0%,100%{opacity:0.45;filter:blur(22px)}
  50%    {opacity:0.75;filter:blur(28px)}
}

/* Desc */
.hero-p{
  font-size:15.5px;line-height:1.78;
  color:var(--tm);margin-bottom:38px;
  max-width:490px;font-weight:400;
  opacity:0;transform:translateY(16px);
  animation:riseIn 0.7s cubic-bezier(0.23,1,0.32,1) 0.4s forwards;
}
.hero-p strong{color:var(--tw);font-weight:600}

/* Buttons */
.hero-actions{
  display:flex;align-items:center;gap:14px;flex-wrap:wrap;
  opacity:0;transform:translateY(14px);
  animation:riseIn 0.7s cubic-bezier(0.23,1,0.32,1) 0.52s forwards;
}
.btn-primary{
  display:inline-flex;align-items:center;gap:10px;
  padding:14px 30px;
  background:linear-gradient(135deg,var(--b1) 0%,var(--b2) 60%,#4476f5 100%);
  color:#fff;border-radius:14px;text-decoration:none;
  font-family:'Instrument Sans',sans-serif;
  font-size:15px;font-weight:600;
  transition:var(--trans);
  box-shadow:0 10px 30px rgba(18,72,212,0.4),inset 0 1px 0 rgba(255,255,255,0.18);
  position:relative;overflow:hidden;letter-spacing:0.2px;
}
.btn-primary::before{
  content:'';position:absolute;inset:0;
  background:linear-gradient(135deg,rgba(255,255,255,0.14),transparent);
  opacity:0;transition:opacity 0.3s;
}
.btn-primary:hover{transform:translateY(-3px);
  box-shadow:0 16px 40px rgba(37,99,235,0.55),inset 0 1px 0 rgba(255,255,255,0.22)}
.btn-primary:hover::before{opacity:1}
.btn-primary:active{transform:translateY(-1px)}

.btn-ghost{
  display:inline-flex;align-items:center;gap:10px;
  padding:14px 28px;
  background:var(--surface);
  color:var(--tw);
  border:1px solid var(--border2);
  border-radius:14px;text-decoration:none;
  font-family:'Instrument Sans',sans-serif;
  font-size:15px;font-weight:500;
  transition:var(--trans);backdrop-filter:blur(16px);
  letter-spacing:0.2px;
}
.btn-ghost:hover{
  background:rgba(37,99,235,0.08);
  border-color:var(--b2);color:var(--b4);
  transform:translateY(-2px);
  box-shadow:0 8px 24px rgba(37,99,235,0.12);
}

/* Stats row */
.hero-stats{
  display:flex;gap:0;
  margin-top:48px;padding-top:32px;
  border-top:1px solid var(--border);
  opacity:0;transform:translateY(14px);
  animation:riseIn 0.7s cubic-bezier(0.23,1,0.32,1) 0.64s forwards;
}
.stat-item{
  flex:1;padding-right:28px;
  position:relative;
}
.stat-item:not(:last-child)::after{
  content:'';position:absolute;right:0;top:0;bottom:0;
  width:1px;background:var(--border);
}
.stat-item:not(:first-child){padding-left:28px}
.stat-n{
  font-family:'Bricolage Grotesque',sans-serif;
  font-size:28px;font-weight:800;
  color:var(--tw);letter-spacing:-1.5px;line-height:1;
}
.stat-n em{color:var(--b3);font-style:normal}
.stat-l{
  font-size:10.5px;color:var(--td);
  text-transform:uppercase;letter-spacing:2px;
  margin-top:5px;font-weight:400;
}

/* ─── HERO RIGHT VISUAL ───────────────────────────────────── */
.hero-visual{
  position:relative;
  display:flex;
  justify-content:center;
  align-items:center;
  width:100%;
  padding:30px 55px 30px 55px;
  opacity:0;transform:translateX(30px);
  animation:slideLeft 0.9s cubic-bezier(0.23,1,0.32,1) 0.35s forwards;
}
@keyframes slideLeft{
  to{opacity:1;transform:translateX(0)}
}

/* Central glow */
.vis-glow{
  position:absolute;width:65%;height:65%;
  background:radial-gradient(circle,rgba(18,72,212,0.22) 0%,transparent 65%);
  border-radius:50%;filter:blur(50px);pointer-events:none;
}

/* Dashboard mockup */
.dashboard-wrap{
  position:relative;z-index:5;
  width:100%;max-width:580px;
  margin:0 auto;
  animation:float 11s ease-in-out infinite;
}
@keyframes float{
  0%,100%{transform:translateY(0)}
  50%{transform:translateY(-14px)}
}

.dash-card{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:22px;
  padding:26px;
  backdrop-filter:blur(20px);
  -webkit-backdrop-filter:blur(20px);
  box-shadow:
    0 40px 100px rgba(0,0,0,0.5),
    0 0 0 1px rgba(82,140,255,0.08),
    inset 0 1px 0 rgba(255,255,255,0.07);
  position:relative;overflow:hidden;
}
.dash-card::before{
  content:'';position:absolute;inset:0;
  background:linear-gradient(135deg,rgba(91,142,248,0.05) 0%,transparent 60%);
  pointer-events:none;
}

/* Topbar */
.dash-topbar{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:18px;padding-bottom:14px;
  border-bottom:1px solid var(--border);
}
.dash-dots{display:flex;gap:6px}
.dash-dot{width:10px;height:10px;border-radius:50%}
.dash-dot-r{background:#f04e5e;opacity:0.85}
.dash-dot-y{background:#f59e0b;opacity:0.85}
.dash-dot-g{background:#0fbe84;opacity:0.85}
.dash-title{
  font-family:'Bricolage Grotesque',sans-serif;
  font-size:12px;font-weight:600;
  color:var(--tm);letter-spacing:0.5px;
}
.dash-badge{
  font-size:10px;font-weight:500;
  background:rgba(15,190,132,0.15);
  color:var(--green);border-radius:20px;
  padding:3px 10px;border:1px solid rgba(15,190,132,0.25);
}

/* Stats row in dash */
.dash-stats{
  display:grid;grid-template-columns:repeat(3,1fr);
  gap:10px;margin-bottom:18px;
}
.dash-stat-cell{
  background:rgba(12,24,54,0.6);
  border:1px solid var(--border);
  border-radius:14px;padding:16px 18px;
  position:relative;overflow:hidden;
  transition:var(--trans);
}
[data-theme="light"] .dash-stat-cell{background:rgba(220,234,255,0.5)}
.dash-stat-cell:hover{border-color:var(--border2);transform:translateY(-2px)}
.dash-stat-cell::after{
  content:'';position:absolute;top:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,rgba(91,142,248,0.4),transparent);
}
.ds-num{
  font-family:'Bricolage Grotesque',sans-serif;
  font-size:28px;font-weight:800;color:var(--tw);
  letter-spacing:-1px;
}
.ds-label{font-size:11px;color:var(--td);letter-spacing:1.5px;text-transform:uppercase;margin-top:3px}
.ds-trend{
  font-size:11px;font-weight:600;margin-top:8px;
  display:flex;align-items:center;gap:4px;
}
.ds-up{color:var(--green)}.ds-down{color:var(--red)}

/* Chart mini */
.dash-chart-wrap{margin-bottom:18px}
.chart-header{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:12px;
}
.chart-label{font-size:12px;font-weight:600;color:var(--tw)}
.chart-period{
  font-size:10.5px;color:var(--tm);
  background:var(--surface2);border:1px solid var(--border);
  border-radius:6px;padding:3px 10px;
}
.chart-bars{
  display:flex;align-items:flex-end;gap:6px;height:80px;
}
.bar{
  flex:1;border-radius:4px 4px 0 0;
  min-height:8px;
  background:linear-gradient(0deg,rgba(18,72,212,0.7),rgba(91,142,248,0.9));
  transition:var(--trans);position:relative;
}
.bar:hover{filter:brightness(1.3)}
.bar.highlight{
  background:linear-gradient(0deg,var(--b1),var(--b3));
  box-shadow:0 0 12px rgba(91,142,248,0.4);
}
.bar-gold{background:linear-gradient(0deg,rgba(232,160,32,0.6),rgba(244,196,90,0.85))}
.bar-gold.highlight{box-shadow:0 0 12px rgba(232,160,32,0.35)}

/* Table */
.dash-table{
  border-radius:10px;overflow:hidden;
  border:1px solid var(--border);
}
.dt-head{
  display:grid;grid-template-columns:2fr 1.2fr 1fr;
  padding:10px 16px;
  background:rgba(12,24,54,0.8);
  font-size:10.5px;font-weight:600;
  color:var(--td);text-transform:uppercase;letter-spacing:1.5px;
}
[data-theme="light"] .dt-head{background:rgba(200,220,255,0.4)}
.dt-row{
  display:grid;grid-template-columns:2fr 1.2fr 1fr;
  padding:11px 16px;border-top:1px solid var(--border);
  font-size:13px;align-items:center;
  transition:background 0.2s;
}
.dt-row:hover{background:rgba(37,99,235,0.04)}
.dt-name{color:var(--tw);font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.dt-dept{color:var(--tm);font-size:11px}
.pill{
  display:inline-flex;align-items:center;gap:4px;
  font-size:10px;font-weight:600;
  border-radius:20px;padding:3px 9px;
}
.pill-green{background:rgba(15,190,132,0.12);color:var(--green);border:1px solid rgba(15,190,132,0.25)}
.pill-yellow{background:rgba(245,158,11,0.12);color:#f59e0b;border:1px solid rgba(245,158,11,0.25)}
.pill-red{background:rgba(240,78,94,0.1);color:var(--red);border:1px solid rgba(240,78,94,0.2)}
.pill-blue{background:rgba(91,142,248,0.12);color:var(--b4);border:1px solid rgba(91,142,248,0.25)}
.pill i{font-size:8px}

/* ─── FLOATING WIDGETS ───────────────────────────────────── */
.widget{
  position:absolute;
  background:var(--surface);
  border:1px solid var(--border2);
  border-radius:14px;
  backdrop-filter:blur(20px);
  -webkit-backdrop-filter:blur(20px);
  box-shadow:0 12px 40px rgba(0,0,0,0.35);
  z-index:20;padding:12px 16px;
  display:flex;align-items:center;gap:12px;
}
.widget-icon{
  width:34px;height:34px;border-radius:9px;
  display:flex;align-items:center;justify-content:center;
  font-size:15px;flex-shrink:0;
}
.wi-green{background:rgba(15,190,132,0.15);color:var(--green)}
.wi-blue{background:rgba(91,142,248,0.15);color:var(--b4)}
.wi-gold{background:rgba(232,160,32,0.15);color:var(--gold2)}

.widget-text .wt-label{font-size:9.5px;color:var(--td);text-transform:uppercase;letter-spacing:1.5px}
.widget-text .wt-val{font-size:13px;font-weight:700;color:var(--tw);font-family:'Bricolage Grotesque',sans-serif}

.widget-1{top:-16px;right:40px;animation:wfloat 7s ease-in-out infinite}
.widget-2{bottom:50px;left:2px;animation:wfloat 9s ease-in-out infinite;animation-delay:-3s}
.widget-3{top:44%;right:4px;animation:wfloat 8s ease-in-out infinite;animation-delay:-5s;
  padding:10px 14px;
}
@keyframes wfloat{
  0%,100%{transform:translateY(0)}
  50%{transform:translateY(-7px)}
}

/* ─── FEATURES SECTION ───────────────────────────────────── */
.features{
  position:relative;z-index:10;
  padding:40px 5% 90px;
}
.section-eyebrow{
  text-align:center;
  font-size:10.5px;font-weight:500;
  letter-spacing:3.5px;text-transform:uppercase;
  color:var(--td);margin-bottom:14px;
}
.section-title{
  text-align:center;
  font-family:'Bricolage Grotesque',sans-serif;
  font-size:clamp(22px,2.5vw,32px);
  font-weight:700;color:var(--tw);
  letter-spacing:-0.8px;margin-bottom:44px;
}
.section-title span{
  background:linear-gradient(90deg,var(--b3),var(--b4));
  -webkit-background-clip:text;background-clip:text;
  -webkit-text-fill-color:transparent;
}

.feat-grid{
  display:grid;
  grid-template-columns:repeat(4,1fr);
  gap:16px;
}
.feat-card{
  background:var(--surface2);
  border:1px solid var(--border);
  border-radius:18px;padding:26px 22px;
  transition:var(--trans);
  position:relative;overflow:hidden;
  backdrop-filter:blur(16px);
  opacity:0;transform:translateY(22px);
}
.feat-card.visible{
  opacity:1;transform:translateY(0);
}
.feat-card::after{
  content:'';position:absolute;inset:0;
  background:linear-gradient(135deg,rgba(91,142,248,0.03),transparent);
  opacity:0;transition:opacity 0.4s;
}
.feat-card:hover{
  border-color:var(--border2);
  transform:translateY(-6px);
  box-shadow:0 20px 50px rgba(0,0,0,0.2),0 0 0 1px rgba(91,142,248,0.1);
}
.feat-card:hover::after{opacity:1}

/* Top accent line */
.feat-card::before{
  content:'';position:absolute;top:0;left:20%;right:20%;height:1px;
  background:linear-gradient(90deg,transparent,rgba(91,142,248,0.5),transparent);
  opacity:0;transition:opacity 0.4s;
}
.feat-card:hover::before{opacity:1}

.feat-ic{
  width:46px;height:46px;border-radius:12px;
  display:flex;align-items:center;justify-content:center;
  font-size:20px;margin-bottom:16px;
  position:relative;
}
.feat-ic::after{
  content:'';position:absolute;inset:0;border-radius:12px;
  opacity:0;transition:opacity 0.4s;
  box-shadow:0 0 20px currentColor;
}
.feat-card:hover .feat-ic::after{opacity:0.25}
.fi-1{background:rgba(37,99,235,0.15);color:#5b8ef8}
.fi-2{background:rgba(15,190,132,0.15);color:#0fbe84}
.fi-3{background:rgba(232,160,32,0.15);color:#e8a020}
.fi-4{background:rgba(168,85,247,0.15);color:#c084fc}

.feat-h{
  font-family:'Bricolage Grotesque',sans-serif;
  font-size:15px;font-weight:700;color:var(--tw);margin-bottom:8px;
}
.feat-p{font-size:13px;color:var(--tm);line-height:1.65;font-weight:400}

/* ─── FOOTER ─────────────────────────────────────────────── */
.footer{
  position:relative;z-index:10;
  text-align:center;
  padding:28px 5%;
  border-top:1px solid var(--border);
  font-size:12px;color:var(--td);
}
.footer span{color:var(--tm)}

/* ─── ANIMATIONS ─────────────────────────────────────────── */
@keyframes riseIn{
  to{opacity:1;transform:translateY(0)}
}

/* ─── SCROLLBAR ──────────────────────────────────────────── */
::-webkit-scrollbar{width:5px}
::-webkit-scrollbar-track{background:var(--bg2)}
::-webkit-scrollbar-thumb{background:var(--b1);border-radius:4px}
::-webkit-scrollbar-thumb:hover{background:var(--b2)}

/* ─── RESPONSIVE ─────────────────────────────────────────── */
@media(max-width:1100px){
  .hero-inner{grid-template-columns:1fr;gap:50px}
  .hero-visual{order:-1;max-width:540px;margin:0 auto;padding:24px 48px}
  .feat-grid{grid-template-columns:repeat(2,1fr)}
  .nav-links,.search-pill{display:none}
  .widget-1,.widget-2,.widget-3{display:none}
}
@media(max-width:640px){
  .hero-h1{font-size:36px;letter-spacing:-1.5px}
  .hero-stats{gap:0}
  .stat-item{padding:0 16px}
  .feat-grid{grid-template-columns:1fr}
  .hero-actions{flex-direction:column;align-items:stretch}
  .btn-primary,.btn-ghost{text-align:center;justify-content:center}
}
    </style>
</head>
<body>

<!-- BG LAYER -->
<div id="bg-canvas">
  <div class="noise"></div>
  <div class="mesh"></div>
  <div class="grid-lines"></div>
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
  <div class="orb orb-3"></div>
</div>
<div class="cursor-glow" id="cursorGlow"></div>

<!-- ═══════════════ NAVBAR ═══════════════ -->
<nav class="navbar" id="navbar">
  <a href="index.php" class="nav-brand">
    <img src="assets/images/logo.png" alt="SNI" class="nav-logo-img"
         onerror="this.style.display='none'">
    <div class="nav-wordmark">
    </div>
  </a>

  <ul class="nav-links">
    <li><a href="#home" class="active"><i class="fas fa-house-chimney"></i> Beranda</a></li>
    <li><a href="#features"><i class="fas fa-th-large"></i> Fitur</a></li>
    <li><a href="#about"><i class="fas fa-circle-info"></i> Tentang</a></li>
  </ul>

  <div class="nav-right">
    <div class="search-pill">
      <i class="fas fa-magnifying-glass"></i>
      <input type="text" placeholder="Cari dokumen SOP...">
    </div>
    <button class="icon-btn" id="themeToggle" title="Ganti Tema">
      <i class="fas fa-moon" id="themeIcon"></i>
    </button>
    <a href="login.php" class="btn-cta-nav">
      <i class="fas fa-arrow-right-to-bracket"></i> Masuk
    </a>
  </div>
</nav>

<!-- ═══════════════ HERO ═══════════════ -->
<section class="hero" id="home">
  <div class="hero-inner">

    <!-- LEFT -->
    <div class="hero-left">
      <div class="eyebrow">
        <span class="pulse-dot"></span>
        Sistem Dokumen Digital & Terintegrasi
      </div>

      <h1 class="hero-h1">
        <span class="h1-line1">SINERGI NUSANTARA</span>
        <span class="h1-line2" data-text="INTEGRASI">INTEGRASI</span>
      </h1>

    <style>
        .hero-p {
        text-align: justify;
        line-height: 1.8;
        max-width: 800px;
        }
    </style>

    <p class="hero-p">
    <strong>PT. Sinergi Nusantara Integrasi (SINERGI)</strong> adalah perusahaan dengan solusi teknologi kelas dunia yang memberikan berbagai solusi inovatif berdasarkan teknologi yang terintegrasi berdasarkan pada produk perangkat lunak dengan kinerja terbaik bagi dunia usaha dalam memecahkan masalah lebih efektif dan efisien.
    </p>

      <div class="hero-actions">
        <a href="login.php" class="btn-primary">
          <i class="fas fa-rocket"></i> Masuk ke Sistem
        </a>
        <a href="#features" class="btn-ghost">
          <i class="fas fa-circle-play"></i> Pelajari Fitur
        </a>
      </div>

      <div class="hero-stats">
        <div class="stat-item">
          <div class="stat-n">50<em>+</em></div>
          <div class="stat-l">Dokumen SOP</div>
        </div>
        <div class="stat-item">
          <div class="stat-n">99<em>%</em></div>
          <div class="stat-l">Waktu Aktif</div>
        </div>
        <div class="stat-item">
          <div class="stat-n">4<em>+</em></div>
          <div class="stat-l">Kategori Aktif</div>
        </div>
      </div>
    </div>

    <!-- RIGHT: DASHBOARD MOCKUP -->
    <div class="hero-visual" id="about">
      <div class="vis-glow"></div>

      <!-- Floating widgets -->
      <div class="widget widget-1">
        <div class="widget-icon wi-green"><i class="fas fa-check-circle"></i></div>
        <div class="widget-text">
          <span class="wt-label">Status Terakhir</span>
          <span class="wt-val">SOP Disetujui</span>
        </div>
      </div>

      <div class="widget widget-2">
        <div class="widget-icon wi-blue"><i class="fas fa-file-lines"></i></div>
        <div class="widget-text">
          <span class="wt-label">Dokumen Aktif</span>
          <span class="wt-val">50+ SOP</span>
        </div>
      </div>

      <div class="widget widget-3">
        <div class="widget-icon wi-gold"><i class="fas fa-layer-group"></i></div>
        <div class="widget-text">
          <span class="wt-label">Versi</span>
          <span class="wt-val">v3.2.1</span>
        </div>
      </div>

      <!-- Dashboard card -->
      <div class="dashboard-wrap">
        <div class="dash-card">
          <!-- Top bar -->
          <div class="dash-topbar">
            <div class="dash-dots">
              <span class="dash-dot dash-dot-r"></span>
              <span class="dash-dot dash-dot-y"></span>
              <span class="dash-dot dash-dot-g"></span>
            </div>
            <span class="dash-title">SOP Digital Dashboard</span>
            <span class="dash-badge"><i class="fas fa-circle" style="font-size:7px;margin-right:4px"></i>Live</span>
          </div>

          <!-- Mini stats -->
          <div class="dash-stats">
            <div class="dash-stat-cell">
              <div class="ds-num">50</div>
              <div class="ds-label">Total SOP</div>
              <div class="ds-trend ds-up"><i class="fas fa-arrow-trend-up"></i> +8%</div>
            </div>
            <div class="dash-stat-cell">
              <div class="ds-num">3</div>
              <div class="ds-label">Pending</div>
              <div class="ds-trend ds-down"><i class="fas fa-arrow-trend-down"></i> -3</div>
            </div>
            <div class="dash-stat-cell">
              <div class="ds-num">99%</div>
              <div class="ds-label">Aktif</div>
              <div class="ds-trend ds-up"><i class="fas fa-arrow-trend-up"></i> +2%</div>
            </div>
          </div>

          <!-- Bar chart -->
          <div class="dash-chart-wrap">
            <div class="chart-header">
              <span class="chart-label">Aktivitas Dokumen</span>
              <span class="chart-period">7 hari</span>
            </div>
            <div class="chart-bars" id="chartBars">
              <div class="bar" style="height:45%"></div>
              <div class="bar" style="height:62%"></div>
              <div class="bar" style="height:38%"></div>
              <div class="bar highlight" style="height:78%"></div>
              <div class="bar" style="height:55%"></div>
              <div class="bar bar-gold highlight" style="height:90%"></div>
              <div class="bar" style="height:67%"></div>
            </div>
          </div>

          <!-- Table -->
          <div class="dash-table">
            <div class="dt-head">
              <span>Nama Dokumen</span>
              <span>Kategori</span>
              <span>Status</span>
            </div>
            <div class="dt-row">
              <span class="dt-name">SOP Pengajuan Cuti</span>
              <span class="dt-dept">Administrasi</span>
              <span><span class="pill pill-green"><i class="fas fa-check"></i> Aktif</span></span>
            </div>
            <div class="dt-row">
              <span class="dt-name">SOP Rekrutmen Karyawan</span>
              <span class="dt-dept">SDM</span>
              <span><span class="pill pill-yellow"><i class="fas fa-clock"></i> Review</span></span>
            </div>
            <div class="dt-row">
              <span class="dt-name">SOP Keuangan</span>
              <span class="dt-dept">Keuangan</span>
              <span><span class="pill pill-blue"><i class="fas fa-pen"></i> Revisi</span></span>
            </div>
            <div class="dt-row">
              <span class="dt-name">SOP Instalasi Software</span>
              <span class="dt-dept">IT & Infrastruktur</span>
              <span><span class="pill pill-yellow"><i class="fas fa-check"></i> Draft</span></span>
            </div>
          </div>

        </div>
      </div>
    </div>

  </div>
</section>

<!-- ═══════════════ FEATURES ═══════════════ -->
<section class="features" id="features">
  <p class="section-eyebrow">Platform Unggulan</p>
  <h2 class="section-title">Semua yang Anda Butuhkan, <span>dalam Satu Platform</span></h2>

  <div class="feat-grid">
    <div class="feat-card">
      <div class="feat-ic fi-1"><i class="fas fa-file-shield"></i></div>
      <div class="feat-h">Dokumentasi SOP Digital</div>
      <p class="feat-p">Kelola seluruh dokumen SOP secara digital dengan sistem versioning dan approval workflow yang terstruktur dan aman.</p>
    </div>
    <div class="feat-card">
      <div class="feat-ic fi-2"><i class="fas fa-arrows-rotate"></i></div>
      <div class="feat-h">Approval Workflow</div>
      <p class="feat-p">Proses persetujuan dokumen multi-level yang cepat, transparan, dan dapat dilacak secara real-time oleh seluruh tim.</p>
    </div>
    <div class="feat-card">
      <div class="feat-ic fi-3"><i class="fas fa-chart-line"></i></div>
      <div class="feat-h">Monitoring & Analitik</div>
      <p class="feat-p">Dashboard analitik komprehensif untuk memantau tingkat kepatuhan dan efektivitas implementasi SOP di seluruh divisi.</p>
    </div>
    <div class="feat-card">
      <div class="feat-ic fi-4"><i class="fas fa-users-gear"></i></div>
      <div class="feat-h">Manajemen Pengguna</div>
      <p class="feat-p">Kontrol akses berbasis peran (RBAC) untuk memastikan keamanan, privasi, dan integritas dokumen perusahaan.</p>
    </div>
  </div>
</section>

<!-- ═══════════════ FOOTER ═══════════════ -->
<footer class="footer">
  <p>&copy; <?php echo date('Y'); ?> <span>PT. Sinergi Nusantara Integrasi</span> — SOP Digital System</p>
  <p style="margin-top:5px;font-size:11px">Developed</i> by <span>Rahul Candra</span></p>
</footer>

<script>
/* ── Cursor glow ── */
const glow = document.getElementById('cursorGlow');
document.addEventListener('mousemove', e => {
  glow.style.left = e.clientX + 'px';
  glow.style.top  = e.clientY + 'px';
});

/* ── Navbar scroll ── */
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
  navbar.classList.toggle('scrolled', window.scrollY > 20);
}, { passive: true });
navbar.classList.toggle('scrolled', window.scrollY > 20);

/* ── Theme toggle ── */
const themeBtn  = document.getElementById('themeToggle');
const themeIcon = document.getElementById('themeIcon');
const html      = document.documentElement;

const saved = localStorage.getItem('sni-theme-v2');
if (saved === 'light') {
  html.setAttribute('data-theme', 'light');
  themeIcon.classList.replace('fa-moon', 'fa-sun');
}

themeBtn.addEventListener('click', () => {
  const isLight = html.getAttribute('data-theme') === 'light';
  if (isLight) {
    html.removeAttribute('data-theme');
    themeIcon.classList.replace('fa-sun', 'fa-moon');
    localStorage.setItem('sni-theme-v2', 'dark');
  } else {
    html.setAttribute('data-theme', 'light');
    themeIcon.classList.replace('fa-moon', 'fa-sun');
    localStorage.setItem('sni-theme-v2', 'light');
  }
});

/* ── Smooth scroll ── */
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    e.preventDefault();
    const t = document.querySelector(a.getAttribute('href'));
    if (t) t.scrollIntoView({ behavior: 'smooth' });
  });
});

/* ── Intersection observer: feat cards ── */
const io = new IntersectionObserver(entries => {
  entries.forEach((en, i) => {
    if (en.isIntersecting) {
      setTimeout(() => en.target.classList.add('visible'), i * 100);
      io.unobserve(en.target);
    }
  });
}, { threshold: 0.12 });

document.querySelectorAll('.feat-card').forEach((c, i) => {
  c.style.transitionDelay = (i * 0.08) + 's';
  io.observe(c);
});

/* ── Bar chart animated entrance ── */
const bars = document.querySelectorAll('.bar');
const barIO = new IntersectionObserver(entries => {
  if (entries[0].isIntersecting) {
    bars.forEach((b, i) => {
      const target = b.style.height;
      b.style.height = '0%';
      setTimeout(() => {
        b.style.transition = 'height 0.7s cubic-bezier(0.23,1,0.32,1)';
        b.style.height = target;
      }, 100 + i * 60);
    });
    barIO.disconnect();
  }
}, { threshold: 0.2 });
if (bars[0]) barIO.observe(bars[0].closest('.dash-card'));

/* ── Stats count-up ── */
function countUp(el, target, suffix, duration) {
  let start = 0;
  const step = target / (duration / 16);
  const timer = setInterval(() => {
    start = Math.min(start + step, target);
    el.querySelector('em').previousSibling.textContent = Math.floor(start);
    if (start >= target) clearInterval(timer);
  }, 16);
}

const statsIO = new IntersectionObserver(entries => {
  if (entries[0].isIntersecting) {
    document.querySelectorAll('.stat-item').forEach(item => {
      const numEl = item.querySelector('.stat-n');
      const text  = numEl.textContent;
      const match = text.match(/(\d+)/);
      if (!match) return;
      const val  = parseInt(match[1]);
      numEl.querySelector('em').previousSibling.textContent = '0';
      countUp(numEl, val, '', 1200);
    });
    statsIO.disconnect();
  }
}, { threshold: 0.5 });

const statsEl = document.querySelector('.hero-stats');
if (statsEl) statsIO.observe(statsEl);
</script>
</body>
</html>