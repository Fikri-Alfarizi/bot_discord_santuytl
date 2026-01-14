@extends('layouts.app')

@section('title', 'Leaderboard - SantuyTL')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-trophy"></i> Leaderboard</h1>
    <p class="page-subtitle">Top 50 players berdasarkan Level dan XP</p>
</div>

<div class="card">
    <div class="leaderboard-filters">
        <button class="filter-btn active" data-sort="level">By Level</button>
        <button class="filter-btn" data-sort="coins">By Coins</button>
        <button class="filter-btn" data-sort="seasonal">Seasonal XP</button>
    </div>

    <div class="table-container">
        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Player</th>
                    <th>Level</th>
                    <th>XP</th>
                    <th>Seasonal XP</th>
                    <th>Coins</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaderboard as $index => $user)
                <tr class="leaderboard-row {{ $index < 3 ? 'top-three' : '' }}">
                    <td class="rank-cell">
                        @if($index == 0)
                            <div class="rank-medal gold">
                                <span class="medal-icon"><i class="fa-solid fa-crown"></i></span>
                                <span class="rank-number">1</span>
                            </div>
                        @elseif($index == 1)
                            <div class="rank-medal silver">
                                <span class="medal-icon"><i class="fa-solid fa-medal"></i></span>
                                <span class="rank-number">2</span>
                            </div>
                        @elseif($index == 2)
                            <div class="rank-medal bronze">
                                <span class="medal-icon"><i class="fa-solid fa-award"></i></span>
                                <span class="rank-number">3</span>
                            </div>
                        @else
                            <span class="rank-number-plain">{{ $index + 1 }}</span>
                        @endif
                    </td>
                    <td class="player-cell">
                        <div class="player-avatar">
                            @if($user->avatar)
                                <img src="https://cdn.discordapp.com/avatars/{{ $user->id }}/{{ $user->avatar }}.png" alt="{{ $user->username }}" style="width: 100%; height: 100%; border-radius: 50%;">
                            @else
                                {{ strtoupper(substr($user->username ?? 'U', 0, 1)) }}
                            @endif
                        </div>
                        <span class="player-name">{{ $user->username ?? 'Unknown' }}</span>
                    </td>
                    <td>
                        <div class="level-display">
                            <span class="level-badge">{{ $user->level ?? 1 }}</span>
                            <div class="xp-bar">
                                <div class="xp-fill" style="width: {{ min(($user->xp ?? 0) % 100, 100) }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="xp-cell">{{ number_format($user->xp ?? 0) }}</td>
                    <td class="seasonal-cell">
                        <span class="seasonal-badge"><i class="fa-solid fa-star"></i> {{ number_format($user->seasonal_xp ?? 0) }}</span>
                    </td>
                    <td class="coins-cell">
                        <span class="coins-display"><i class="fa-solid fa-coins"></i> {{ number_format($user->coins ?? 0) }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="empty-state">
                        <div class="empty-content">
                            <div class="empty-icon"><i class="fa-solid fa-chart-bar"></i></div>
                            <h3>Belum Ada Data</h3>
                            <p>Leaderboard akan terisi setelah user aktif di server Discord.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    .page-header {
        margin-bottom: 2rem;
    }

    .page-subtitle {
        color: var(--text-secondary);
        margin-top: 0.25rem;
    }

    .leaderboard-filters {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--border);
    }

    .filter-btn {
        padding: 0.5rem 1rem;
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: 20px;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.875rem;
    }

    .filter-btn:hover, .filter-btn.active {
        background: var(--accent);
        color: white;
        border-color: var(--accent);
    }

    .leaderboard-table {
        width: 100%;
    }

    .leaderboard-row {
        transition: all 0.2s;
    }

    .leaderboard-row:hover {
        background: rgba(108, 99, 255, 0.1);
    }

    .leaderboard-row.top-three {
        background: rgba(108, 99, 255, 0.05);
    }

    .rank-cell {
        width: 80px;
    }

    .rank-medal {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.25rem;
    }

    .medal-icon {
        font-size: 1.5rem;
    }

    .rank-medal.gold .medal-icon { color: #fbbf24; }
    .rank-medal.silver .medal-icon { color: #94a3b8; }
    .rank-medal.bronze .medal-icon { color: #cd7f32; }

    .rank-number {
        font-size: 0.75rem;
        font-weight: 700;
    }

    .rank-number-plain {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-secondary);
    }

    .player-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .player-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6c63ff, #a855f7);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: white;
        font-size: 0.875rem;
    }

    .player-name {
        font-weight: 600;
    }

    .level-display {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .level-badge {
        background: linear-gradient(135deg, #6c63ff, #a855f7);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        min-width: 45px;
        text-align: center;
    }

    .xp-bar {
        width: 60px;
        height: 6px;
        background: var(--bg-secondary);
        border-radius: 3px;
        overflow: hidden;
    }

    .xp-fill {
        height: 100%;
        background: linear-gradient(90deg, #6c63ff, #a855f7);
        transition: width 0.3s;
    }

    .xp-cell {
        color: var(--text-secondary);
    }

    .seasonal-badge {
        background: rgba(255, 215, 0, 0.15);
        color: #ffd700;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .coins-display {
        color: #fbbf24;
        font-weight: 600;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem !important;
    }

    .empty-content {
        color: var(--text-secondary);
    }

    .empty-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        color: var(--accent);
    }

    .empty-content h3 {
        margin-bottom: 0.5rem;
        color: var(--text-primary);
    }
</style>
@endsection
