<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Recoveries') — Whence Financial Services</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
<style>
:root {
    --bg:#0b0f1a;--surface:#111827;--card:#161e2e;--border:#1f2d42;
    --accent:#00d4a0;--accent2:#3b82f6;--accent3:#f59e0b;--danger:#ef4444;
    --text:#e8edf5;--muted:#6b7a99;--subtle:#1d2b42;
    --runaway:#3b82f6;--escalated:#f59e0b;--dormant:#a855f7;--legal:#ef4444;--skip:#00d4a0;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex}
a{color:inherit;text-decoration:none}
/* Sidebar */
.sidebar{width:240px;min-height:100vh;background:var(--surface);border-right:1px solid var(--border);display:flex;flex-direction:column;padding:28px 0;position:fixed;top:0;left:0;bottom:0;z-index:100;overflow-y:auto}
.logo{padding:0 24px 28px;border-bottom:1px solid var(--border)}
.logo-badge{display:inline-block;background:var(--accent);color:#000;font-family:'Syne',sans-serif;font-weight:800;font-size:10px;letter-spacing:2px;padding:4px 8px;border-radius:4px;margin-bottom:8px}
.logo h1{font-family:'Syne',sans-serif;font-size:15px;font-weight:700;line-height:1.3}
.logo span{color:var(--accent)}
nav{padding:16px 0;flex:1}
.nav-section{font-size:10px;font-weight:500;letter-spacing:2px;color:var(--muted);padding:8px 24px 4px;text-transform:uppercase}
.nav-item{display:flex;align-items:center;gap:10px;padding:10px 24px;font-size:13.5px;color:var(--muted);cursor:pointer;transition:all .2s;border-left:2px solid transparent}
.nav-item:hover{color:var(--text);background:var(--subtle)}
.nav-item.active{color:var(--accent);background:rgba(0,212,160,.07);border-left-color:var(--accent)}
.sidebar-footer{padding:16px 24px;border-top:1px solid var(--border)}
.user-row{display:flex;align-items:center;gap:10px}
.avatar{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--accent2),var(--accent));display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-weight:700;font-size:12px;color:#fff;flex-shrink:0}
.user-name{font-size:12.5px;font-weight:500}
.user-role{font-size:11px;color:var(--muted)}
/* Main */
.main{margin-left:240px;flex:1;min-width:0}
.topbar{background:var(--surface);border-bottom:1px solid var(--border);padding:16px 36px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50}
.topbar-left h2{font-family:'Syne',sans-serif;font-size:19px;font-weight:700}
.topbar-left p{font-size:12.5px;color:var(--muted);margin-top:1px}
.topbar-right{display:flex;align-items:center;gap:12px}
.content{padding:32px 36px}
/* Shared components */
.panel{background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden;margin-bottom:20px}
.panel-header{padding:18px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.panel-title{font-family:'Syne',sans-serif;font-size:14px;font-weight:700}
.panel-subtitle{font-size:11.5px;color:var(--muted);margin-top:2px}
.panel-body{padding:20px 24px}
.btn{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;border-radius:8px;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:500;cursor:pointer;border:none;transition:all .2s}
.btn-primary{background:var(--accent);color:#000}
.btn-primary:hover{background:#00bfa5}
.btn-secondary{background:var(--subtle);color:var(--text);border:1px solid var(--border)}
.btn-secondary:hover{border-color:var(--accent)}
.btn-danger{background:rgba(239,68,68,.15);color:var(--danger);border:1px solid rgba(239,68,68,.3)}
.btn-sm{padding:6px 12px;font-size:12px}
.tag{display:inline-block;padding:3px 9px;border-radius:20px;font-size:10.5px;font-weight:500}
.tag-success{background:rgba(0,212,160,.12);color:var(--accent)}
.tag-warning{background:rgba(245,158,11,.12);color:var(--escalated)}
.tag-danger{background:rgba(239,68,68,.12);color:var(--danger)}
.tag-primary{background:rgba(59,130,246,.12);color:var(--runaway)}
.tag-purple{background:rgba(168,85,247,.12);color:var(--dormant)}
.tag-info{background:rgba(0,212,160,.12);color:var(--skip)}
/* Tables */
.data-table{width:100%;border-collapse:collapse}
.data-table th{font-size:10.5px;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);font-weight:500;padding:10px 20px;text-align:left;border-bottom:1px solid var(--border);background:var(--surface)}
.data-table td{padding:13px 20px;font-size:13px;border-bottom:1px solid var(--border);vertical-align:middle}
.data-table tr:last-child td{border-bottom:none}
.data-table tr:hover td{background:var(--subtle)}
/* Forms */
.form-group{margin-bottom:18px}
.form-label{display:block;font-size:12px;font-weight:500;color:var(--muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px}
.form-control{width:100%;background:var(--subtle);border:1px solid var(--border);color:var(--text);border-radius:8px;padding:10px 14px;font-family:'DM Sans',sans-serif;font-size:13px;transition:.2s}
.form-control:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px rgba(0,212,160,.1)}
select.form-control option{background:var(--surface)}
.form-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.form-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px}
/* Alerts */
.alert{padding:12px 18px;border-radius:8px;font-size:13px;margin-bottom:16px}
.alert-success{background:rgba(0,212,160,.1);border:1px solid rgba(0,212,160,.3);color:var(--accent)}
.alert-error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:var(--danger)}
/* Period pill */
.period-pill{display:flex;background:var(--card);border:1px solid var(--border);border-radius:8px;overflow:hidden}
.period-pill a,.period-pill button{background:none;border:none;color:var(--muted);font-family:'DM Sans',sans-serif;font-size:12px;padding:6px 14px;cursor:pointer;transition:.2s;text-decoration:none;display:block}
.period-pill a.active,.period-pill button.active{background:var(--accent);color:#000;font-weight:500}
</style>
@stack('styles')
</head>
<body>
<aside class="sidebar">
    <div class="logo">
        <div class="logo-badge">WFS</div>
        <h1>Whence Financial<br><span>Recoveries</span></h1>
    </div>
    <nav>
        <div class="nav-section">Overview</div>
        <a href="{{ url('recovery/overview') }}" class="nav-item {{ Request::is('recovery/overview') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <div class="nav-section">Cases</div>
        <a href="{{ url('recovery/case/cross_branch') }}" class="nav-item {{ request()->is('recoveries/cases*') && request('category')=='cross_branch' ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
            Cross-Branch
        </a>
        <a href="{{ url('recovery/case/escalated') }}" class="nav-item {{ request('category')=='escalated' ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            Escalated Accounts
        </a>
        <a href="{{ url('recovery/case/dormant') }}" class="nav-item {{ request('category')=='dormant' ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            Dormant Accounts
        </a>
        <a href="{{ url('recovery/case/legal') }}" class="nav-item {{ request('category')=='legal' ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Legal Recovery
        </a>
        <a href="{{ url('recovery/case/skip_trace') }}" class="nav-item {{ request('category')=='skip_trace' ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            Skip Tracing
        </a>
        <a href="{{ url('recovery/case/data') }}" class="nav-item {{ Request::is('recovery/case/data') && !request('category') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
            All Cases
        </a>
        <div class="nav-section">Team</div>
        <a href="{{ url('recovery/specialist/data') }}" class="nav-item {{ Request::is('recovery/specialist/*') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Specialists
        </a>
        <div class="nav-section">Reports</div>
        <a href="{{ url('recovery/report/overview') }}" class="nav-item {{ Request::is('recovery/report/overview') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            Monthly Report
        </a>
        <a href="{{ url('recovery/report/attribution') }}" class="nav-item {{ Request::is('recovery/report/attribution') ? 'active' : '' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><path d="M13 6h3a2 2 0 0 1 2 2v7"/><line x1="6" y1="9" x2="6" y2="21"/></svg>
            Attribution
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="user-row">
            <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name ?? 'User' }}</div>
                <div class="user-role">{{ auth()->user()->role_label ?? 'Recoveries' }}</div>
            </div>
        </div>
    </div>
</aside>

<main class="main">
    <div class="topbar">
        <div class="topbar-left">
            <h2>@yield('page-title', 'Recoveries')</h2>
            <p>@yield('page-subtitle', '')</p>
        </div>
        <div class="topbar-right">
            @yield('topbar-actions')
        </div>
    </div>

    <div class="content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</main>

@stack('scripts')
</body>
</html>
