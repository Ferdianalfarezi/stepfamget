<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>STEPFAMGET - @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logostep31.png') }}">
    <style>
        :root {
            --sb-w:       228px;
            --sb-w-col:   58px;
            --sb-gap:     12px;
            --topbar-h:   52px;
            --bg:         #f0f3f0;
            --white:      #ffffff;
            --border:     rgba(0,0,0,0.07);
            --text:       #111111;
            --muted:      #7a7a7a;
            --green:      #0b4614;
            --green-lt:   #eaf3eb;
            --radius:     14px;
            --sb-radius:  18px;
            --font:       'DM Sans', sans-serif;
            --glass-bg:   rgba(255, 255, 255, 0.365);
            --glass-blur: blur(22px) saturate(1.5);
            --glass-bdr:  0.5px solid rgba(255,255,255,0.92);
            --shadow-sm:  0 2px 12px rgba(0,0,0,0.05);
            --shadow-sb:  0 4px 32px rgba(0,0,0,0.08), 0 1px 4px rgba(0,0,0,0.04);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            height: 100%;
            font-family: var(--font);
            background: var(--bg);
            color: var(--text);
        }

        body {
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── BG AMBIENCE ── */
        body::before {
            content: '';
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
            background:
                radial-gradient(ellipse at 15% 15%, rgba(58,125,68,0.07) 0%, transparent 55%),
                radial-gradient(ellipse at 85% 85%, rgba(58,125,68,0.05) 0%, transparent 50%);
        }

        /* ══════════════════════════════════════
           SIDEBAR
        ══════════════════════════════════════ */
        .sidebar {
            position: fixed;
            top: var(--sb-gap);
            left: var(--sb-gap);
            bottom: var(--sb-gap);
            width: var(--sb-w);
            background: var(--glass-bg);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border: var(--glass-bdr);
            border-radius: var(--sb-radius);
            box-shadow: var(--shadow-sb);
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: width 0.32s cubic-bezier(.4,0,.2,1);
            overflow: hidden;
            background: linear-gradient(
            to bottom,
                var(--glass-bg) 60%,
                rgba(11, 70, 20, 0.18) 100%
            );
        }

        .sidebar.collapsed { width: var(--sb-w-col); }

        /* Brand */
        .sb-brand {
            padding: 16px 14px 13px;
            display: flex; align-items: center; gap: 10px;
            border-bottom: 0.5px solid var(--border);
            flex-shrink: 0; overflow: hidden;
        }
        .sb-brand-icon {
            width: 30px; height: 30px; flex-shrink: 0;
            background: var(--green); border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 12px;
        }
        .sb-brand-text { overflow: hidden; white-space: nowrap; }
        .sb-brand-name { font-size: 13px; font-weight: 700; color: var(--text); }
        .sb-brand-sub  { font-size: 9px; color: var(--muted); letter-spacing: 1px; text-transform: uppercase; margin-top: 1px; }

        /* Toggle button */
        .sb-toggle {
            position: absolute; top: 16px; right: 10px;
            width: 22px; height: 22px; border-radius: 7px;
            background: var(--bg); border: 0.5px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: var(--muted); font-size: 11px;
            transition: all .18s; flex-shrink: 0; z-index: 2;
        }
        .sb-toggle:hover { background: var(--green-lt); color: var(--green); }
        .sidebar.collapsed .sb-toggle { right: 9px; top: 14px; }

        /* Section label */
        .sb-section-label {
            font-size: 9px; font-weight: 600; color: var(--muted);
            text-transform: uppercase; letter-spacing: 1.2px;
            padding: 13px 15px 5px;
            white-space: nowrap; overflow: hidden;
            transition: opacity .2s;
        }
        .sidebar.collapsed .sb-section-label { opacity: 0; }

        /* Nav wrap (for active curve effect) */
        .sb-nav-wrap { position: relative; }

        /* Nav item */
        .sb-nav {
            display: flex; align-items: center; gap: 10px;
            padding: 8.5px 10px 8.5px 13px;
            margin: 1px 8px;
            border-radius: 10px;
            cursor: pointer; text-decoration: none;
            color: var(--muted);
            font-size: 12.5px; font-weight: 500;
            white-space: nowrap; overflow: hidden;
            transition: background .15s, color .15s;
        }
        .sb-nav i { font-size: 14px; flex-shrink: 0; width: 17px; text-align: center; }
        .sb-nav:hover { background: rgba(58,125,68,0.08); color: var(--green); }
        .sb-nav:hover i { color: var(--green); }

        /* Active state */
        .sb-nav-wrap.active .sb-nav {
            background: var(--green);
            color: #fff;
            font-weight: 600;
        }
        .sb-nav-wrap.active .sb-nav i { color: #fff; }

        /* Nav badge */
        .sb-badge {
            margin-left: auto; flex-shrink: 0;
            background: rgba(0,0,0,0.07); color: var(--muted);
            font-size: 9.5px; font-weight: 600;
            padding: 1px 7px; border-radius: 10px;
            transition: opacity .2s;
        }
        .sb-nav-wrap.active .sb-nav .sb-badge { background: rgba(255,255,255,0.22); color: rgba(255,255,255,0.9); }
        .sidebar.collapsed .sb-badge { opacity: 0; width: 0; padding: 0; }

        /* Nav label text */
        .sb-nav-label { transition: opacity .2s; }
        .sidebar.collapsed .sb-nav-label { opacity: 0; width: 0; }

        /* Dot indicator for active (shown only when NOT collapsed) */
        .sb-dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: #fff; flex-shrink: 0;
            box-shadow: 0 0 6px rgba(255,255,255,0.6);
            display: none;
        }
        .sb-nav-wrap.active .sb-nav .sb-dot { display: block; }

        /* Service block */
        .sb-service-block {
            margin: 2px 8px;
            background: rgba(0,0,0,0.025);
            border: 0.5px solid var(--border);
            border-radius: 10px; overflow: hidden;
        }
        .sb-svc-item {
            display: flex; align-items: center; gap: 9px;
            padding: 7px 13px;
            cursor: pointer;
            font-size: 12px; color: #555;
            white-space: nowrap; overflow: hidden;
            transition: background .15s;
            text-decoration: none;
        }
        .sb-svc-item:hover { background: rgba(0,0,0,0.04); }
        .sb-svc-dot {
            width: 22px; height: 22px; border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 11px;
        }
        .sb-svc-label { transition: opacity .2s; }
        .sidebar.collapsed .sb-svc-label { opacity: 0; width: 0; }

        /* Footer */
        .sb-footer {
            margin-top: auto; padding: 12px 14px;
            border-top: 0.5px solid var(--border);
            flex-shrink: 0;
        }
        .sb-footer-label {
            font-size: 9px; font-weight: 600; color: var(--muted);
            text-transform: uppercase; letter-spacing: 1.2px;
            margin-bottom: 9px;
            transition: opacity .2s;
        }
        .sidebar.collapsed .sb-footer-label { opacity: 0; height: 0; margin: 0; overflow: hidden; }

        .sb-user { display: flex; align-items: center; gap: 9px; overflow: hidden; }
        .sb-avatar {
            width: 30px; height: 30px; border-radius: 50%;
            background: var(--green);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: 11px; flex-shrink: 0;
        }
        .sb-uinfo { overflow: hidden; flex: 1; transition: opacity .2s, width .2s; }
        .sb-uname { font-size: 12px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sb-uid   { font-size: 10px; color: var(--muted); }
        .sidebar.collapsed .sb-uinfo { opacity: 0; width: 0; }

        .sb-logout {
            flex-shrink: 0;
            width: 26px; height: 26px; border-radius: 8px;
            background: none; border: 0.5px solid var(--border);
            color: var(--muted); cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; transition: .15s;
        }
        .sb-logout:hover { background: #fee2e2; color: #dc2626; border-color: #fca5a5; }
        .sidebar.collapsed .sb-logout { display: none; }

        /* ══════════════════════════════════════
           MAIN AREA
        ══════════════════════════════════════ */
        .main {
            margin-left: calc(var(--sb-w) + var(--sb-gap) * 2);
            flex: 1;
            display: flex; flex-direction: column;
            min-height: 100vh;
            padding: var(--sb-gap) var(--sb-gap) var(--sb-gap) 0;
            transition: margin-left 0.32s cubic-bezier(.4,0,.2,1);
            position: relative; z-index: 1;
        }
        .main.sb-collapsed {
            margin-left: calc(var(--sb-w-col) + var(--sb-gap) * 2);
        }

        /* ── TOPBAR ── */
        .topbar {
            background: var(--glass-bg);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border: var(--glass-bdr);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            height: var(--topbar-h);
            display: flex; align-items: center;
            padding: 0 16px; gap: 10px;
            margin-bottom: 10px;
            flex-shrink: 0;
        }
        .topbar-title { font-size: 15px; font-weight: 700; flex: 1; }

        .topbar-search {
            display: flex; align-items: center; gap: 7px;
            background: var(--bg); border: 0.5px solid var(--border);
            border-radius: 9px; padding: 0 11px; height: 32px;
            font-size: 12px; color: var(--muted);
        }
        .topbar-search i { font-size: 12px; }

        .topbar-btn {
            width: 32px; height: 32px; border-radius: 9px;
            background: var(--bg); border: 0.5px solid var(--border);
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            color: var(--muted); transition: .15s;
        }
        .topbar-btn:hover { background: var(--green-lt); color: var(--green); border-color: #c8e6ca; }
        .topbar-btn i { font-size: 13px; }

        .topbar-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: var(--green);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: 11px; cursor: pointer;
        }

        /* ── PAGE CONTENT ── */
        .page-content { flex: 1; display: flex; flex-direction: column; gap: 10px; }

        /* ── CARD ── */
        .card {
            background: var(--glass-bg);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border: var(--glass-bdr);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
        }
        .card-header {
            padding: 13px 16px;
            border-bottom: 0.5px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title {
            font-size: 12.5px; font-weight: 600;
            display: flex; align-items: center; gap: 7px;
        }
        .card-title i { color: var(--green); font-size: 13px; }
        .card-body { padding: 16px; }

        /* ── BUTTONS ── */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 9px;
            font-size: 12.5px; font-weight: 500;
            cursor: pointer; border: none; transition: all .18s;
            text-decoration: none; font-family: var(--font);
        }
        .btn-dark    { background: #1a3320; color: #fff; }
        .btn-dark:hover { background: #2a5233; }
        .btn-outline {
            background: var(--white); color: var(--text);
            border: 0.5px solid var(--border);
        }
        .btn-outline:hover { background: var(--bg); }
        .btn-green   { background: var(--green); color: #fff; }
        .btn-green:hover { background: #2e6b38; }
        .btn-sm      { padding: 5px 12px; font-size: 11.5px; }

        /* ── STATS ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 10px;
        }
        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border: var(--glass-bdr);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            padding: 15px 16px;
            position: relative; overflow: hidden;
        }
        .stat-accent {
            position: absolute; top: 0; right: 0;
            width: 64px; height: 64px;
            border-radius: 0 14px 0 64px;
            opacity: 0.07;
        }
        .stat-sublabel { font-size: 10px; color: var(--muted); font-weight: 500; text-transform: uppercase; letter-spacing: .6px; margin-bottom: 5px; }
        .stat-value    { font-size: 24px; font-weight: 700; color: var(--text); line-height: 1.1; }
        .stat-value sup { font-size: 11px; font-weight: 400; color: var(--muted); }
        .stat-trend    { font-size: 10.5px; margin-top: 4px; display: flex; align-items: center; gap: 3px; color: var(--muted); }
        .stat-trend.up   { color: #2a7d32; }
        .stat-trend.down { color: #c62828; }
        .mini-bars {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            display: flex; align-items: flex-end; gap: 2.5px; height: 28px;
        }
        .mini-bar   { width: 4px; border-radius: 2px; background: rgba(0,0,0,0.06); }
        .mini-bar.g { background: var(--green); opacity: 0.5; }
        .mini-bar.r { background: #ef5350; opacity: 0.5; }

        /* ── TABLE ── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            background: rgba(240,243,240,0.6);
            font-size: 9.5px; font-weight: 600;
            text-transform: uppercase; letter-spacing: .7px; color: var(--muted);
            padding: 10px 14px; text-align: left;
            border-bottom: 0.5px solid var(--border);
            white-space: nowrap;
        }
        tbody td {
            padding: 10px 14px; font-size: 12.5px;
            border-bottom: 0.5px solid var(--border);
            vertical-align: middle;
        }
        tbody tr:hover td { background: rgba(58,125,68,0.03); }
        tbody tr:last-child td { border-bottom: none; }

        /* ── BADGES ── */
        .badge {
            display: inline-flex; align-items: center; gap: 3px;
            padding: 2px 9px; border-radius: 20px;
            font-size: 10.5px; font-weight: 500;
        }
        .badge-success { background: #e8f5e9; color: #2e7d32; }
        .badge-danger  { background: #ffebee; color: #c62828; }
        .badge-primary { background: #e3f2fd; color: #1565c0; }
        .badge-gray    { background: #f5f5f5; color: #666; }
        .badge-warning { background: #fff3e0; color: #e65100; }

        /* ── ACTION BUTTONS ── */
        .action-btn {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 11px; border-radius: 7px;
            font-size: 11.5px; font-weight: 500;
            cursor: pointer; border: 0.5px solid var(--border);
            background: var(--white); color: var(--text);
            transition: all .15s; font-family: var(--font);
        }
        .action-btn:hover         { background: #1a3320; color: #fff; border-color: #1a3320; }
        .action-btn-warning       { background: #fff8e1; color: #f57f17; border-color: #ffe082; }
        .action-btn-warning:hover { background: #ffe082; color: #e65100; }
        .action-btn-danger        { background: #ffebee; color: #c62828; border-color: #ffcdd2; }
        .action-btn-danger:hover  { background: #ffcdd2; color: #b71c1c; }

        /* ── FILTERS ── */
        .filters { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
        .form-control {
            height: 36px; padding: 0 12px;
            border: 0.5px solid var(--border); border-radius: 9px;
            font-size: 12.5px; color: var(--text); background: var(--white);
            outline: none; font-family: var(--font); transition: .15s;
        }
        .form-control:focus { border-color: var(--green); box-shadow: 0 0 0 3px rgba(58,125,68,.1); }
        .search-wrap { position: relative; }
        .search-wrap i { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 12px; pointer-events: none; }
        .search-wrap .form-control { padding-left: 32px; }

        /* ── MODAL ── */
        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(10,25,15,.4);
            z-index: 1000; display: none;
            align-items: center; justify-content: center;
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
        }
        .modal-overlay.show { display: flex; }
        .modal-box {
            background: var(--white); border-radius: 18px;
            width: 90%; max-width: 780px; max-height: 90vh;
            display: flex; flex-direction: column;
            box-shadow: 0 24px 64px rgba(0,0,0,0.14);
            animation: modalIn .22s ease;
            border: 0.5px solid rgba(255,255,255,0.9);
        }
        .modal-box-md { max-width: 640px; }
        .modal-box-sm { max-width: 480px; }
        @keyframes modalIn {
            from { opacity:0; transform:scale(.96) translateY(10px); }
            to   { opacity:1; transform:scale(1) translateY(0); }
        }
        .modal-header {
            padding: 18px 22px 14px; border-bottom: 0.5px solid var(--border);
            display: flex; align-items: flex-start; justify-content: space-between;
            flex-shrink: 0;
        }
        .modal-title    { font-size: 15px; font-weight: 700; }
        .modal-subtitle { font-size: 11.5px; color: var(--muted); margin-top: 2px; }
        .modal-close {
            width: 28px; height: 28px; border-radius: 8px;
            background: var(--bg); border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            color: var(--muted); transition: .15s; flex-shrink: 0;
        }
        .modal-close:hover { background: #ffebee; color: #c62828; }
        .modal-body { padding: 18px 22px; overflow-y: auto; flex: 1; }

        /* ── INFO GRID ── */
        .info-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; margin-bottom: 16px; }
        .info-item { background: var(--bg); border-radius: 10px; padding: 11px 13px; }
        .info-item-label { font-size: 9.5px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .7px; margin-bottom: 3px; }
        .info-item-value { font-size: 13px; font-weight: 600; }

        /* ── FORM ── */
        .k-form-grid     { display: grid; grid-template-columns: 1fr 1fr; gap: 13px; margin-bottom: 16px; }
        .k-form-col-2    { grid-column: span 2; }
        .k-form-group    { display: flex; flex-direction: column; gap: 5px; }
        .k-form-label    { font-size: 10.5px; font-weight: 600; color: #475569; text-transform: uppercase; letter-spacing: .6px; }
        .k-form-label .required { color: #ef4444; }
        .k-form-input {
            height: 38px; padding: 0 12px;
            border: 0.5px solid var(--border); border-radius: 9px;
            font-size: 13px; color: var(--text); background: #fff;
            width: 100%; box-sizing: border-box; font-family: var(--font);
            outline: none; transition: .15s;
        }
        .k-form-input:focus    { border-color: var(--green); box-shadow: 0 0 0 3px rgba(58,125,68,.1); }
        .k-form-input.is-invalid { border-color: #ef4444; }
        .k-form-error   { font-size: 11px; color: #ef4444; min-height: 15px; }
        .k-form-actions { display: flex; gap: 10px; justify-content: flex-end; padding-top: 4px; margin-top: 6px; }
        .k-section-label {
            font-size: 10.5px; font-weight: 700; color: var(--green);
            text-transform: uppercase; letter-spacing: .9px; margin-bottom: 10px;
            display: flex; align-items: center; gap: 7px;
        }
        .k-section-label::after { content: ''; flex: 1; height: 1px; background: var(--border); }

        /* ── FAMILY TABLE ── */
        .k-family-wrap   { display: none; margin-bottom: 10px; overflow-x: auto; border: 0.5px solid var(--border); border-radius: 10px; }
        .k-family-table  { width: 100%; border-collapse: collapse; min-width: 680px; }
        .k-fth { padding: 8px 10px; font-size: 10px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .6px; text-align: left; white-space: nowrap; border-bottom: 0.5px solid var(--border); background: rgba(240,243,240,0.6); }
        .k-ftd { padding: 6px 8px; border-bottom: 0.5px solid rgba(0,0,0,0.04); vertical-align: middle; }
        .k-fc  { height: 33px; padding: 0 9px; border: 0.5px solid var(--border); border-radius: 7px; font-size: 12.5px; color: var(--text); width: 100%; box-sizing: border-box; background: #fff; font-family: var(--font); outline: none; transition: .15s; }
        .k-fc:focus      { border-color: var(--green); box-shadow: 0 0 0 3px rgba(58,125,68,.08); }
        .k-fc.is-invalid { border-color: #ef4444; }
        .k-btn-rm { background: #ffebee; color: #c62828; border: none; border-radius: 6px; width: 27px; height: 27px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; font-size: 11px; transition: .15s; }
        .k-btn-rm:hover { background: #ffcdd2; }
        .k-btn-add-family { display: inline-flex; align-items: center; gap: 7px; padding: 7px 14px; font-size: 12.5px; font-weight: 600; color: var(--green); background: var(--green-lt); border: 1px dashed #a5d6a7; border-radius: 8px; cursor: pointer; transition: .15s; margin-bottom: 4px; font-family: var(--font); }
        .k-btn-add-family:hover { background: #c8e6c9; border-color: var(--green); }
        .k-family-empty { text-align: center; padding: 14px 0 6px; color: var(--muted); font-size: 13px; }

        /* ── PAGINATION ── */
        .pagination-wrap { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-top: 0.5px solid var(--border); }
        .pagination-info { font-size: 11.5px; color: var(--muted); }
        .pagination { display: flex; gap: 4px; }
        .page-link { width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 8px; font-size: 12px; text-decoration: none; color: var(--text); transition: .15s; border: 0.5px solid var(--border); background: var(--white); }
        .page-link:hover  { background: var(--green-lt); color: var(--green); border-color: #a5d6a7; }
        .page-link.active { background: #1a3320; color: #fff; border-color: #1a3320; font-weight: 600; }
        .page-link.disabled { opacity: .4; pointer-events: none; }

        /* ── SPINNER / TOAST ── */
        .spinner { width: 28px; height: 28px; border: 2.5px solid var(--border); border-top-color: var(--green); border-radius: 50%; animation: spin .7s linear infinite; margin: 40px auto; display: block; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .toast-container { position: fixed; top: 18px; right: 18px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; pointer-events: none; }
        .toast { padding: 11px 16px; border-radius: 10px; font-size: 12.5px; font-weight: 500; color: #fff; box-shadow: 0 4px 20px rgba(0,0,0,.12); display: flex; align-items: center; gap: 9px; animation: toastIn .22s ease; max-width: 300px; pointer-events: auto; backdrop-filter: blur(8px); }
        .toast-success { background: #2e7d32; }
        .toast-error   { background: #c62828; }
        @keyframes toastIn { from{opacity:0;transform:translateY(-8px) scale(.97);}to{opacity:1;transform:translateY(0) scale(1);} }

        .btn.loading { opacity: .7; pointer-events: none; }

        /* ── HUBUNGAN COLORS ── */
        .hubungan-karyawan  { background: #e3f2fd; color: #1565c0; }
        .hubungan-karyawati { background: #fce4ec; color: #880e4f; }
        .hubungan-istri     { background: #fce4ec; color: #ad1457; }
        .hubungan-suami     { background: #ede7f6; color: #4527a0; }
        .hubungan-anak      { background: #e8f5e9; color: #2e7d32; }
        .hubungan-saudara   { background: #fff3e0; color: #e65100; }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.12); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.22); }
    </style>
    @stack('styles')
</head>
<body>

{{-- ══════════════════════════════════
     SIDEBAR
══════════════════════════════════ --}}
<aside class="sidebar" id="sidebar">

    {{-- Toggle --}}
    <button class="sb-toggle" id="sbToggleBtn" onclick="toggleSidebar()" title="Collapse sidebar">
        <i class="fa-solid fa-chevron-left" id="sbToggleIcon" style="font-size:10px;"></i>
    </button>

    {{-- Brand --}}
    <div class="sb-brand">
        <div class="sb-brand-icon"><i class="fa-solid fa-layer-group"></i></div>
        <div class="sb-brand-text">
            <div class="sb-brand-name">STEPFAMGET</div>
            <div class="sb-brand-sub">HR System</div>
        </div>
    </div>
    @if(auth()->user()->nama !== 'Hitz')
        {{-- Navigation --}}
        <div class="sb-section-label"></div>

        <div class="sb-nav-wrap {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="sb-nav">
                @if(request()->routeIs('dashboard'))
                    <div class="sb-dot"></div>
                @else
                    <i class="fa-solid fa-gauge-high"></i>
                @endif
                <span class="sb-nav-label">Dashboard</span>
            </a>
        </div>

        {{-- Navigation --}}
        <div class="sb-section-label">Menu</div>

        <div class="sb-nav-wrap {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <a href="{{ route('users.index') }}" class="sb-nav">
                @if(request()->routeIs('users.*'))
                    <div class="sb-dot"></div>
                @else
                    <i class="fa-solid fa-users-gear"></i>
                @endif
                <span class="sb-nav-label">List Admin</span>
            </a>
        </div>

    @endif
        {{-- Data Karyawan --}}
    <div class="sb-nav-wrap {{ request()->routeIs('karyawan.index') || (request()->routeIs('karyawan.*') && !request()->routeIs('karyawan.detail.all')) ? 'active' : '' }}">
        <a href="{{ route('karyawan.index') }}" class="sb-nav">
            @if(request()->routeIs('karyawan.index') || (request()->routeIs('karyawan.*') && !request()->routeIs('karyawan.detail.all')))
                <div class="sb-dot"></div>
            @else
                <i class="fa-solid fa-users"></i>
            @endif
            <span class="sb-nav-label">Data Karyawan</span>
            <span class="sb-badge">{{ $countKaryawan }}</span>
        </a>
    </div>

    @if(auth()->user()->nama !== 'Hitz')
        {{-- Keluarga Karyawan --}}
        <div class="sb-nav-wrap {{ request()->routeIs('karyawan.detail.all') ? 'active' : '' }}">
            <a href="{{ route('karyawan.detail.all') }}" class="sb-nav">
                @if(request()->routeIs('karyawan.detail.all'))
                    <div class="sb-dot"></div>
                @else
                    <i class="fa-solid fa-people-group"></i>
                @endif
                <span class="sb-nav-label">Keluarga Karyawan</span>
                <span class="sb-badge">{{ $countDetailKaryawan }}</span>
            </a>
        </div>

        <div class="sb-nav-wrap {{ request()->routeIs('guest-menu.*') ? 'active' : '' }}">
            <a href="{{ route('guest-menu.index') }}" class="sb-nav">
                @if(request()->routeIs('guest-menu.*'))
                    <div class="sb-dot"></div>
                @else
                    <i class="fa-solid fa-table-cells"></i>
                @endif
                <span class="sb-nav-label">Menu Karyawan</span>
            </a>
        </div>

        <div class="sb-nav-wrap {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
            <a href="{{ route('suppliers.index') }}" class="sb-nav">
                @if(request()->routeIs('suppliers.*'))
                    <div class="sb-dot"></div>
                @else
                    <i class="fa-solid fa-truck"></i>
                @endif
                <span class="sb-nav-label">Data Supplier</span>
            </a>
        </div>

        <div class="sb-nav-wrap {{ request()->routeIs('voting.*') ? 'active' : '' }}">
            <a href="{{ route('voting.index') }}" class="sb-nav">
                @if(request()->routeIs('voting.*'))
                    <div class="sb-dot"></div>
                @else
                    <i class="fa-solid fa-map-location-dot"></i>
                @endif
                <span class="sb-nav-label">Kelola Tempat</span>
            </a>
        </div>

        <div class="sb-nav-wrap {{ request()->routeIs('buses.*') || request()->routeIs('kendaraans.*') ? 'active' : '' }}">
            <div class="sb-nav" onclick="toggleDropdown('dd-transportasi')" style="cursor:pointer;">
                @if(request()->routeIs('buses.*') || request()->routeIs('kendaraans.*'))
                    <div class="sb-dot"></div>
                @else
                    <i class="fa-solid fa-bus"></i>
                @endif
                <span class="sb-nav-label">Transportasi</span>
                <i class="fa-solid fa-chevron-down" id="dd-transportasi-chevron"
                style="font-size:10px;margin-left:auto;color:#94a3b8;transition:transform .2s;
                        {{ request()->routeIs('buses.*') || request()->routeIs('kendaraans.*') ? 'transform:rotate(180deg);' : '' }}">
                </i>
            </div>
            <div id="dd-transportasi"
                style="overflow:hidden;transition:max-height .25s ease;
                        {{ request()->routeIs('buses.*') || request()->routeIs('kendaraans.*') ? 'max-height:120px;' : 'max-height:0;' }}">
                <div style="padding:4px 0 4px 36px;display:flex;flex-direction:column;gap:2px;">
                    <a href="{{ route('buses.index') }}"
                    style="font-size:12px;font-weight:600;padding:6px 10px;border-radius:8px;text-decoration:none;
                            color:{{ request()->routeIs('buses.*') ? '#0b4614' : '#64748b' }};
                            background:{{ request()->routeIs('buses.*') ? '#e8f5e9' : 'transparent' }};">
                        <i class="fa-solid fa-bus" style="font-size:11px;margin-right:6px;"></i>Bus
                    </a>
                    <a href="{{ route('kendaraans.index') }}"
                    style="font-size:12px;font-weight:600;padding:6px 10px;border-radius:8px;text-decoration:none;
                            color:{{ request()->routeIs('kendaraans.*') ? '#0b4614' : '#64748b' }};
                            background:{{ request()->routeIs('kendaraans.*') ? '#e8f5e9' : 'transparent' }};">
                        <i class="fa-solid fa-car" style="font-size:11px;margin-right:6px;"></i>Kendaraan Pribadi
                    </a>
                </div>
            </div>
        </div>

        <div class="sb-nav-wrap {{ request()->routeIs('pengajuan.*') ? 'active' : '' }}">
            <a href="{{ route('pengajuan.index') }}" class="sb-nav">
                @if(request()->routeIs('pengajuan.*'))
                    <div class="sb-dot"></div>
                @else
                    <i class="fa-solid fa-user-plus"></i>
                @endif
                <span class="sb-nav-label">
                    Pengajuan Anggota
                    @php
                        $pendingCount = \App\Models\PengajuanAnggota::where('status', 'pending')->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span style="
                            background:#ef4444;color:#fff;border-radius:100px;
                            font-size:10px;font-weight:700;padding:1px 6px;
                            margin-left:4px;line-height:1.6;display:inline-block;
                        ">{{ $pendingCount }}</span>
                    @endif
                </span>
            </a>
        </div>

        <div class="sb-nav-wrap {{ request()->routeIs('konveksi.*') ? 'active' : '' }}">
            <a href="{{ route('konveksi.index') }}" class="sb-nav">
                @if(request()->routeIs('konveksi.*'))
                    <div class="sb-dot"></div>
                @else
                    <i class="fa-solid fa-shirt"></i>
                @endif
                <span class="sb-nav-label">Konveksi</span>
            </a>
        </div>

        <div class="sb-nav-wrap {{ request()->routeIs('penerimaan-baju.*') ? 'active' : '' }}">
            <a href="{{ route('penerimaan-baju.index') }}" class="sb-nav">
                @if(request()->routeIs('penerimaan-baju.*'))
                    <div class="sb-dot"></div>
                @else
                    <i class="fa-solid fa-box-open"></i>
                @endif
                <span class="sb-nav-label">Penerimaan Baju</span>
            </a>
        </div>

        <div class="sb-nav-wrap {{ request()->routeIs('penerimaan-barang.*') ? 'active' : '' }}">
            <a href="{{ route('penerimaan-barang.index') }}" class="sb-nav">
                @if(request()->routeIs('penerimaan-barang.*'))
                    <div class="sb-dot"></div>
                @else
                    <i class="fa-solid fa-boxes-stacked"></i>
                @endif
                <span class="sb-nav-label">Penerimaan Barang</span>
            </a>
        </div>

        <div class="sb-nav-wrap {{ request()->routeIs('penerimaan-hadiah.*') ? 'active' : '' }}">
            <a href="{{ route('penerimaan-hadiah.index') }}" class="sb-nav">
                @if(request()->routeIs('penerimaan-hadiah.*'))
                    <div class="sb-dot"></div>
                @else
                    <i class="fa-solid fa-gift"></i>
                @endif
                <span class="sb-nav-label">Penerimaan Hadiah</span>
            </a>
        </div>

    @endif

    <div class="sb-nav-wrap {{ request()->routeIs('rundowns.*') ? 'active' : '' }}">
        <a href="{{ route('rundowns.index') }}" class="sb-nav">
            @if(request()->routeIs('rundowns.*'))
                <div class="sb-dot"></div>
            @else
                <i class="fa-solid fa-calendar-days"></i>
            @endif
            <span class="sb-nav-label">Rundown Acara</span>
        </a>
    </div>

    @if(auth()->user()->nama !== 'Hitz')
        <div class="sb-nav-wrap {{ request()->routeIs('gantt.*') ? 'active' : '' }}">
            <a href="{{ route('gantt.index') }}" class="sb-nav">
                @if(request()->routeIs('gantt.*'))
                    <div class="sb-dot"></div>
                @else
                    <i class="fa-solid fa-chart-gantt"></i>
                @endif
                <span class="sb-nav-label">Gantt Chart</span>
            </a>
        </div>
    @endif
           
    <div class="sb-nav-wrap {{ request()->routeIs('konsumsis.*') ? 'active' : '' }}">
        <a href="{{ route('konsumsis.index') }}" class="sb-nav">
            @if(request()->routeIs('konsumsis.*'))
                <div class="sb-dot"></div>
            @else
                <i class="fa-solid fa-clapperboard"></i></i>
            @endif
            <span class="sb-nav-label">Production</span>
        </a>
    </div>
    {{-- <div class="sb-nav-wrap {{ request()->routeIs('import.*') ? 'active' : '' }}">
        <a href="{{ route('import.index') }}" class="sb-nav">
            @if(request()->routeIs('import.*'))
                <div class="sb-dot"></div>
            @else
                <i class="fa-solid fa-file-import"></i>
            @endif
            <span class="sb-nav-label">Import Excel</span>
        </a>
    </div> --}}

    

    {{-- Footer --}}
    <div class="sb-footer">
        <div class="sb-footer-label">User Account</div>
        <div class="sb-user">
            <div class="sb-avatar">
                {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->username, 0, 1)) }}
            </div>
            <div class="sb-uinfo">
                <div class="sb-uname">{{ auth()->user()->nama ?? auth()->user()->username }}</div>
                <div class="sb-uid">#{{ auth()->user()->username }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="sb-logout" title="Logout">
                    <i class="fa-solid fa-arrow-right-from-bracket" style="font-size:11px;"></i>
                </button>
            </form>
        </div>
    </div>

</aside>

{{-- ══════════════════════════════════
     MAIN
══════════════════════════════════ --}}
<div class="main" id="mainArea">

    {{-- Topbar --}}
    <div class="topbar">
        <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
        <button class="topbar-btn"><i class="fa-regular fa-bell"></i></button>
        <button class="topbar-btn"><i class="fa-solid fa-sliders"></i></button>
        <div class="topbar-avatar">
            {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->username, 0, 1)) }}
        </div>
    </div>

    {{-- Content --}}
    <div class="page-content">
        @yield('content')
    </div>

</div>

{{-- Toast container --}}
<div class="toast-container" id="toastContainer"></div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

{{-- ══════════════════════════════════
     SCRIPTS
══════════════════════════════════ --}}
<script>
    // ── Sidebar collapse ──
    const SB_KEY = 'stepfamget_sb_collapsed';
    const sidebar   = document.getElementById('sidebar');
    const mainArea  = document.getElementById('mainArea');
    const toggleIcon = document.getElementById('sbToggleIcon');

    function applySidebarState(collapsed) {
        if (collapsed) {
            sidebar.classList.add('collapsed');
            mainArea.classList.add('sb-collapsed');
            toggleIcon.style.transform = 'rotate(180deg)';
        } else {
            sidebar.classList.remove('collapsed');
            mainArea.classList.remove('sb-collapsed');
            toggleIcon.style.transform = 'rotate(0deg)';
        }
    }

    function toggleSidebar() {
        const isCollapsed = sidebar.classList.contains('collapsed');
        localStorage.setItem(SB_KEY, !isCollapsed);
        applySidebarState(!isCollapsed);
    }

    // Restore state on load
    const savedCollapsed = localStorage.getItem(SB_KEY) === 'true';
    applySidebarState(savedCollapsed);

    // ── Toast helper ──
    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <i class="fa-solid fa-${type === 'success' ? 'circle-check' : 'circle-xmark'}" style="font-size:14px;"></i>
            ${message}
        `;
        container.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-6px)';
            toast.style.transition = 'all .3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3200);
    }

    // ── CSRF for AJAX ──
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    
    function toggleDropdown(id) {
        const el      = document.getElementById(id);
        const chevron = document.getElementById(id + '-chevron');
        const isOpen  = el.style.maxHeight && el.style.maxHeight !== '0px';
        el.style.maxHeight      = isOpen ? '0px' : '120px';
        chevron.style.transform = isOpen ? '' : 'rotate(180deg)';
    }

</script>

@stack('scripts')
</body>
</html>