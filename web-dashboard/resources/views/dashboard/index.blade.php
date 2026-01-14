@extends('layouts.app')

@section('title', 'Dashboard - SantuyTL')

@section('content')
<h1 class="page-title"><i class="fa-solid fa-chart-line"></i> Dashboard</h1>

{{-- Stats Grid --}}
<div class="stats-grid">
    <div class="card stat-card">
        <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
        <div class="stat-content">
            <div class="card-title">Total Users</div>
            <div class="card-value">{{ number_format($stats['total_users'] ?? 0) }}</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon coins"><i class="fa-solid fa-coins"></i></div>
        <div class="stat-content">
            <div class="card-title">Total Coins</div>
            <div class="card-value">{{ number_format($stats['total_coins'] ?? 0) }}</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon xp"><i class="fa-solid fa-star"></i></div>
        <div class="stat-content">
            <div class="card-title">Total XP</div>
            <div class="card-value">{{ number_format($stats['total_xp'] ?? 0) }}</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon level"><i class="fa-solid fa-arrow-trend-up"></i></div>
        <div class="stat-content">
            <div class="card-title">Avg Level</div>
            <div class="card-value">{{ $stats['avg_level'] ?? 0 }}</div>
        </div>
    </div>
</div>

{{-- Two Column Layout --}}
<div class="dashboard-grid">
    {{-- Top Players --}}
    <div class="card">
        <div class="card-header">
            <h2><i class="fa-solid fa-fire"></i> Top 5 Players</h2>
            <a href="{{ route('dashboard.leaderboard', ['guildId' => request()->route('guildId')]) }}" class="btn-link">See All <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Level</th>
                        <th>XP</th>
                        <th>Coins</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stats['top_users'] ?? [] as $index => $user)
                    <tr>
                        <td>
                            @if($index == 0) <span class="rank-badge gold"><i class="fa-solid fa-trophy"></i></span>
                            @elseif($index == 1) <span class="rank-badge silver"><i class="fa-solid fa-medal"></i></span>
                            @elseif($index == 2) <span class="rank-badge bronze"><i class="fa-solid fa-award"></i></span>
                            @else {{ $index + 1 }}
                            @endif
                        </td>
                        <td class="user-cell">
                            <span class="username">{{ $user->username ?? 'Unknown' }}</span>
                        </td>
                        <td><span class="level-badge">Lv.{{ $user->level ?? 1 }}</span></td>
                        <td>{{ number_format($user->xp ?? 0) }}</td>
                        <td class="coins-cell"><i class="fa-solid fa-coins"></i> {{ number_format($user->coins ?? 0) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="empty-state">
                            <div class="empty-icon"><i class="fa-solid fa-robot"></i></div>
                            <p>Belum ada data. Jalankan bot dan invite ke server!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Server List --}}
    <div class="card">
        <div class="card-header">
            <h2><i class="fa-brands fa-discord"></i> Your Servers</h2>
        </div>
        <div class="server-list">
            @forelse($guilds as $guild)
            <div class="server-item {{ ($guild['id'] ?? '') == $selectedGuildId ? 'active' : '' }}">
                @if(isset($guild['icon']))
                    <img src="https://cdn.discordapp.com/icons/{{ $guild['id'] }}/{{ $guild['icon'] }}.png" 
                         alt="{{ $guild['name'] }}" class="server-icon">
                @else
                    <div class="server-icon-placeholder">{{ substr($guild['name'] ?? 'S', 0, 1) }}</div>
                @endif
                <div class="server-info">
                    <div class="server-name">{{ $guild['name'] ?? 'Unknown Server' }}</div>
                    <div class="server-id">ID: {{ $guild['id'] ?? 'N/A' }}</div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <p>No servers found. Make sure the bot is in your servers.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
    }

    .stat-icon {
        font-size: 1.5rem;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(108, 99, 255, 0.15);
        border-radius: 12px;
        color: var(--accent);
    }

    .stat-icon.coins { background: rgba(251, 191, 36, 0.15); color: #fbbf24; }
    .stat-icon.xp { background: rgba(168, 85, 247, 0.15); color: #a855f7; }
    .stat-icon.level { background: rgba(34, 197, 94, 0.15); color: #22c55e; }

    .stat-content {
        flex: 1;
    }

    .card-title {
        font-size: 0.85rem;
        color: var(--text-secondary);
        margin-bottom: 0.25rem;
    }

    .card-value {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 1024px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border);
    }

    .card-header h2 {
        font-size: 1.1rem;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-link {
        color: var(--accent);
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .btn-link:hover {
        text-decoration: underline;
    }

    .rank-badge {
        font-size: 1.1rem;
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .rank-badge.gold { color: #fbbf24; }
    .rank-badge.silver { color: #94a3b8; }
    .rank-badge.bronze { color: #cd7f32; }

    .level-badge {
        background: linear-gradient(135deg, #6c63ff, #a855f7);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .user-cell {
        font-weight: 500;
    }

    .coins-cell {
        color: #fbbf24;
        font-weight: 600;
    }

    .empty-state {
        text-align: center;
        padding: 2rem;
        color: var(--text-secondary);
    }

    .empty-icon {
        font-size: 3rem;
        margin-bottom: 0.5rem;
        color: var(--accent);
    }

    .server-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        max-height: 400px;
        overflow-y: auto;
    }

    .server-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem;
        border-radius: 8px;
        background: var(--bg-secondary);
        border: 1px solid transparent;
        transition: all 0.2s;
    }

    .server-item:hover, .server-item.active {
        border-color: var(--accent);
        background: rgba(108, 99, 255, 0.1);
    }

    .server-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .server-icon-placeholder {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--accent);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: white;
    }

    .server-info {
        flex: 1;
    }

    .server-name {
        font-weight: 600;
        font-size: 0.9rem;
    }

    .server-id {
        font-size: 0.75rem;
        color: var(--text-secondary);
    }
</style>
@endsection
