<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SantuyTL - The Most Chill Discord Bot</title>
    <link rel="icon" href="https://ext.same-assets.com/825836073/2113711417.png" type="image/png">
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            /* Indigo */
            --primary-dark: #4f46e5;
            --secondary: #d946ef;
            /* Fuchsia */
            --accent: #06b6d4;
            /* Cyan */
            --background: #0f172a;
            /* Slate 900 */
            --surface: #1e293b;
            /* Slate 800 */
            --surface-hover: #334155;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --success: #10b981;
            --gradient-hero: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            --gradient-accent: linear-gradient(90deg, #6366f1, #d946ef);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background);
            color: var(--text-main);
            line-height: 1.6;
            overflow-x: hidden;
        }

        h1,
        h2,
        h3,
        h4,
        .brand-font {
            font-family: 'Outfit', sans-serif;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* Animations */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        @keyframes glow {

            0%,
            100% {
                box-shadow: 0 0 5px rgba(99, 102, 241, 0.5);
            }

            50% {
                box-shadow: 0 0 20px rgba(217, 70, 239, 0.6);
            }
        }

        @keyframes ticker-slide {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        /* Announcement Banner */
        .announcement-banner {
            background: var(--gradient-accent);
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            font-size: 14px;
            font-weight: 600;
        }

        .announcement-banner .tag {
            background: rgba(255, 255, 255, 0.2);
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Navigation */
        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 5%;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 40px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 24px;
            font-weight: 800;
            color: white;
            letter-spacing: -0.5px;
        }

        .logo img {
            height: 40px;
        }

        .logo span {
            background: var(--gradient-accent);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .nav-menu a {
            color: var(--text-muted);
            font-size: 15px;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-menu a:hover {
            color: var(--text-main);
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.4);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.5);
        }

        .btn-outline {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        /* Hero Section */
        .hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 100px 5%;
            min-height: 80vh;
            background: var(--gradient-hero);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
            animation: float 20s infinite linear;
            z-index: 0;
        }

        .hero-content {
            max-width: 600px;
            z-index: 2;
        }

        .hero h1 {
            font-size: 64px;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 24px;
            letter-spacing: -1.5px;
        }

        .hero h1 span {
            color: var(--secondary);
        }

        .hero p {
            color: var(--text-muted);
            font-size: 18px;
            line-height: 1.8;
            margin-bottom: 40px;
            max-width: 90%;
        }

        .hero-buttons {
            display: flex;
            gap: 16px;
        }

        .btn-lg {
            padding: 16px 32px;
            font-size: 16px;
        }

        .hero-image {
            position: relative;
            z-index: 1;
            max-width: 45%;
            animation: float 6s ease-in-out infinite;
        }

        .hero-image img {
            width: 100%;
            height: auto;
            filter: drop-shadow(0 20px 40px rgba(0, 0, 0, 0.5));
        }

        /* Stats Bar */
        .stats-bar {
            background: var(--surface);
            padding: 30px 5%;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            justify-content: space-around;
            text-align: center;
        }

        .stat-item h3 {
            font-size: 32px;
            font-weight: 700;
            color: white;
            margin-bottom: 4px;
        }

        .stat-item p {
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Trusted By Section (Infinite Scroll) */
        .trusted-section {
            padding: 60px 0;
            background: var(--background);
            overflow: hidden;
            text-align: center;
        }

        .trusted-section h4 {
            color: var(--text-muted);
            margin-bottom: 30px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .server-grid-wrapper {
            display: flex;
            width: fit-content;
            animation: ticker-slide 40s linear infinite;
        }

        .server-grid {
            display: flex;
            gap: 20px;
            padding: 0 10px;
        }

        .server-card {
            background: var(--surface);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 15px 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            min-width: 200px;
        }

        .server-card img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .server-info {
            text-align: left;
        }

        .server-name {
            font-weight: 700;
            font-size: 14px;
            display: block;
        }

        .server-members {
            color: var(--success);
            font-size: 12px;
            font-weight: 600;
        }

        /* Features Section */
        .features-container {
            padding: 100px 5%;
        }

        .feature-row {
            display: flex;
            align-items: center;
            gap: 80px;
            margin-bottom: 120px;
        }

        .feature-row:nth-child(even) {
            flex-direction: row-reverse;
        }

        .feature-content {
            flex: 1;
        }

        .feature-label {
            color: var(--accent);
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
            display: block;
        }

        .feature-content h2 {
            font-size: 42px;
            font-weight: 800;
            margin-bottom: 24px;
            line-height: 1.1;
        }

        .feature-content p {
            color: var(--text-muted);
            font-size: 18px;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        .feature-image {
            flex: 1;
            position: relative;
        }

        .feature-image img {
            width: 100%;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s;
        }

        .feature-image:hover img {
            transform: scale(1.02);
        }

        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 100px 5%;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
        }

        .cta-section h2 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 20px;
            color: white;
        }

        .cta-section p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 20px;
            margin-bottom: 40px;
        }

        .btn-white {
            background: white;
            color: var(--primary);
            padding: 18px 40px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 18px;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
        }

        .btn-white:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
        }

        /* Footer */
        .footer {
            background: #0b1120;
            padding: 80px 5% 40px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 60px;
            margin-bottom: 60px;
        }

        .footer-brand p {
            color: var(--text-muted);
            margin-top: 20px;
            max-width: 300px;
        }

        .footer-col h4 {
            color: white;
            margin-bottom: 24px;
            font-size: 16px;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col li {
            margin-bottom: 12px;
        }

        .footer-col a {
            color: var(--text-muted);
            transition: color 0.2s;
        }

        .footer-col a:hover {
            color: var(--accent);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-muted);
            font-size: 14px;
        }

        /* Mobile Responsive */
        @media (max-width: 1024px) {
            .hero h1 {
                font-size: 48px;
            }

            .feature-row {
                gap: 40px;
            }
        }

        @media (max-width: 768px) {
            .nav-menu {
                display: none;
            }

            .hero {
                flex-direction: column;
                text-align: center;
                padding-top: 60px;
            }

            .hero-content {
                margin-bottom: 60px;
            }

            .hero-image {
                max-width: 80%;
            }

            .hero-buttons {
                justify-content: center;
            }

            .stats-bar {
                flex-direction: column;
                gap: 30px;
            }

            .feature-row {
                flex-direction: column !important;
                text-align: center;
            }

            .feature-content h2 {
                font-size: 32px;
            }

            .footer-grid {
                grid-template-columns: 1fr;
                gap: 40px;
                text-align: center;
            }

            .footer-brand {
                margin: 0 auto;
            }

            .footer-bottom {
                flex-direction: column;
                gap: 20px;
            }
        }
    </style>
</head>

<body>
    <!-- Announcement Banner -->
    <div class="announcement-banner">
        <span class="tag">New</span>
        <span>SantuyTL Update v2.0 is live! Economy & Job Systems are now faster than ever.</span>
    </div>

    <!-- Navigation -->
    <nav class="nav">
        <div class="nav-left">
            <a href="#" class="logo">
                <img src="https://ext.same-assets.com/825836073/4021116197.svg" alt="SantuyTL">
                <span>SantuyTL</span>
            </a>
            <div class="nav-menu">
                <a href="#features">Features</a>
                <a href="#commands">Commands</a>
                <a href="#premium">Premium</a>
                <a href="{{ route('select-server') }}">Dashboard</a>
            </div>
        </div>
        <div class="nav-right">
            <a href="https://discord.gg/adMaSMC4sc" class="btn btn-outline" target="_blank">Support</a>
            @auth
                <a href="{{ route('select-server') }}" class="btn btn-primary">Dashboard</a>
            @else
                <a href="{{ route('auth.discord') }}" class="btn btn-primary">Login</a>
            @endauth
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Make Your Discord Server <br><span>Truly Santuy</span></h1>
            <p>The ultimate all-in-one bot for Indonesian communities. Manage economy, jobs, levels, and moderation with
                a single, easy-to-use bot.</p>
            <div class="hero-buttons">
                <a href="https://discord.com/oauth2/authorize?client_id=1441035775218286672&permissions=8&integration_type=0&scope=bot+applications.commands"
                    class="btn btn-primary btn-lg" target="_blank">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;">
                        <path
                            d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03z" />
                    </svg>
                    Add to Discord
                </a>
                <a href="#features" class="btn btn-outline btn-lg">View Features</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="https://ext.same-assets.com/825836073/389126229.svg" alt="SantuyTL Characters">
        </div>
    </section>

    <!-- Stats Section -->
    <div class="container mt-5">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <div class="stats-bar">
        <div class="stat-item">
            <h3>100k+</h3>
            <p>Active Users</p>
        </div>
        <div class="stat-item">
            <h3>500+</h3>
            <p>Communities</p>
        </div>
        <div class="stat-item">
            <h3>24/7</h3>
            <p>Uptime Online</p>
        </div>
    </div>

    <!-- Trusted By -->
    <div class="trusted-section">
        <h4>Trusted by Top Communities</h4>
        <div class="server-grid-wrapper">
            <div class="server-grid">
                <!-- Duplicate this block for infinite scroll illusion -->
                <div class="server-card">
                    <img src="https://ext.same-assets.com/825836073/2392798351.png" alt="Server">
                    <div class="server-info">
                        <span class="server-name">Roblox Indo</span>
                        <span class="server-members">1.1M Members</span>
                    </div>
                </div>
                <div class="server-card">
                    <img src="https://ext.same-assets.com/825836073/3548832117.png" alt="Server">
                    <div class="server-info">
                        <span class="server-name">PUBG Mobile ID</span>
                        <span class="server-members">641k Members</span>
                    </div>
                </div>
                <div class="server-card">
                    <img src="https://ext.same-assets.com/825836073/2912723876.png" alt="Server">
                    <div class="server-info">
                        <span class="server-name">Gamers Santuy</span>
                        <span class="server-members">480K Members</span>
                    </div>
                </div>
                <div class="server-card">
                    <img src="https://ext.same-assets.com/825836073/2001907847.png" alt="Server">
                    <div class="server-info">
                        <span class="server-name">COD Mobile</span>
                        <span class="server-members">345k Members</span>
                    </div>
                </div>
                <!-- Cloning for effect -->
                <div class="server-card">
                    <img src="https://ext.same-assets.com/825836073/2392798351.png" alt="Server">
                    <div class="server-info">
                        <span class="server-name">Roblox Indo</span>
                        <span class="server-members">1.1M Members</span>
                    </div>
                </div>
                <div class="server-card">
                    <img src="https://ext.same-assets.com/825836073/3548832117.png" alt="Server">
                    <div class="server-info">
                        <span class="server-name">PUBG Mobile ID</span>
                        <span class="server-members">641k Members</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="features-container" id="features">

        <!-- Feature 1: Economy & Jobs (Mapped to Bot Personalizer Image) -->
        <div class="feature-row">
            <div class="feature-content">
                <span class="feature-label">Immersive Economy</span>
                <h2>Work, Earn, and Flex Your Wealth</h2>
                <p>Don't just chat—live in the server! With our advanced job system, you can choose your career path,
                    enable daily stipends, and grind your way to the top of the Global Leaderboard. Buy items, trade
                    with friends, and show off your profile.</p>
                <a href="#" class="btn btn-primary">Start Your Career</a>
            </div>
            <div class="feature-image">
                <img src="https://ext.same-assets.com/825836073/1517980391.webp" alt="Economy System">
            </div>
        </div>

        <!-- Feature 2: Welcome (Mapped to Welcome Image) -->
        <div class="feature-row">
            <div class="feature-content">
                <span class="feature-label">Warm Welcome</span>
                <h2>Greet New Members Like a Pro</h2>
                <p>First impressions matter. Automatically send customizable welcome cards with user avatars, member
                    counts, and server rules. Make every new "Santuy-er" feel at home instantly without lifting a
                    finger.</p>
                <a href="#" class="btn btn-outline">Configure Welcome</a>
            </div>
            <div class="feature-image">
                <img src="https://ext.same-assets.com/825836073/1846121344.webp" alt="Welcome System">
            </div>
        </div>

        <!-- Feature 3: Custom Commands/Utility (Mapped to Custom Commands Image) -->
        <div class="feature-row">
            <div class="feature-content">
                <span class="feature-label">Unlimited Power</span>
                <h2>Powerful Tools at Your Fingertips</h2>
                <p>Need a poll? A giveaway? Or maybe a custom meme generator? SantuyTL comes packed with utility
                    commands that make managing your community effortless and fun. Everything is configurable from our
                    web dashboard.</p>
                <a href="#" class="btn btn-primary">See Command List</a>
            </div>
            <div class="feature-image">
                <img src="https://ext.same-assets.com/825836073/632657109.webp" alt="Custom Commands">
            </div>
        </div>

        <!-- Feature 4: Moderation (Mapped to Moderation Image) -->
        <div class="feature-row">
            <div class="feature-content">
                <span class="feature-label">Safety First</span>
                <h2>Keep the Vibes Santuy</h2>
                <p>Trolls and raiders don't stand a chance. Our auto-moderation tools protect your server 24/7. Set up
                    anti-spam, bad word filters, and auto-mute systems to ensure your community stays toxic-free.</p>
                <a href="#" class="btn btn-outline">Setup Moderation</a>
            </div>
            <div class="feature-image">
                <img src="https://ext.same-assets.com/825836073/1466727104.webp" alt="Moderation">
            </div>
        </div>

        <!-- Feature 5: Leveling (Mapped to Levels Image) -->
        <div class="feature-row">
            <div class="feature-content">
                <span class="feature-label">Level Up</span>
                <h2>Reward Active Members</h2>
                <p>Gamify your chat! Users earn XP for every message and voice minute. Unlock special roles, distinct
                    badges, and bragging rights as they climb the ranks. It's the best way to keep your server active.
                </p>
                <a href="#" class="btn btn-primary">View Leaderboard</a>
            </div>
            <div class="feature-image">
                <img src="https://ext.same-assets.com/825836073/1502117820.webp" alt="Leveling System">
            </div>
        </div>

    </div>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-content">
            <h2>Ready to make your server Santuy?</h2>
            <p>Join thousands of other communities who have switched to the most relaxed and feature-rich bot on
                Discord.</p>
            <a href="https://discord.com/oauth2/authorize?client_id=1441035775218286672&permissions=8&integration_type=0&scope=bot+applications.commands"
                class="btn btn-white" target="_blank">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03z" />
                </svg>
                Invite SantuyTL Now
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="#" class="logo">
                    <img src="https://ext.same-assets.com/825836073/4021116197.svg" alt="SantuyTL">
                    <span>SantuyTL</span>
                </a>
                <p>The best Indonesian Discord bot to bootstrap and grow your community with economy, games, and
                    moderation.</p>
            </div>
            <div class="footer-col">
                <h4>Product</h4>
                <ul>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#premium">Premium</a></li>
                    <li><a href="{{ route('select-server') }}">Dashboard</a></li>
                    <li><a href="#">Changelog</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Resources</h4>
                <ul>
                    <li><a href="#">Documentation</a></li>
                    <li><a href="#">API Reference</a></li>
                    <li><a href="https://discord.gg/adMaSMC4sc">Community Support</a></li>
                    <li><a href="#">Guides</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Legal</h4>
                <ul>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Cookie Policy</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; 2025 SantuyTL Project. All rights reserved.</span>
            <div class="social-links">
                <!-- Social icons can go here -->
            </div>
        </div>
    </footer>

    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        // Add scrolled class to nav on scroll
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('.nav');
            if (window.scrollY > 50) {
                nav.style.background = 'rgba(15, 23, 42, 0.95)';
                nav.style.boxShadow = '0 10px 30px -10px rgba(0,0,0,0.5)';
            } else {
                nav.style.background = 'rgba(15, 23, 42, 0.8)';
                nav.style.boxShadow = 'none';
            }
        });
    </script>
</body>

</html>