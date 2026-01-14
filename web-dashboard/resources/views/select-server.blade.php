<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Server - SantuyTL</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #d946ef;
            --accent: #06b6d4;
            --background: #0f172a;
            --surface: #1e293b;
            --surface-hover: #334155;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --success: #10b981;
            --gradient-hero: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            --gradient-accent: linear-gradient(90deg, #6366f1, #d946ef);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background);
            color: var(--text-main);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        a { text-decoration: none; color: inherit; }
        button { font-family: inherit; }

        /* Navbar (Condensed version of welcome.blade.php) */
        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 5%;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 24px;
            font-weight: 800;
            color: white;
            letter-spacing: -0.5px;
            font-family: 'Outfit', sans-serif;
        }

        .logo img { height: 32px; }

        .logo span {
            background: var(--gradient-accent);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-right { display: flex; align-items: center; gap: 15px; }

        .btn-logout {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-muted);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            border: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-logout:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        /* Main Content */
        .container {
            max-width: 800px;
            width: 100%;
            margin: 60px auto;
            padding: 0 20px;
            flex: 1;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .page-header p {
            color: var(--text-muted);
        }

        /* Server List */
        .server-list {
            background: var(--surface);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .server-row-form {
            display: block;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .server-row-form:last-child { border-bottom: none; }

        .server-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            width: 100%;
            background: transparent;
            border: none;
            cursor: pointer;
            text-align: left;
            transition: background 0.2s;
        }

        .server-row:hover {
            background: var(--surface-hover);
        }

        .server-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .server-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--surface-hover);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            font-weight: 700;
            color: var(--text-muted);
            font-size: 18px;
        }

        .server-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .server-details {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .server-name {
            font-weight: 600;
            font-size: 16px;
            color: white;
        }

        .server-info-badges {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .badge-owner {
            color: #fbbf24;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .badge-id {
            opacity: 0.6;
            font-family: monospace;
        }

        .btn-action {
            padding: 8px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            background: var(--primary);
            color: white;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .server-row:hover .btn-action {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .btn-setup {
            background: transparent;
            border: 1px solid var(--text-muted);
            color: var(--text-muted);
        }

        .server-row:hover .btn-setup {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-color: white;
            box-shadow: none;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-muted);
        }

        .empty-state span {
            font-size: 48px;
            display: block;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        /* Footer */
        .footer-tiny {
            text-align: center;
            padding: 20px;
            color: var(--text-muted);
            font-size: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            background: #0b1120;
        }

        @media (max-width: 640px) {
            .server-row { padding: 16px; }
            .server-icon { width: 40px; height: 40px; }
            .badge-id { display: none; }
            .btn-action { padding: 6px 16px; font-size: 13px; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="nav">
        <a href="/" class="logo">
            <img src="https://ext.same-assets.com/825836073/4021116197.svg" alt="SantuyTL">
            <span>SantuyTL</span>
        </a>
        <div class="nav-right">
            <span style="color: var(--text-muted); font-size: 14px;">{{ auth()->user()->name ?? 'User' }}</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Select a Server</h1>
            <p>Choose a server to manage with SantuyTL</p>
        </div>

        @if(count($guilds) > 0)
            <div class="server-list">
                @foreach($guilds as $guild)
                    <div class="server-row-form">
                        <div class="server-row">
                            <div class="server-left">
                                <div class="server-icon">
                                    @if(!empty($guild['icon']))
                                        <img src="https://cdn.discordapp.com/icons/{{ $guild['id'] }}/{{ $guild['icon'] }}.png" alt="{{ $guild['name'] }}">
                                    @else
                                        {{ strtoupper(substr($guild['name'] ?? 'S', 0, 2)) }}
                                    @endif
                                </div>
                                <div class="server-details">
                                    <div class="server-name">{{ $guild['name'] ?? 'Unknown Server' }}</div>
                                    <div class="server-info-badges">
                                        @if(($guild['owner'] ?? false))
                                            <span class="badge-owner">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M5 16L3 5L8.5 10L12 4L15.5 10L21 5L19 16H5M19 19C19 19.6 18.6 20 18 20H6C5.4 20 5 19.6 5 19V18H19V19Z" />
                                                </svg>
                                                Owner
                                            </span>
                                        @endif
                                        <span class="badge-id">ID: {{ $guild['id'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="server-action">
                                @if($guild['has_bot'])
                                    <form action="{{ route('select-server.select') }}" method="POST" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="guild_id" value="{{ $guild['id'] }}">
                                        <button type="submit" class="btn-action">Manage</button>
                                    </form>
                                @else
                                    <a href="https://discord.com/oauth2/authorize?client_id={{ config('services.discord.client_id') }}&permissions=8&scope=bot+applications.commands&guild_id={{ $guild['id'] }}" target="_blank" class="btn-action btn-setup">Setup</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="server-list">
                <div class="empty-state">
                    <span>😕</span>
                    <h3>No Servers Found</h3>
                    <p>It looks like you don't have manage permissions on any servers.</p>
                </div>
            </div>
        @endif
    </div>

    <footer class="footer-tiny">
        &copy; 2025 SantuyTL Project. All rights reserved.
    </footer>
</body>
</html>
