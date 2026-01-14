<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SantuyTL Dashboard')</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --bg-primary: #0f0f23;
            --bg-secondary: #1a1a2e;
            --bg-card: #16213e;
            --accent: #6c63ff;
            --accent-hover: #5a52d5;
            --text-primary: #ffffff;
            --text-secondary: #a0aec0;
            --success: #48bb78;
            --warning: #ed8936;
            --danger: #fc8181;
            --border: #2d3748;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
        }

        /* Navbar */
        .navbar {
            background: var(--bg-secondary);
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--accent);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Navbar Actions */
        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .navbar-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid var(--accent);
        }

        .navbar-avatar-placeholder {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #a855f7);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
            color: white;
        }

        /* Notification Bell */
        .notification-wrapper {
            position: relative;
        }

        .notification-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            color: var(--text-secondary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            transition: all 0.2s;
            position: relative;
        }

        .notification-btn:hover {
            background: var(--bg-card);
            color: var(--accent);
            border-color: var(--accent);
        }

        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: #ef4444;
            color: white;
            font-size: 0.65rem;
            font-weight: 700;
            min-width: 18px;
            height: 18px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            animation: pulse-badge 2s infinite;
        }

        @keyframes pulse-badge {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .notification-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 360px;
            max-height: 480px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.4);
            display: none;
            overflow: hidden;
            z-index: 1000;
        }

        .notification-dropdown.active { display: block; }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
        }

        .notification-header span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .notification-clear {
            background: none;
            border: none;
            color: var(--accent);
            font-size: 0.8rem;
            cursor: pointer;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .notification-clear:hover {
            background: rgba(108, 99, 255, 0.15);
        }

        .notification-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .notification-empty {
            padding: 3rem 1rem;
            text-align: center;
            color: var(--text-secondary);
        }

        .notification-empty i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .notification-item {
            display: flex;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border);
            cursor: pointer;
            transition: all 0.2s;
        }

        .notification-item:hover { background: rgba(108, 99, 255, 0.1); }
        .notification-item.unread { background: rgba(108, 99, 255, 0.05); }

        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .notification-icon.success { background: rgba(16, 185, 129, 0.15); color: #10b981; }
        .notification-icon.info { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
        .notification-icon.warning { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
        .notification-icon.error { background: rgba(239, 68, 68, 0.15); color: #ef4444; }

        .notification-content { flex: 1; min-width: 0; }
        .notification-title { font-weight: 600; font-size: 0.875rem; margin-bottom: 2px; }
        .notification-text { color: var(--text-secondary); font-size: 0.8rem; }
        .notification-time { color: var(--text-secondary); font-size: 0.7rem; margin-top: 4px; }

        /* Profile Dropdown */
        .profile-wrapper {
            position: relative;
        }

        .profile-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.75rem 0.375rem 0.375rem;
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 50px;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.2s;
        }

        .profile-btn:hover {
            background: var(--bg-card);
            border-color: var(--accent);
        }

        .profile-name {
            font-size: 0.875rem;
            font-weight: 500;
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .profile-arrow {
            font-size: 0.7rem;
            color: var(--text-secondary);
            transition: transform 0.2s;
        }

        .profile-wrapper.active .profile-arrow {
            transform: rotate(180deg);
        }

        .profile-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 280px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.4);
            display: none;
            overflow: hidden;
            z-index: 1000;
        }

        .profile-dropdown.active { display: block; }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1.25rem;
            background: linear-gradient(135deg, rgba(108, 99, 255, 0.1), rgba(168, 85, 247, 0.1));
        }

        .profile-avatar-large {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: 2px solid var(--accent);
        }

        .profile-avatar-large.placeholder {
            background: linear-gradient(135deg, var(--accent), #a855f7);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.25rem;
            color: white;
            border: none;
        }

        .profile-info { min-width: 0; }
        .profile-name-large { font-weight: 700; font-size: 1rem; }
        .profile-email { color: var(--text-secondary); font-size: 0.8rem; overflow: hidden; text-overflow: ellipsis; }

        .profile-divider {
            height: 1px;
            background: var(--border);
            margin: 0;
        }

        .profile-section {
            padding: 0.75rem;
        }

        .profile-section-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-secondary);
            padding: 0.25rem 0.5rem;
            margin-bottom: 0.25rem;
        }

        .profile-servers {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .profile-server-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            border-radius: 8px;
            color: var(--text-primary);
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.2s;
        }

        .profile-server-item:hover { background: rgba(108, 99, 255, 0.1); }
        .profile-server-item.active { background: rgba(108, 99, 255, 0.2); color: var(--accent); }

        .server-mini-icon {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            flex-shrink: 0;
        }

        .server-mini-icon.placeholder {
            background: linear-gradient(135deg, var(--accent), #a855f7);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            color: white;
        }

        .profile-server-more {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            color: var(--accent);
            text-decoration: none;
            font-size: 0.8rem;
        }

        .profile-menu {
            padding: 0.5rem;
        }

        .profile-menu-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            border-radius: 10px;
            color: var(--text-primary);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s;
            border: none;
            background: none;
            width: 100%;
            cursor: pointer;
            text-align: left;
        }

        .profile-menu-item:hover { background: rgba(108, 99, 255, 0.1); }

        .profile-menu-item i { width: 18px; color: var(--text-secondary); }

        .profile-menu-item.logout { color: #ef4444; }
        .profile-menu-item.logout i { color: #ef4444; }
        .profile-menu-item.logout:hover { background: rgba(239, 68, 68, 0.1); }

        .menu-badge {
            margin-left: auto;
            padding: 0.125rem 0.5rem;
            border-radius: 10px;
            font-size: 0.65rem;
            font-weight: 700;
        }

        .menu-badge.premium {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: #1a1a2e;
        }

        .profile-logout-form { margin: 0; padding: 0 0.5rem 0.5rem; }

        /* Layout */
        .layout {
            display: flex;
            min-height: calc(100vh - 57px);
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border);
            overflow-y: auto;
            height: calc(100vh - 57px);
            position: sticky;
            top: 57px;
        }

        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

        /* Server Switcher */
        .server-header {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .server-switch {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: var(--bg-card);
            border-radius: 10px;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s;
        }

        .server-switch:hover { background: rgba(108, 99, 255, 0.15); }

        .server-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            flex-shrink: 0;
        }

        .server-icon.placeholder {
            background: linear-gradient(135deg, #6c63ff, #a855f7);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .server-info { flex: 1; min-width: 0; }
        .server-name { font-weight: 600; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .server-meta { font-size: 0.7rem; color: var(--text-secondary); }

        /* Sidebar Navigation */
        .sidebar-nav { padding: 0.75rem; }

        .nav-section { margin-bottom: 1.25rem; }

        .nav-section-title {
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.5rem 0.75rem;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 0.75rem;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 2px;
            transition: all 0.2s;
            font-size: 0.85rem;
            position: relative;
        }

        .nav-item:hover { background: var(--bg-card); color: var(--text-primary); }
        .nav-item.active { background: rgba(108, 99, 255, 0.15); color: var(--accent); }

        .nav-icon { 
            font-size: 1rem; 
            width: 20px; 
            text-align: center; 
            color: inherit;
        }
        .nav-text { flex: 1; }

        .nav-badge {
            font-size: 0.55rem;
            padding: 0.15rem 0.4rem;
            border-radius: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-coming { background: rgba(108, 99, 255, 0.2); color: var(--accent); }
        .badge-active { background: rgba(72, 187, 120, 0.2); color: var(--success); }
        .badge-new { background: rgba(237, 137, 54, 0.2); color: var(--warning); }
        .badge-premium { background: rgba(255, 215, 0, 0.15); color: #ffd700; }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 1.5rem 2rem;
            min-width: 0;
        }

        .page-title { font-size: 1.5rem; margin-bottom: 0.25rem; }
        .page-subtitle { color: var(--text-secondary); margin-bottom: 1.5rem; font-size: 0.9rem; }

        /* Cards */
        .card {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border);
        }

        /* Tables */
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.875rem 1rem; text-align: left; border-bottom: 1px solid var(--border); }
        th { background: var(--bg-secondary); color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; }
        tr:hover { background: rgba(108, 99, 255, 0.05); }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            font-size: 0.875rem;
        }

        .btn-primary { background: var(--accent); color: white; }
        .btn-primary:hover { background: var(--accent-hover); }

        /* Forms */
        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; margin-bottom: 0.4rem; color: var(--text-secondary); font-size: 0.85rem; }
        .form-input {
            width: 100%;
            padding: 0.65rem 1rem;
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 0.9rem;
        }
        .form-input:focus { outline: none; border-color: var(--accent); }

        /* Alerts */
        .alert { padding: 0.875rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.9rem; }
        .alert-success { background: rgba(72, 187, 120, 0.15); border: 1px solid var(--success); color: var(--success); }
        .alert-error { background: rgba(252, 129, 129, 0.15); border: 1px solid var(--danger); color: var(--danger); }

        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }

        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar { width: 240px; }
        }
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { padding: 1rem; }
        }
    </style>
