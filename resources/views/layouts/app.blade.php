<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Expense Tracker')</title>
    <style>
        :root { color-scheme: light; --bg: #f5f7fb; --panel: #ffffff; --line: #d9e0ea; --text: #172033; --muted: #687387; --brand: #2563eb; --brand-dark: #1d4ed8; --danger: #b91c1c; --ok: #047857; --accent: #0f766e; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--bg); color: var(--text); font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        a { color: inherit; text-decoration: none; }
        .shell { display: grid; grid-template-columns: 240px 1fr; min-height: 100vh; }
        .sidebar { background: #101827; color: #e6edf7; padding: 24px 18px; }
        .brand { display: block; font-size: 20px; font-weight: 700; margin-bottom: 28px; }
        .nav { display: grid; gap: 8px; }
        .nav a, .nav button { border: 0; border-radius: 8px; background: transparent; color: #c7d2e3; cursor: pointer; display: block; font: inherit; padding: 10px 12px; text-align: left; width: 100%; }
        .nav a:hover, .nav button:hover, .nav .active { background: #1e2a3d; color: #ffffff; }
        .main { padding: 0 28px 28px; }
        .app-topbar { align-items: center; background: rgba(245, 247, 251, .94); border-bottom: 1px solid var(--line); backdrop-filter: blur(12px); display: flex; justify-content: flex-end; min-height: 72px; margin: 0 -28px 24px; padding: 14px 28px; position: sticky; top: 0; z-index: 8; }
        .page-header { align-items: center; background: var(--panel); border: 1px solid var(--line); border-radius: 12px; box-shadow: 0 14px 35px rgba(23, 32, 51, .05); display: flex; gap: 18px; justify-content: space-between; margin-bottom: 18px; padding: 20px 22px; }
        .page-heading h1 { font-size: 30px; line-height: 1.1; margin: 0; }
        .eyebrow { color: var(--brand); font-size: 12px; font-weight: 800; letter-spacing: .08em; margin: 0 0 8px; text-transform: uppercase; }
        .subheading { color: var(--muted); margin: 8px 0 0; max-width: 680px; }
        .page-actions { align-items: center; display: flex; flex-wrap: wrap; gap: 10px; justify-content: flex-end; }
        .topbar-right { align-items: center; display: flex; gap: 12px; }
        .profile-menu { position: relative; }
        .profile-button { align-items: center; background: var(--panel); border: 1px solid var(--line); border-radius: 999px; box-shadow: 0 8px 24px rgba(23, 32, 51, .06); cursor: pointer; display: inline-flex; gap: 10px; padding: 7px 12px 7px 8px; }
        .avatar { align-items: center; background: var(--brand); border-radius: 50%; color: #fff; display: inline-flex; font-size: 13px; font-weight: 800; height: 28px; justify-content: center; width: 28px; }
        .profile-dropdown { background: var(--panel); border: 1px solid var(--line); border-radius: 10px; box-shadow: 0 18px 40px rgba(23, 32, 51, .12); display: none; min-width: 220px; padding: 8px; position: absolute; right: 0; top: calc(100% + 8px); z-index: 10; }
        .profile-menu:hover .profile-dropdown, .profile-menu:focus-within .profile-dropdown { display: block; }
        .profile-dropdown a, .profile-dropdown button { background: transparent; border: 0; border-radius: 8px; color: var(--text); cursor: pointer; display: block; font: inherit; padding: 10px 12px; text-align: left; width: 100%; }
        .profile-dropdown a:hover, .profile-dropdown button:hover { background: #f3f6fb; }
        .profile-summary { border-bottom: 1px solid var(--line); margin-bottom: 6px; padding: 10px 12px; }
        .muted { color: var(--muted); }
        .panel { background: var(--panel); border: 1px solid var(--line); border-radius: 10px; padding: 22px; }
        .soft-panel { background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%); border: 1px solid var(--line); border-radius: 12px; box-shadow: 0 14px 35px rgba(23, 32, 51, .06); padding: 22px; }
        .grid { display: grid; gap: 18px; }
        .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .filter-grid { display: grid; gap: 14px; grid-template-columns: 1.4fr 1fr 1fr .8fr auto auto; align-items: end; }
        .expense-list-card { background: var(--panel); border: 1px solid var(--line); border-radius: 12px; box-shadow: 0 14px 35px rgba(23, 32, 51, .05); overflow: visible; }
        .expense-list-header { align-items: flex-end; display: flex; gap: 18px; justify-content: space-between; padding: 18px 20px; }
        .expense-list-title h2 { font-size: 22px; margin: 0; }
        .expense-list-title p { margin: 6px 0 0; }
        .expense-list-meta { align-items: flex-end; display: grid; gap: 12px; justify-items: end; }
        .filtered-total { text-align: right; }
        .filtered-total .label { color: var(--muted); font-size: 12px; font-weight: 700; text-transform: uppercase; }
        .filtered-total .value { font-size: 26px; font-weight: 850; line-height: 1.1; }
        .top-list-tools { align-items: center; display: flex; flex-wrap: wrap; gap: 10px; justify-content: flex-end; }
        .date-filter-wrap { position: relative; }
        .date-filter-wrap[open] .date-trigger { border-color: #c59a36; box-shadow: 0 0 0 3px rgba(197, 154, 54, .12); }
        .date-trigger { align-items: center; background: #fff; border: 1px solid #e2d5b4; border-radius: 9px; color: #2f2410; cursor: pointer; display: inline-flex; gap: 10px; list-style: none; min-height: 40px; padding: 8px 12px; transition: border-color .16s ease, box-shadow .16s ease, background .16s ease; }
        .date-trigger:hover { background: #fffaf0; border-color: #d8bc70; }
        .date-trigger::-webkit-details-marker { display: none; }
        .date-trigger strong { font-size: 13px; }
        .date-trigger span { color: #7c6a45; font-size: 12px; }
        .date-popover { background: #fffefa; border: 1px solid #e6cf91; border-radius: 12px; box-shadow: 0 18px 44px rgba(67, 47, 10, .16); overflow: hidden; position: absolute; right: 0; top: calc(100% + 8px); width: min(380px, calc(100vw - 36px)); z-index: 15; }
        .date-range-title { background: linear-gradient(135deg, #fff9e9 0%, #fff 100%); border-bottom: 1px solid #f0dfb2; color: #2f2410; font-size: 14px; font-weight: 800; padding: 12px 14px; }
        .date-popover-body { display: grid; gap: 12px; padding: 12px; }
        .quick-dates { display: grid; gap: 8px; grid-template-columns: 1fr 1fr; }
        .quick-date { align-items: center; background: #fff; border: 1px solid #eadfbe; border-radius: 8px; color: #3f3014; cursor: pointer; display: flex; font-size: 13px; font-weight: 750; justify-content: center; min-height: 36px; padding: 8px 10px; transition: background .16s ease, border-color .16s ease, color .16s ease, transform .16s ease; }
        .quick-date:hover { background: #fff8e6; border-color: #d8bc70; transform: translateY(-1px); }
        .quick-date-input { position: absolute; opacity: 0; pointer-events: none; }
        .quick-date-input:checked + .quick-date { background: #fff8e6; border-color: #c59a36; color: #5b3f08; box-shadow: inset 0 0 0 1px rgba(197, 154, 54, .16); }
        .date-custom-card { background: #fff; border: 1px solid #eadfbe; border-radius: 10px; padding: 12px; }
        .date-custom-title { align-items: center; display: flex; justify-content: space-between; gap: 10px; margin-bottom: 10px; }
        .date-custom-title span { color: #6b4e13; font-size: 13px; font-weight: 800; }
        .custom-date-grid { display: grid; gap: 10px; grid-template-columns: 1fr 1fr; }
        .custom-date-grid label { color: #6b4e13; font-size: 12px; }
        .custom-date-grid input { min-height: 38px; padding: 8px 10px; }
        .date-popover-footer { align-items: center; background: #fffaf0; border-top: 1px solid #f0dfb2; display: flex; gap: 8px; justify-content: space-between; padding: 10px 12px; }
        .date-popover-footer .btn-primary { background: #b98416; border-color: #a66f10; color: #fff; min-width: 92px; }
        .list-footer { align-items: center; background: #fbfcff; border-top: 1px solid var(--line); border-radius: 0 0 12px 12px; display: flex; flex-wrap: wrap; gap: 10px; justify-content: space-between; padding: 10px 14px; }
        .list-controls { align-items: center; display: flex; flex-wrap: wrap; gap: 10px; }
        .list-control { align-items: center; display: inline-flex; gap: 8px; min-width: auto; }
        .list-control label { color: var(--muted); font-size: 12px; margin: 0; text-transform: uppercase; white-space: nowrap; }
        .list-control select { min-height: 34px; padding: 6px 30px 6px 10px; width: auto; }
        .compact-pagination { align-items: center; display: inline-flex; gap: 6px; }
        .page-chip { align-items: center; background: #fff; border: 1px solid var(--line); border-radius: 8px; display: inline-flex; font-weight: 700; justify-content: center; min-height: 32px; min-width: 32px; padding: 5px 9px; }
        .page-chip:hover { background: #f3f6fb; }
        .page-chip.active { background: var(--brand); border-color: var(--brand); color: #fff; }
        .page-chip.disabled { color: #a1a8b5; cursor: not-allowed; }
        .panel.list-panel { border: 0; border-radius: 0; border-top: 1px solid var(--line); padding: 0; overflow-x: auto; }
        .drawer-toggle { display: none; }
        .drawer-overlay { background: rgba(15, 23, 42, .34); inset: 0; opacity: 0; pointer-events: none; position: fixed; transition: opacity .18s ease; z-index: 20; }
        .filter-drawer { background: var(--panel); border-left: 1px solid var(--line); bottom: 0; box-shadow: -24px 0 48px rgba(23, 32, 51, .18); display: flex; flex-direction: column; max-width: min(440px, 100vw); position: fixed; right: 0; top: 0; transform: translateX(100%); transition: transform .2s ease; width: 420px; z-index: 21; }
        .drawer-toggle:checked ~ .drawer-overlay { opacity: 1; pointer-events: auto; }
        .drawer-toggle:checked ~ .filter-drawer { transform: translateX(0); }
        .drawer-header { align-items: center; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; padding: 24px 26px; }
        .drawer-header h2 { font-size: 26px; margin: 0; }
        .drawer-close { align-items: center; border-radius: 8px; cursor: pointer; display: inline-flex; font-size: 30px; height: 38px; justify-content: center; line-height: 1; width: 38px; }
        .drawer-close:hover { background: #f3f6fb; }
        .drawer-body { display: grid; gap: 20px; overflow-y: auto; padding: 24px 26px; }
        .drawer-footer { border-top: 1px solid var(--line); display: grid; gap: 12px; grid-template-columns: 1fr 1fr; margin-top: auto; padding: 16px 26px; }
        .actions { align-items: center; display: flex; gap: 10px; justify-content: flex-end; }
        .btn { align-items: center; border: 1px solid transparent; border-radius: 8px; cursor: pointer; display: inline-flex; font-weight: 600; justify-content: center; min-height: 38px; padding: 8px 14px; }
        .btn-primary { background: var(--brand); color: #ffffff; }
        .btn-primary:hover { background: var(--brand-dark); }
        .btn-secondary { background: #ffffff; border-color: var(--line); color: var(--text); }
        .btn-danger { background: #fff1f2; border-color: #fecdd3; color: var(--danger); }
        table { border-collapse: collapse; width: 100%; }
        th, td { border-bottom: 1px solid var(--line); padding: 14px 16px; text-align: left; vertical-align: middle; }
        th { color: var(--muted); font-size: 12px; letter-spacing: .04em; text-transform: uppercase; }
        tbody tr:hover { background: #fafcff; }
        tr:last-child td { border-bottom: 0; }
        label { display: block; font-weight: 600; margin-bottom: 7px; }
        input, select, textarea { border: 1px solid var(--line); border-radius: 8px; font: inherit; padding: 10px 12px; width: 100%; }
        textarea { min-height: 110px; resize: vertical; }
        .field { margin-bottom: 16px; }
        .error { color: var(--danger); font-size: 14px; margin-top: 6px; }
        .alert { border-radius: 8px; margin-bottom: 18px; padding: 12px 14px; }
        .alert-success { background: #ecfdf5; border: 1px solid #bbf7d0; color: var(--ok); }
        .toggle { align-items: center; border: 1px solid var(--line); border-radius: 999px; cursor: pointer; display: inline-flex; gap: 8px; min-width: 96px; padding: 5px 9px; }
        .toggle-dot { background: #ffffff; border: 1px solid var(--line); border-radius: 50%; height: 18px; width: 18px; }
        .toggle.active { background: #ecfdf5; border-color: #a7f3d0; color: var(--ok); }
        .toggle.inactive { background: #f3f4f6; color: var(--muted); }
        .auth-page { display: grid; min-height: 100vh; place-items: center; padding: 24px; }
        .auth-card { max-width: 420px; width: 100%; }
        .stat { font-size: 28px; font-weight: 800; margin-top: 8px; }
        .stat-card { overflow: hidden; position: relative; }
        .stat-card::after { background: rgba(37, 99, 235, .08); border-radius: 999px; content: ""; height: 96px; position: absolute; right: -34px; top: -34px; width: 96px; }
        .stat-label { color: var(--muted); font-size: 13px; font-weight: 700; text-transform: uppercase; }
        .money { font-variant-numeric: tabular-nums; white-space: nowrap; }
        .insight-list { display: grid; gap: 12px; margin: 0; padding: 0; }
        .insight-item { border: 1px solid var(--line); border-radius: 8px; display: grid; gap: 4px; list-style: none; padding: 12px; }
        .chart-list { display: grid; gap: 12px; }
        .chart-row { display: grid; gap: 6px; }
        .chart-meta { align-items: center; display: flex; justify-content: space-between; gap: 12px; }
        .bar-track { background: #eef2f7; border-radius: 999px; height: 12px; overflow: hidden; }
        .bar-fill { background: var(--brand); border-radius: 999px; height: 100%; min-width: 3px; }
        .bar-fill.alt { background: var(--accent); }
        .badge { border-radius: 999px; display: inline-flex; font-size: 12px; font-weight: 700; padding: 4px 9px; }
        .badge-up { background: #fff1f2; color: var(--danger); }
        .badge-down { background: #ecfdf5; color: var(--ok); }
        .badge-neutral { background: #eef2f7; color: var(--muted); }
        @media (max-width: 820px) { .shell { grid-template-columns: 1fr; } .sidebar { position: static; } .filter-grid, .grid-2, .grid-3 { grid-template-columns: 1fr; } .main { padding: 0 18px 18px; } .app-topbar { margin: 0 -18px 18px; min-height: 64px; padding: 12px 18px; } .page-header, .expense-list-header { align-items: flex-start; flex-direction: column; } .page-actions, .topbar-right { justify-content: flex-start; width: 100%; } .expense-list-meta { justify-items: stretch; width: 100%; } .filtered-total { text-align: left; } .list-footer, .top-list-tools { align-items: stretch; flex-direction: column; } .list-controls { align-items: stretch; flex-direction: column; } .date-popover { position: static; width: 100%; margin-top: 10px; } .quick-dates, .custom-date-grid, .drawer-footer { grid-template-columns: 1fr; } .list-control { justify-content: space-between; width: 100%; } }
    </style>
</head>
<body>
    @auth
        <div class="shell">
            <aside class="sidebar">
                <a class="brand" href="{{ route('dashboard') }}">Expense Tracker</a>
                <nav class="nav">
                    <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                    <a class="{{ request()->routeIs('expenses.*') ? 'active' : '' }}" href="{{ route('expenses.index') }}">Expenses</a>
                    <a class="{{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">Categories</a>
                    <a class="{{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">Reports</a>
                </nav>
            </aside>

            <main class="main">
                <div class="app-topbar">
                    <div class="topbar-right">
                        <div class="profile-menu">
                            <button class="profile-button" type="button">
                                <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                                <span>{{ auth()->user()->name }}</span>
                            </button>
                            <div class="profile-dropdown">
                                <div class="profile-summary">
                                    <strong>{{ auth()->user()->name }}</strong>
                                    <div class="muted" style="font-size:13px;">{{ auth()->user()->email }}</div>
                                </div>
                                <a href="{{ route('profile.edit') }}">Profile details</a>
                                <a href="{{ route('password.edit') }}">Reset password</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit">Log out</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <section class="page-header">
                    <div class="page-heading">
                        @hasSection('eyebrow')
                            <p class="eyebrow">@yield('eyebrow')</p>
                        @endif
                        <h1>@yield('heading', 'Dashboard')</h1>
                        @hasSection('subheading')
                            <p class="subheading">@yield('subheading')</p>
                        @endif
                    </div>
                    @hasSection('page-actions')
                        <div class="page-actions">
                            @yield('page-actions')
                        </div>
                    @endif
                </section>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert" style="background:#fff1f2; border:1px solid #fecdd3; color:var(--danger);">{{ session('error') }}</div>
                @endif

                @yield('content')
            </main>
        </div>
    @else
        <main class="auth-page">
            @yield('content')
        </main>
    @endauth
</body>
</html>
