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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== VARIABEL DARI SISTEM ===== */
        :root {
            --bg: #020617;
            --bg2: #0f172a;
            --surface: rgba(15, 23, 42, 0.95);
            --surface2: rgba(30, 41, 59, 0.8);
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --primary-light: #60a5fa;
            --accent: #8b5cf6;
            --gold: #f59e0b;
            --gold-light: #fbbf24;
            --green: #10b981;
            --red: #ef4444;
            --text: #f8fafc;
            --text-muted: #94a3b8;
            --text-sub: #cbd5e1;
            --border: rgba(255, 255, 255, 0.08);
            --border-light: rgba(255, 255, 255, 0.12);
            --glow: rgba(59, 130, 246, 0.3);
            --radius: 16px;
            --radius-sm: 10px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        [data-theme="light"] {
            --bg: #f0f4f8;
            --bg2: #e2e8f0;
            --surface: rgba(255, 255, 255, 0.98);
            --surface2: rgba(255, 255, 255, 0.95);
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #3b82f6;
            --text: #0f172a;
            --text-muted: #475569;
            --text-sub: #334155;
            --border: rgba(0, 0, 0, 0.08);
            --border-light: rgba(0, 0, 0, 0.12);
            --glow: rgba(37, 99, 235, 0.2);
        }

        /* ===== RESET & BASE ===== */
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            overflow-x: hidden;
            transition: background-color 0.3s, color 0.3s;
            min-height: 100vh;
            position: relative;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at 20% 30%, rgba(59,130,246,0.08), transparent 50%);
            pointer-events: none;
            z-index: -1;
        }

        /* ===== BACKGROUND CANVAS ===== */
        #bg-canvas {
            position: fixed;
            inset: 0;
            z-index: -1;
            overflow: hidden;
        }
        .noise {
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
            background-size: 128px 128px;
            opacity: 0.025;
            mix-blend-mode: overlay;
        }
        .mesh {
            position: absolute;
            inset: 0;
            background: 
                radial-gradient(ellipse 70% 60% at 15% 30%, rgba(59,130,246,0.15) 0%, transparent 55%),
                radial-gradient(ellipse 50% 70% at 85% 75%, rgba(245,158,11,0.07) 0%, transparent 50%),
                radial-gradient(ellipse 55% 45% at 65% 5%, rgba(139,92,246,0.1) 0%, transparent 55%);
        }
        .grid-lines {
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(var(--border) 1px, transparent 1px),
                linear-gradient(90deg, var(--border) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(ellipse 65% 65% at 50% 40%, black 20%, transparent 80%);
            -webkit-mask-image: radial-gradient(ellipse 65% 65% at 50% 40%, black 20%, transparent 80%);
            animation: gridShift 50s linear infinite;
        }
        @keyframes gridShift {
            from { background-position: 0 0, 0 0; }
            to { background-position: 60px 60px, 60px 60px; }
        }
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            will-change: transform;
        }
        .orb-1 {
            width: 700px; height: 700px;
            background: rgba(59,130,246,0.1);
            top: -15%; left: -10%;
            animation: drift1 30s ease-in-out infinite alternate;
        }
        .orb-2 {
            width: 500px; height: 500px;
            background: rgba(245,158,11,0.06);
            bottom: -10%; right: -5%;
            animation: drift2 25s ease-in-out infinite alternate;
        }
        .orb-3 {
            width: 350px; height: 350px;
            background: rgba(139,92,246,0.08);
            top: 35%; right: 18%;
            animation: drift3 20s ease-in-out infinite alternate;
        }
        @keyframes drift1 { to { transform: translate(60px, 40px) scale(1.1); } }
        @keyframes drift2 { to { transform: translate(-40px, -30px) scale(1.08); } }
        @keyframes drift3 { to { transform: translate(30px, -25px) scale(1.05); } }

        /* ===== CURSOR GLOW ===== */
        .cursor-glow {
            position: fixed;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--glow) 0%, transparent 60%);
            pointer-events: none;
            z-index: 9999;
            transform: translate(-50%, -50%);
            transition: transform 0.1s ease;
        }

        /* ===== NAVBAR ===== */
        .navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 72px;
            padding: 0 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 100;
            transition: var(--transition);
            background: transparent;
        }
        .navbar.scrolled {
            background: var(--surface);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
        }
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .nav-logo-img {
            height: 40px;
            width: auto;
            filter: drop-shadow(0 0 8px var(--glow));
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
            list-style: none;
        }
        .nav-links a {
            text-decoration: none;
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        /* Hover effect dihilangkan, hanya cursor pointer */
        .nav-links a:hover {
            cursor: pointer;
        }
        .nav-links a.active {
            color: var(--primary-light);
        }
        .nav-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .search-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 40px;
            padding: 8px 16px;
            transition: var(--transition);
        }
        .search-pill input {
            background: none;
            border: none;
            outline: none;
            color: var(--text);
            font-family: 'Outfit', sans-serif;
            font-size: 13px;
            width: 160px;
        }
        .search-pill input::placeholder { color: var(--text-muted); }
        .search-pill i { color: var(--text-muted); }
        .search-pill:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }
        .icon-btn {
            width: 38px; height: 38px;
            border-radius: var(--radius-sm);
            background: var(--surface2);
            border: 1px solid var(--border);
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 16px;
            transition: var(--transition);
        }
        /* Hover icon-btn dihilangkan */
        .icon-btn:hover {
            cursor: pointer;
        }
        .btn-cta-nav {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 22px;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: white;
            border-radius: 40px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(37,99,235,0.3);
        }
        /* Hover btn-cta-nav dihilangkan */
        .btn-cta-nav:hover {
            cursor: pointer;
        }

        /* ===== HERO SECTION ===== */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 100px 5% 50px;
            position: relative;
            z-index: 10;
        }
        .hero-inner {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 48px;
            align-items: center;
        }
        .hero-left { max-width: 600px; }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(59,130,246,0.1);
            border: 1px solid rgba(59,130,246,0.2);
            border-radius: 40px;
            padding: 6px 16px 6px 12px;
            font-size: 12px;
            font-weight: 500;
            color: var(--primary-light);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 24px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeUp 0.6s ease 0.2s forwards;
        }
        .pulse-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--primary);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.3);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%,100% { box-shadow: 0 0 0 3px rgba(59,130,246,0.3); }
            50% { box-shadow: 0 0 0 6px rgba(59,130,246,0); }
        }

        .hero-h1 {
            font-size: clamp(48px, 8vw, 88px);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -2px;
            margin-bottom: 24px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeUp 0.6s ease 0.3s forwards;
        }
        .h1-line1 { color: var(--text); }
        .h1-line2 {
            background: linear-gradient(135deg, var(--primary), var(--accent), var(--gold));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            position: relative;
        }
        .h1-line2::after {
            content: attr(data-text);
            position: absolute;
            left: 0; top: 0;
            z-index: -1;
            filter: blur(20px);
            opacity: 0.5;
        }

        .hero-p {
            font-size: 16px;
            line-height: 1.8;
            color: var(--text-muted);
            margin-bottom: 32px;
            max-width: 520px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeUp 0.6s ease 0.4s forwards;
        }
        .hero-p strong { color: var(--text); }

        .hero-actions {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 40px;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeUp 0.6s ease 0.5s forwards;
        }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 32px;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: white;
            border-radius: var(--radius);
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: var(--transition);
            box-shadow: 0 8px 20px rgba(37,99,235,0.3);
        }
        /* Hover btn-primary dihilangkan */
        .btn-primary:hover {
            cursor: pointer;
        }
        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 28px;
            background: var(--surface2);
            border: 1px solid var(--border);
            color: var(--text);
            border-radius: var(--radius);
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            transition: var(--transition);
        }
        /* Hover btn-ghost dihilangkan */
        .btn-ghost:hover {
            cursor: pointer;
        }

        .hero-stats {
            display: flex;
            gap: 0;
            padding-top: 28px;
            border-top: 1px solid var(--border);
            opacity: 0;
            transform: translateY(20px);
            animation: fadeUp 0.6s ease 0.6s forwards;
        }
        .stat-item {
            flex: 1;
            padding-right: 24px;
            position: relative;
        }
        .stat-item:not(:last-child)::after {
            content: '';
            position: absolute;
            right: 0; top: 0; bottom: 0;
            width: 1px;
            background: var(--border);
        }
        .stat-item:not(:first-child) { padding-left: 24px; }
        .stat-n {
            font-size: 32px;
            font-weight: 700;
            color: var(--text);
            line-height: 1;
        }
        .stat-n em {
            color: var(--primary-light);
            font-style: normal;
        }
        .stat-l {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* ===== HERO VISUAL (DASHBOARD MOCKUP) ===== */
        .hero-visual {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            opacity: 0;
            transform: translateX(30px);
            animation: slideLeft 0.8s ease 0.35s forwards;
        }
        @keyframes slideLeft { to { opacity: 1; transform: translateX(0); } }
        @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }

        .vis-glow {
            position: absolute;
            width: 70%; height: 70%;
            background: radial-gradient(circle, rgba(59,130,246,0.2) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(60px);
        }
        .dashboard-wrap {
            position: relative;
            width: 100%;
            max-width: 580px;
            animation: float 8s ease-in-out infinite;
        }
        @keyframes float {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }

        .dash-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 24px;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
        }

        .dash-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
        }
        .dash-dots { display: flex; gap: 6px; }
        .dash-dot { width: 10px; height: 10px; border-radius: 50%; }
        .dash-dot-r { background: #ef4444; }
        .dash-dot-y { background: #f59e0b; }
        .dash-dot-g { background: #10b981; }
        .dash-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
        }
        .dash-badge {
            background: rgba(16,185,129,0.15);
            color: #10b981;
            border: 1px solid rgba(16,185,129,0.3);
            border-radius: 20px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 600;
        }

        .dash-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        .dash-stat-cell {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 16px;
            transition: var(--transition);
        }
        /* Hover dash-stat-cell dihilangkan */
        .dash-stat-cell:hover {
            cursor: default;
        }
        .ds-num {
            font-size: 28px;
            font-weight: 700;
            color: var(--text);
        }
        .ds-label {
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .ds-trend {
            font-size: 11px;
            font-weight: 600;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .ds-up { color: #10b981; }
        .ds-down { color: #ef4444; }

        .dash-chart-wrap { margin-bottom: 20px; }
        .chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .chart-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text);
        }
        .chart-period {
            font-size: 11px;
            color: var(--text-muted);
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 3px 10px;
        }
        .chart-bars {
            display: flex;
            align-items: flex-end;
            gap: 6px;
            height: 80px;
        }
        .bar {
            flex: 1;
            border-radius: 4px 4px 0 0;
            background: linear-gradient(0deg, var(--primary-dark), var(--primary));
            min-height: 8px;
            transition: height 0.5s ease;
        }
        .bar.highlight {
            background: linear-gradient(0deg, var(--accent), var(--primary-light));
            box-shadow: 0 0 10px var(--glow);
        }
        /* Hover bar dihilangkan */

        .dash-table {
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
        }
        .dt-head {
            display: grid;
            grid-template-columns: 2fr 1.2fr 1fr;
            padding: 10px 16px;
            background: var(--surface2);
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .dt-row {
            display: grid;
            grid-template-columns: 2fr 1.2fr 1fr;
            padding: 12px 16px;
            border-top: 1px solid var(--border);
            font-size: 13px;
            transition: background 0.2s;
        }
        /* Hover dt-row dihilangkan */
        .dt-row:hover {
            cursor: default;
        }
        .dt-name {
            font-weight: 500;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .dt-dept { color: var(--text-muted); font-size: 12px; }
        .pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 20px;
            padding: 3px 10px;
        }
        .pill-green {
            background: rgba(16,185,129,0.1);
            color: #10b981;
            border: 1px solid rgba(16,185,129,0.2);
        }
        .pill-yellow {
            background: rgba(245,158,11,0.1);
            color: #f59e0b;
            border: 1px solid rgba(245,158,11,0.2);
        }
        .pill-blue {
            background: rgba(59,130,246,0.1);
            color: var(--primary-light);
            border: 1px solid rgba(59,130,246,0.2);
        }

        /* ===== FLOATING WIDGETS ===== */
        .widget {
            position: absolute;
            background: var(--surface);
            border: 1px solid var(--border-light);
            border-radius: 16px;
            backdrop-filter: blur(16px);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            z-index: 20;
        }
        .widget-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }
        .wi-green { background: rgba(16,185,129,0.15); color: #10b981; }
        .wi-blue { background: rgba(59,130,246,0.15); color: var(--primary-light); }
        .wi-gold { background: rgba(245,158,11,0.15); color: #f59e0b; }
        .widget-text .wt-label {
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .widget-text .wt-val {
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
        }
        .widget-1 { top: -20px; right: 20px; animation: wfloat 7s ease-in-out infinite; }
        .widget-2 { bottom: 40px; left: -10px; animation: wfloat 9s ease-in-out infinite; }
        .widget-3 { top: 40%; right: -10px; animation: wfloat 8s ease-in-out infinite; }
        @keyframes wfloat {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        /* ===== FEATURES SECTION ===== */
        .features {
            padding: 60px 5% 100px;
            position: relative;
            z-index: 10;
        }
        .section-eyebrow {
            text-align: center;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--primary-light);
            margin-bottom: 12px;
        }
        .section-title {
            text-align: center;
            font-size: clamp(28px, 4vw, 36px);
            font-weight: 700;
            color: var(--text);
            margin-bottom: 48px;
        }
        .section-title span {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .feat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }
        .feat-card {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 28px 22px;
            transition: var(--transition);
            opacity: 0;
            transform: translateY(20px);
        }
        .feat-card.visible {
            opacity: 1;
            transform: translateY(0);
        }
        /* Hover feat-card dihilangkan */
        .feat-card:hover {
            cursor: default;
        }
        .feat-ic {
            width: 48px; height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 18px;
        }
        .fi-1 { background: rgba(59,130,246,0.15); color: var(--primary-light); }
        .fi-2 { background: rgba(16,185,129,0.15); color: #10b981; }
        .fi-3 { background: rgba(245,158,11,0.15); color: #f59e0b; }
        .fi-4 { background: rgba(139,92,246,0.15); color: #a78bfa; }
        .feat-h {
            font-size: 16px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }
        .feat-p {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* ===== FOOTER ===== */
        .footer {
            padding: 24px 5%;
            border-top: 1px solid var(--border);
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
        }
        .footer span { color: var(--primary-light); }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1100px) {
            .hero-inner { grid-template-columns: 1fr; }
            .hero-visual { order: -1; max-width: 540px; margin: 0 auto; }
            .feat-grid { grid-template-columns: repeat(2, 1fr); }
            .nav-links, .search-pill { display: none; }
            .widget-1, .widget-2, .widget-3 { display: none; }
        }
        @media (max-width: 640px) {
            .hero-h1 { font-size: 42px; }
            .hero-actions { flex-direction: column; }
            .btn-primary, .btn-ghost { justify-content: center; }
            .feat-grid { grid-template-columns: 1fr; }
            .stat-item:not(:first-child) { padding-left: 16px; }
            .stat-item:not(:last-child)::after { display: none; }
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: var(--bg2); }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 4px; }
    </style>
</head>
<body>

<!-- Background Canvas -->
<div id="bg-canvas">
    <div class="noise"></div>
    <div class="mesh"></div>
    <div class="grid-lines"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
</div>
<div class="cursor-glow" id="cursorGlow"></div>

<!-- Navbar -->
<nav class="navbar" id="navbar">
    <a href="index.php" class="nav-brand">
        <img src="assets/images/logo.png" alt="SNI" class="nav-logo-img" onerror="this.style.display='none'">
    </a>
    <ul class="nav-links">
        <li><a href="#home" class="active"><i class="fas fa-house-chimney"></i> Beranda</a></li>
        <li><a href="#features"><i class="fas fa-th-large"></i> Fitur</a></li>
        <li><a href="#about"><i class="fas fa-circle-info"></i> Tentang</a></li>
    </ul>
    <div class="nav-right">
        <div class="search-pill">
            <i class="fas fa-magnifying-glass"></i>
            <input type="text" placeholder="Cari SOP...">
        </div>
        <button class="icon-btn" id="themeToggle" title="Ganti Tema">
            <i class="far fa-moon" id="themeIcon"></i>
        </button>
        <a href="login.php" class="btn-cta-nav">
            <i class="fas fa-arrow-right-to-bracket"></i> Masuk
        </a>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero" id="home">
    <div class="hero-inner">
        <div class="hero-left">
            <div class="eyebrow">
                <span class="pulse-dot"></span>
                Sistem Dokumen Digital & Terintegrasi
            </div>
            <h1 class="hero-h1">
                <span class="h1-line1">SINERGI NUSANTARA</span>
                <span class="h1-line2" data-text="INTEGRASI">INTEGRASI</span>
            </h1>
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

        <div class="hero-visual" id="about">
            <div class="vis-glow"></div>
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
            <div class="dashboard-wrap">
                <div class="dash-card">
                    <div class="dash-topbar">
                        <div class="dash-dots">
                            <span class="dash-dot dash-dot-r"></span>
                            <span class="dash-dot dash-dot-y"></span>
                            <span class="dash-dot dash-dot-g"></span>
                        </div>
                        <span class="dash-title">SOP Digital Dashboard</span>
                        <span class="dash-badge"><i class="fas fa-circle" style="font-size:7px;margin-right:4px"></i>Live</span>
                    </div>
                    <div class="dash-stats">
                        <div class="dash-stat-cell">
                            <div class="ds-num">50</div>
                            <div class="ds-label">Total SOP</div>
                            <div class="ds-trend ds-up"><i class="fas fa-arrow-trend-up"></i> +8%</div>
                        </div>
                        <div class="dash-stat-cell">
                            <div class="ds-num">4</div>
                            <div class="ds-label">Kategori</div>
                            <div class="ds-trend ds-up"><i class="fas fa-arrow-trend-up"></i> +4%</div>
                        </div>
                        <div class="dash-stat-cell">
                            <div class="ds-num">99%</div>
                            <div class="ds-label">Aktif</div>
                            <div class="ds-trend ds-up"><i class="fas fa-arrow-trend-up"></i> +1%</div>
                        </div>
                    </div>
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

<!-- Features Section -->
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

<!-- Footer -->
<footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> <span>PT. Sinergi Nusantara Integrasi</span> — SOP Digital System</p>
    <p style="margin-top:5px;font-size:11px">Developed by <span>Rahul Candra</span></p>
</footer>

<script>
    // Cursor glow
    const glow = document.getElementById('cursorGlow');
    document.addEventListener('mousemove', e => {
        glow.style.left = e.clientX + 'px';
        glow.style.top  = e.clientY + 'px';
    });

    // Navbar scroll effect
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 20);
    }, { passive: true });
    navbar.classList.toggle('scrolled', window.scrollY > 20);

    // Theme toggle (sinkron dengan sistem)
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

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            e.preventDefault();
            const t = document.querySelector(a.getAttribute('href'));
            if (t) t.scrollIntoView({ behavior: 'smooth' });
        });
    });

    // Intersection Observer untuk feature cards
    const io = new IntersectionObserver(entries => {
        entries.forEach((en, i) => {
            if (en.isIntersecting) {
                setTimeout(() => en.target.classList.add('visible'), i * 100);
                io.unobserve(en.target);
            }
        });
    }, { threshold: 0.15 });

    document.querySelectorAll('.feat-card').forEach((c, i) => {
        c.style.transitionDelay = (i * 0.1) + 's';
        io.observe(c);
    });

    // Bar chart animation
    const bars = document.querySelectorAll('.bar');
    const barIO = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting) {
            bars.forEach((b, i) => {
                const target = b.style.height;
                b.style.height = '0%';
                setTimeout(() => {
                    b.style.transition = 'height 0.7s cubic-bezier(0.4, 0, 0.2, 1)';
                    b.style.height = target;
                }, 100 + i * 60);
            });
            barIO.disconnect();
        }
    }, { threshold: 0.2 });
    if (bars[0]) barIO.observe(bars[0].closest('.dash-card'));

    // Stats count-up
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