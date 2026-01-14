<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Santuy World')</title>
    
    <!-- Fonts: Nunito (Rounded & Friendly) -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            /* Cute & Friendly Palette (Soft Night Theme) */
            --bg-body: #232136;
            --bg-card: #2a2744;
            --bg-card-hover: #393552;
            
            /* Pastel Accents */
            --cute-pink: #eb92be;   /* Rosewater */
            --cute-purple: #c4a7e7; /* Iris */
            --cute-blue: #9ccfd8;   /* Foam */
            --cute-yellow: #f6c177; /* Gold */
            --cute-red: #eb6f92;    /* Love */
            
            --text-main: #e0def4;
            --text-muted: #908caa;
            
            --radius-l: 24px;
            --radius-m: 16px;
            --radius-s: 12px;
            
            --shadow-soft: 0 10px 30px -10px rgba(0, 0, 0, 0.3);
            --bounce: cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(235, 146, 190, 0.05) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(196, 167, 231, 0.05) 0%, transparent 20%);
        }

        /* Cute Scrollbar */
        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { background: var(--bg-body); }
        ::-webkit-scrollbar-thumb { 
            background: var(--bg-card-hover); 
            border-radius: 10px; 
            border: 3px solid var(--bg-body);
        }

        /* Layout Structure */
        .app-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem 6rem 1rem; /* Bottom padding for mobile nav */
        }

        /* Navbar (Desktop) */
        .desktop-nav {
            display: none;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            align-items: center;
            justify-content: space-between;
        }

        .brand {
            font-weight: 900;
            font-size: 1.5rem;
            color: var(--cute-pink);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            transition: transform 0.3s var(--bounce);
        }
        
        .brand:hover { transform: scale(1.05); }

        .brand-icon {
            background: rgba(235, 146, 190, 0.15);
            width: 48px;
            height: 48px;
            border-radius: var(--radius-m);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            transform: rotate(-5deg);
        }

        .nav-links {
            display: flex;
            gap: 1rem;
            background: var(--bg-card);
            padding: 0.5rem;
            border-radius: 50px;
            box-shadow: var(--shadow-soft);
        }

        .nav-link {
            padding: 0.75rem 1.5rem;
            border-radius: 40px;
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 700;
            transition: all 0.3s var(--bounce);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link:hover { color: var(--text-main); background: rgba(255,255,255,0.05); }
        .nav-link.active {
            background: var(--cute-purple);
            color: #232136;
            box-shadow: 0 5px 15px rgba(196, 167, 231, 0.4);
        }

        .user-pill {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--bg-card);
            padding: 0.5rem 0.5rem 0.5rem 1.25rem;
            border-radius: 50px;
            text-decoration: none;
            color: var(--text-main);
            font-weight: 700;
            transition: transform 0.3s var(--bounce);
            box-shadow: var(--shadow-soft);
        }

        .user-pill:hover { transform: translateY(-3px); }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid var(--cute-pink);
        }

        /* Mobile Bottom Nav */
        .mobile-nav {
            position: fixed;
            bottom: 1rem;
            left: 1rem;
            right: 1rem;
            background: rgba(42, 39, 68, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem;
            border-radius: var(--radius-l);
            display: flex;
            justify-content: space-around;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            z-index: 1000;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .mobile-item {
            text-decoration: none;
            color: var(--text-muted);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.75rem;
            position: relative;
            transition: all 0.3s var(--bounce);
        }

        .mobile-item i {
            font-size: 1.25rem;
            margin-bottom: 2px;
            transition: transform 0.3s var(--bounce);
        }

        .mobile-item.active { color: var(--cute-pink); }
        .mobile-item.active i { transform: translateY(-5px); }

        /* Responsive Breakpoints */
        @media (min-width: 768px) {
            .desktop-nav { display: flex; }
            .mobile-nav { display: none; }
            .app-container { padding-bottom: 2rem; }
        }

        /* Common Components */
        .card {
            background: var(--bg-card);
            border-radius: var(--radius-l);
            padding: 1.5rem;
            border: 1px solid rgba(255,255,255,0.05);
            transition: transform 0.3s var(--bounce), box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-soft);
            border-color: rgba(255,255,255,0.1);
        }

        .btn-cute {
            background: var(--cute-pink);
            color: #232136;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-m);
            font-weight: 800;
            font-family: 'Nunito', sans-serif;
            cursor: pointer;
            transition: transform 0.2s var(--bounce);
            text-decoration: none;
            display: inline-block;
        }

        .btn-cute:hover { transform: scale(1.05) rotate(-2deg); }
        
        .page-title {
            font-size: 2rem;
            font-weight: 900;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--cute-pink), var(--cute-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="app-container">
        {{-- Desktop Navbar --}}
        <header class="desktop-nav">
            <a href="{{ route('member.hub') }}" class="brand">
                <div class="brand-icon"><i class="fa-solid fa-gamepad"></i></div>
                Santuy World
            </a>

            <nav class="nav-links">
                <a href="{{ route('member.hub') }}" class="nav-link {{ request()->routeIs('member.hub') ? 'active' : '' }}">
                    <i class="fa-solid fa-house"></i> Home
                </a>
                <a href="{{ route('member.market') }}" class="nav-link {{ request()->routeIs('member.market') ? 'active' : '' }}">
                    <i class="fa-solid fa-store"></i> Market
                </a>
                <a href="{{ route('member.leaderboard') }}" class="nav-link {{ request()->routeIs('member.leaderboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-trophy"></i> HOF
                </a>
                <a href="{{ route('member.profile') }}" class="nav-link {{ request()->routeIs('member.profile') ? 'active' : '' }}">
                    <i class="fa-solid fa-user"></i> Profile
                </a>
                <a href="{{ route('select-server') }}" class="nav-link">
                    <i class="fa-solid fa-wrench"></i> Manage
                </a>
            </nav>

            <a href="{{ route('member.profile') }}" class="user-pill">
                <span>{{ Auth::user()->name }}</span>
                @if(Auth::user()->avatar)
                    <img src="{{ Auth::user()->avatar }}" class="user-avatar">
                @else
                    <div class="user-avatar" style="background: var(--cute-purple); display: flex; align-items: center; justify-content: center;">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                @endif
            </a>
        </header>

        {{-- Mobile Navbar --}}
        <nav class="mobile-nav">
            <a href="{{ route('member.hub') }}" class="mobile-item {{ request()->routeIs('member.hub') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i>
                <span>Home</span>
            </a>
            <a href="{{ route('member.market') }}" class="mobile-item {{ request()->routeIs('member.market') ? 'active' : '' }}">
                <i class="fa-solid fa-store"></i>
                <span>Shop</span>
            </a>
            <div class="mobile-item" style="margin-top: -2rem;">
                <div style="background: var(--cute-pink); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 5px 15px rgba(235, 146, 190, 0.4);">
                    <i class="fa-solid fa-gamepad" style="color: #232136; margin: 0; transform: none;"></i>
                </div>
            </div>
            <a href="{{ route('member.leaderboard') }}" class="mobile-item {{ request()->routeIs('member.leaderboard') ? 'active' : '' }}">
                <i class="fa-solid fa-trophy"></i>
                <span>Rank</span>
            </a>
            <a href="{{ route('member.profile') }}" class="mobile-item {{ request()->routeIs('member.profile') ? 'active' : '' }}">
                <i class="fa-solid fa-user"></i>
                <span>Me</span>
            </a>
        </nav>

        {{-- Content Area --}}
        <main>
            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>