</head>
<body>
    @auth
    @php 
        $navGuildId = request()->route('guildId') ?? session('selected_guild_id');
        $userGuilds = session('discord_guilds', []);
    @endphp
    <nav class="navbar">
        <a href="{{ $navGuildId ? route('dashboard', ['guildId' => $navGuildId]) : route('select-server') }}" class="navbar-brand"><i class="fa-solid fa-gamepad"></i> SantuyTL</a>
        
        <div class="navbar-actions">
            {{-- Notification Bell --}}
            <div class="notification-wrapper">
                <button class="notification-btn" id="notificationBtn" onclick="toggleNotifications()">
                    <i class="fa-solid fa-bell"></i>
                    <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
                </button>
                <div class="notification-dropdown" id="notificationDropdown">
                    <div class="notification-header">
                        <span><i class="fa-solid fa-bell"></i> Notifikasi</span>
                        <button class="notification-clear" onclick="clearNotifications()">Hapus Semua</button>
                    </div>
                    <div class="notification-list" id="notificationList">
                        <div class="notification-empty">
                            <i class="fa-solid fa-inbox"></i>
                            <p>Tidak ada notifikasi</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Profile Dropdown --}}
            <div class="profile-wrapper">
                <button class="profile-btn" onclick="toggleProfileMenu()">
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="Avatar" class="navbar-avatar">
                    @else
                        <div class="navbar-avatar-placeholder">{{ substr(Auth::user()->name, 0, 1) }}</div>
                    @endif
                    <span class="profile-name">{{ Auth::user()->name }}</span>
                    <i class="fa-solid fa-chevron-down profile-arrow"></i>
                </button>
                
                <div class="profile-dropdown" id="profileDropdown">
                    {{-- User Info --}}
                    <div class="profile-header">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" alt="Avatar" class="profile-avatar-large">
                        @else
                            <div class="profile-avatar-large placeholder">{{ substr(Auth::user()->name, 0, 1) }}</div>
                        @endif
                        <div class="profile-info">
                            <div class="profile-name-large">{{ Auth::user()->name }}</div>
                            <div class="profile-email">{{ Auth::user()->email ?? 'Discord User' }}</div>
                        </div>
                    </div>
                    
                    <div class="profile-divider"></div>
                    
                    {{-- My Servers --}}
                    <div class="profile-section">
                        <div class="profile-section-title"><i class="fa-brands fa-discord"></i> Server Saya</div>
                        <div class="profile-servers">
                            @foreach(array_slice($userGuilds, 0, 5) as $guild)
                                <a href="{{ route('dashboard', ['guildId' => $guild['id']]) }}" class="profile-server-item {{ $navGuildId == $guild['id'] ? 'active' : '' }}">
                                    @if(!empty($guild['icon']))
                                        <img src="https://cdn.discordapp.com/icons/{{ $guild['id'] }}/{{ $guild['icon'] }}.png" class="server-mini-icon">
                                    @else
                                        <div class="server-mini-icon placeholder">{{ substr($guild['name'], 0, 1) }}</div>
                                    @endif
                                    <span>{{ Str::limit($guild['name'], 18) }}</span>
                                </a>
                            @endforeach
                            @if(count($userGuilds) > 5)
                                <a href="{{ route('select-server') }}" class="profile-server-more">
                                    <i class="fa-solid fa-ellipsis"></i> Lihat Semua ({{ count($userGuilds) }})
                                </a>
                            @endif
                        </div>
                    </div>
                    
                    <div class="profile-divider"></div>
                    
                    {{-- Quick Links --}}
                    <div class="profile-menu">
                        @if($navGuildId)
                            <a href="{{ route('dashboard.settings', ['guildId' => $navGuildId]) }}" class="profile-menu-item">
                                <i class="fa-solid fa-gear"></i> Pengaturan Server
                            </a>
                        @endif
                        <a href="{{ route('select-server') }}" class="profile-menu-item">
                            <i class="fa-solid fa-server"></i> Ganti Server
                        </a>
                        <a href="https://discord.gg/santuytl" target="_blank" class="profile-menu-item">
                            <i class="fa-brands fa-discord"></i> Support Server
                        </a>
                        <div class="profile-divider"></div>
                        <a href="{{ route('member.hub') }}" class="profile-menu-item">
                            <i class="fa-solid fa-earth-asia"></i> Santuy World
                            <span class="menu-badge" style="background: linear-gradient(135deg, #f472b6, #a78bfa); color: white;">NEW</span>
                        </a>
                        <a href="{{ route('shop.index', ['guildId' => $navGuildId]) }}" class="profile-menu-item">
                            <i class="fa-solid fa-crown"></i> Shop & Premium
                            <span class="menu-badge premium">PRO</span>
                        </a>
                    </div>
                    
                    <div class="profile-divider"></div>
                    
                    {{-- Logout --}}
                    <form action="{{ route('logout') }}" method="POST" class="profile-logout-form">
                        @csrf
                        <button type="submit" class="profile-menu-item logout">
                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="layout">
        <aside class="sidebar">
            {{-- Get guildId from route parameter --}}
            @php 
                $guildId = request()->route('guildId') ?? session('selected_guild_id');
                $guild = session('selected_guild'); 
            @endphp
            
            {{-- Server Header --}}
            <div class="server-header">
                <a href="{{ route('select-server') }}" class="server-switch">
                    @if(!empty($guild['icon']))
                        <img src="https://cdn.discordapp.com/icons/{{ $guild['id'] }}/{{ $guild['icon'] }}.png" class="server-icon">
                    @else
                        <div class="server-icon placeholder">{{ substr($guild['name'] ?? 'S', 0, 1) }}</div>
                    @endif
                    <div class="server-info">
                        <div class="server-name">{{ $guild['name'] ?? 'Select Server' }}</div>
                        <div class="server-meta">Click to switch server</div>
                    </div>
                </a>
            </div>


            <nav class="sidebar-nav">
                {{-- Dashboard --}}
                <div class="nav-section">
                    <a href="{{ route('dashboard', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-chart-line"></i></span>
                        <span class="nav-text">Dashboard</span>
                    </a>
                    <a href="{{ route('dashboard.leaderboard', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('dashboard.leaderboard') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-trophy"></i></span>
                        <span class="nav-text">Leaderboard</span>
                    </a>
                    <a href="{{ route('plugins', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('plugins') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-puzzle-piece"></i></span>
                        <span class="nav-text">Plugins</span>
                        <span class="nav-badge badge-new">New</span>
                    </a>
                    <a href="{{ route('dashboard.settings', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('dashboard.settings') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-gear"></i></span>
                        <span class="nav-text">Settings</span>
                    </a>
                </div>

                {{-- Essentials --}}
                <div class="nav-section">
                    <div class="nav-section-title">Essentials</div>
                    <a href="{{ route('welcome.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('welcome.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-hand"></i></span>
                        <span class="nav-text">Welcome & Goodbye</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                    <a href="{{ route('reaction-roles.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('reaction-roles.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-icons"></i></span>
                        <span class="nav-text">Reaction Roles</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                    <a href="{{ route('moderator.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('moderator.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-shield-halved"></i></span>
                        <span class="nav-text">Moderator</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                    <a href="{{ route('welcome-channel.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('welcome-channel.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-door-open"></i></span>
                        <span class="nav-text">Welcome Channel</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                    <a href="{{ route('levels.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('levels.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-arrow-up-right-dots"></i></span>
                        <span class="nav-text">Levels</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                    <a href="#" class="nav-item" onclick="showComingSoon(event)">
                        <span class="nav-icon"><i class="fa-solid fa-medal"></i></span>
                        <span class="nav-text">Achievements</span>
                        <span class="nav-badge badge-coming">Soon</span>
                    </a>
                </div>

                {{-- Server Management --}}
                <div class="nav-section">
                    <div class="nav-section-title">Server Management</div>
                    <a href="{{ route('automations.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('automations.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-robot"></i></span>
                        <span class="nav-text">Automations</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                    <a href="{{ route('custom-commands.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('custom-commands.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-terminal"></i></span>
                        <span class="nav-text">Custom Commands</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                    <a href="{{ route('invite-tracker.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('invite-tracker.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-link"></i></span>
                        <span class="nav-text">Invite Tracker</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                    <a href="{{ route('ticketing.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('ticketing.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-ticket"></i></span>
                        <span class="nav-text">Ticketing</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                </div>

                {{-- Utilities --}}
                <div class="nav-section">
                    <div class="nav-section-title">Utilities</div>
                    <a href="{{ route('emojis.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('emojis.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-regular fa-face-grin"></i></span>
                        <span class="nav-text">Emojis</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                    <a href="{{ route('polls.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('polls.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-square-poll-vertical"></i></span>
                        <span class="nav-text">Polls</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                    <a href="{{ route('embed.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('embed.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-code"></i></span>
                        <span class="nav-text">Embed Messages</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                    <a href="#" class="nav-item" onclick="showComingSoon(event)">
                        <span class="nav-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <span class="nav-text">Search Anything</span>
                        <span class="nav-badge badge-coming">Soon</span>
                    </a>
                    <a href="#" class="nav-item" onclick="showComingSoon(event)">
                        <span class="nav-icon"><i class="fa-solid fa-microphone"></i></span>
                        <span class="nav-text">Record</span>
                        <span class="nav-badge badge-coming">Soon</span>
                    </a>
                    <a href="{{ route('reminders.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('reminders.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-regular fa-clock"></i></span>
                        <span class="nav-text">Reminders</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                    <a href="{{ route('stats-channels.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('stats-channels.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-chart-simple"></i></span>
                        <span class="nav-text">Statistics Channels</span>
                        <span class="nav-badge badge-premium">Pro</span>
                    </a>
                    <a href="{{ route('temp-channels.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('temp-channels.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-volume-high"></i></span>
                        <span class="nav-text">Temporary Channels</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                </div>

                {{-- Social Alerts --}}
                <div class="nav-section">
                    <div class="nav-section-title">Social Alerts</div>
                    <a href="{{ route('twitch.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('twitch.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-brands fa-twitch"></i></span>
                        <span class="nav-text">Twitch Alerts</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                    <a href="{{ route('tiktok.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('tiktok.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-brands fa-tiktok"></i></span>
                        <span class="nav-text">TikTok Alerts</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                    <a href="{{ route('x.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('x.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-brands fa-x-twitter"></i></span>
                        <span class="nav-text">X Alerts</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                    <a href="{{ route('youtube.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('youtube.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-brands fa-youtube"></i></span>
                        <span class="nav-text">YouTube Alerts</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                    <a href="{{ route('reddit.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('reddit.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-brands fa-reddit-alien"></i></span>
                        <span class="nav-text">Reddit Alerts</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                    <a href="{{ route('instagram.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('instagram.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-brands fa-instagram"></i></span>
                        <span class="nav-text">Instagram Alerts</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                    <a href="{{ route('kick.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('kick.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-brands fa-kickstarter"></i></span>
                        <span class="nav-text">Kick Alerts</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                    <a href="#" class="nav-item" onclick="showComingSoon(event)">
                        <span class="nav-icon"><i class="fa-solid fa-rss"></i></span>
                        <span class="nav-text">RSS Feeds</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                </div>

                {{-- Games & Fun --}}
                <div class="nav-section">
                    <div class="nav-section-title">Games & Fun</div>
                    <a href="{{ route('giveaways.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('giveaways.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-gift"></i></span>
                        <span class="nav-text">Giveaways</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                    <a href="{{ route('birthdays.index', ['guildId' => $guildId]) }}" class="nav-item {{ request()->routeIs('birthdays.*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-cake-candles"></i></span>
                        <span class="nav-text">Birthdays</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                    <a href="#" class="nav-item" onclick="showComingSoon(event)">
                        <span class="nav-icon"><i class="fa-solid fa-music"></i></span>
                        <span class="nav-text">Music Quiz</span>
                        <span class="nav-badge badge-coming">Soon</span>
                    </a>
                    <a href="#" class="nav-item" onclick="showComingSoon(event)">
                        <span class="nav-icon"><i class="fa-solid fa-coins"></i></span>
                        <span class="nav-text">Economy</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                </div>

                {{-- AI --}}
                <div class="nav-section">
                    <div class="nav-section-title">AI Features</div>
                    <a href="#" class="nav-item" onclick="showComingSoon(event)">
                        <span class="nav-icon"><i class="fa-solid fa-brain"></i></span>
                        <span class="nav-text">AI Chat</span>
                        <span class="nav-badge badge-active">Active</span>
                    </a>
                    <a href="#" class="nav-item" onclick="showComingSoon(event)">
                        <span class="nav-icon"><i class="fa-solid fa-masks-theater"></i></span>
                        <span class="nav-text">AI Characters</span>
                        <span class="nav-badge badge-coming">Soon</span>
                    </a>
                </div>

                {{-- Admin Panel --}}
                @if(in_array(Auth::user()->discord_id ?? '', ['1155782329332146238']))
                <div class="nav-section">
                    <div class="nav-section-title" style="color: #f59e0b;"><i class="fa-solid fa-lock"></i> Admin Panel</div>
                    <a href="{{ route('admin.users') }}" class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-users"></i></span>
                        <span class="nav-text">Users</span>
                    </a>
                    <a href="{{ route('admin.economy') }}" class="nav-item {{ request()->routeIs('admin.economy*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-wallet"></i></span>
                        <span class="nav-text">Economy</span>
                    </a>
                    <a href="{{ route('admin.seasons') }}" class="nav-item {{ request()->routeIs('admin.seasons*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-ranking-star"></i></span>
                        <span class="nav-text">Seasons</span>
                    </a>
                    <a href="{{ route('admin.moderation') }}" class="nav-item {{ request()->routeIs('admin.moderation*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-gavel"></i></span>
                        <span class="nav-text">Moderation</span>
                    </a>
                    <a href="{{ route('admin.analytics') }}" class="nav-item {{ request()->routeIs('admin.analytics*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-solid fa-chart-pie"></i></span>
                        <span class="nav-text">Analytics</span>
                    </a>
                    <a href="{{ route('admin.guilds') }}" class="nav-item {{ request()->routeIs('admin.guilds*') ? 'active' : '' }}">
                        <span class="nav-icon"><i class="fa-brands fa-discord"></i></span>
                        <span class="nav-text">Guilds</span>
                    </a>
                </div>
                @endif
            </nav>
        </aside>

        <main class="main-content">
            @if(session('success'))
                <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error"><i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>

    {{-- Coming Soon Modal --}}
    <div id="comingSoonModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center;">
        <div style="background: var(--bg-card); padding: 2rem; border-radius: 16px; text-align: center; max-width: 400px; margin: 1rem;">
            <div style="font-size: 4rem; margin-bottom: 1rem; color: var(--accent);"><i class="fa-solid fa-rocket"></i></div>
            <h2 style="margin-bottom: 0.5rem;">Coming Soon!</h2>
            <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">Fitur ini sedang dalam pengembangan dan akan segera hadir.</p>
            <button onclick="closeComingSoon()" class="btn btn-primary"><i class="fa-solid fa-check"></i> Mengerti</button>
        </div>
    </div>

    <script>
        // Coming Soon Modal
        function showComingSoon(e) {
            e.preventDefault();
            document.getElementById('comingSoonModal').style.display = 'flex';
        }
        function closeComingSoon() {
            document.getElementById('comingSoonModal').style.display = 'none';
        }
        document.getElementById('comingSoonModal').addEventListener('click', function(e) {
            if (e.target === this) closeComingSoon();
        });

        // Profile Dropdown Toggle
        function toggleProfileMenu() {
            const wrapper = document.querySelector('.profile-wrapper');
            const dropdown = document.getElementById('profileDropdown');
            
            // Close notification dropdown
            document.getElementById('notificationDropdown').classList.remove('active');
            
            wrapper.classList.toggle('active');
            dropdown.classList.toggle('active');
        }

        // Notification Dropdown Toggle
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            
            // Close profile dropdown
            document.querySelector('.profile-wrapper').classList.remove('active');
            document.getElementById('profileDropdown').classList.remove('active');
            
            dropdown.classList.toggle('active');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            const profileWrapper = document.querySelector('.profile-wrapper');
            const notificationWrapper = document.querySelector('.notification-wrapper');
            
            if (profileWrapper && !profileWrapper.contains(e.target)) {
                profileWrapper.classList.remove('active');
                document.getElementById('profileDropdown').classList.remove('active');
            }
            
            if (notificationWrapper && !notificationWrapper.contains(e.target)) {
                document.getElementById('notificationDropdown').classList.remove('active');
            }
        });

        // Notification System
        let notifications = [];

        function addNotification(type, title, text, icon = 'fa-bell') {
            const notification = {
                id: Date.now(),
                type: type, // success, info, warning, error
                title: title,
                text: text,
                icon: icon,
                time: new Date(),
                read: false
            };
            notifications.unshift(notification);
            updateNotificationUI();
            showNotificationToast(notification);
        }

        function updateNotificationUI() {
            const badge = document.getElementById('notificationBadge');
            const list = document.getElementById('notificationList');
            const unreadCount = notifications.filter(n => !n.read).length;

            // Update badge
            if (unreadCount > 0) {
                badge.style.display = 'flex';
                badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
            } else {
                badge.style.display = 'none';
            }

            // Update list
            if (notifications.length === 0) {
                list.innerHTML = `
                    <div class="notification-empty">
                        <i class="fa-solid fa-inbox"></i>
                        <p>Tidak ada notifikasi</p>
                    </div>
                `;
            } else {
                list.innerHTML = notifications.slice(0, 20).map(n => `
                    <div class="notification-item ${n.read ? '' : 'unread'}" onclick="markAsRead(${n.id})">
                        <div class="notification-icon ${n.type}">
                            <i class="fa-solid ${n.icon}"></i>
                        </div>
                        <div class="notification-content">
                            <div class="notification-title">${n.title}</div>
                            <div class="notification-text">${n.text}</div>
                            <div class="notification-time">${timeAgo(n.time)}</div>
                        </div>
                    </div>
                `).join('');
            }
        }

        function markAsRead(id) {
            const notification = notifications.find(n => n.id === id);
            if (notification) {
                notification.read = true;
                updateNotificationUI();
            }
        }

        function clearNotifications() {
            notifications = [];
            updateNotificationUI();
        }

        function timeAgo(date) {
            const seconds = Math.floor((new Date() - date) / 1000);
            if (seconds < 60) return 'Baru saja';
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return `${minutes} menit lalu`;
            const hours = Math.floor(minutes / 60);
            if (hours < 24) return `${hours} jam lalu`;
            const days = Math.floor(hours / 24);
            return `${days} hari lalu`;
        }

        function showNotificationToast(notification) {
            const toast = document.createElement('div');
            toast.className = 'notification-toast';
            toast.innerHTML = `
                <div class="toast-icon ${notification.type}">
                    <i class="fa-solid ${notification.icon}"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">${notification.title}</div>
                    <div class="toast-text">${notification.text}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;
            document.body.appendChild(toast);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        // Load saved notifications from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const savedNotifications = localStorage.getItem('santuy_notifications');
            if (savedNotifications) {
                notifications = JSON.parse(savedNotifications).map(n => ({
                    ...n,
                    time: new Date(n.time)
                }));
            }
            updateNotificationUI();

            // Demo: Add welcome notification if first visit
            if (!localStorage.getItem('santuy_welcomed')) {
                setTimeout(() => {
                    addNotification('success', 'Selamat Datang!', 'Terima kasih sudah menggunakan SantuyTL Dashboard.', 'fa-hand');
                    localStorage.setItem('santuy_welcomed', 'true');
                }, 2000);
            }
        });

        // Save notifications before page unload
        window.addEventListener('beforeunload', function() {
            localStorage.setItem('santuy_notifications', JSON.stringify(notifications.slice(0, 50)));
        });
    </script>

    <style>
        /* Notification Toast */
        .notification-toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.4);
            z-index: 2000;
            animation: slideIn 0.3s ease;
            max-width: 360px;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }

        .toast-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .toast-icon.success { background: rgba(16, 185, 129, 0.15); color: #10b981; }
        .toast-icon.info { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
        .toast-icon.warning { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
        .toast-icon.error { background: rgba(239, 68, 68, 0.15); color: #ef4444; }

        .toast-content { flex: 1; min-width: 0; }
        .toast-title { font-weight: 600; font-size: 0.875rem; }
        .toast-text { color: var(--text-secondary); font-size: 0.8rem; }

        .toast-close {
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0.25rem;
            font-size: 1rem;
        }

        .toast-close:hover { color: var(--text-primary); }
    </style>
    @else
        @yield('content')
    @endauth
</body>
</html>
