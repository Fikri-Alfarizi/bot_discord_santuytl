@extends('layouts.app')

@section('title', 'Admin - Analytics')

@section('content')
<div class="page-header">
    <h1 class="page-title">📊 Analytics</h1>
    <p class="page-subtitle">Bot usage statistics and insights</p>
</div>

{{-- User Stats --}}
<div class="stats-grid">
    <div class="card stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-content">
            <div class="card-title">Total Users</div>
            <div class="card-value">{{ number_format($userStats['total']) }}</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-content">
            <div class="card-title">With Coins</div>
            <div class="card-value">{{ number_format($userStats['with_coins']) }}</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon">⭐</div>
        <div class="stat-content">
            <div class="card-title">Level 10+</div>
            <div class="card-value">{{ number_format($userStats['high_level']) }}</div>
        </div>
    </div>
</div>

{{-- Guild Stats --}}
<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="card stat-card">
        <div class="stat-icon">🎮</div>
        <div class="stat-content">
            <div class="card-title">Total Guilds</div>
            <div class="card-value">{{ number_format($guildStats['total']) }}</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon">👋</div>
        <div class="stat-content">
            <div class="card-title">With Welcome</div>
            <div class="card-value">{{ $guildStats['with_welcome'] }}</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon">📰</div>
        <div class="stat-content">
            <div class="card-title">With News</div>
            <div class="card-value">{{ $guildStats['with_news'] }}</div>
        </div>
    </div>
</div>

<div class="analytics-grid">
    {{-- Level Distribution --}}
    <div class="card">
        <h2 style="margin-bottom: 1rem;">📈 Level Distribution</h2>
        <div class="level-chart">
            @php $maxCount = $levelDistribution->max('count') ?: 1; @endphp
            @foreach($levelDistribution->take(15) as $level)
            <div class="level-bar-container">
                <span class="level-label">Lv.{{ $level->level }}</span>
                <div class="level-bar-bg">
                    <div class="level-bar" style="width: {{ ($level->count / $maxCount) * 100 }}%"></div>
                </div>
                <span class="level-count">{{ $level->count }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Top Players --}}
    <div class="card">
        <h2 style="margin-bottom: 1rem;">🏆 Top Players</h2>
        
        <h3 class="subsection">By XP</h3>
        <div class="top-list">
            @foreach($topByXP as $user)
            <div class="top-item">
                <span class="top-name">{{ $user->username ?? 'Unknown' }}</span>
                <span class="top-value">{{ number_format($user->xp) }} XP</span>
            </div>
            @endforeach
        </div>

        <h3 class="subsection">By Coins</h3>
        <div class="top-list">
            @foreach($topByCoins as $user)
            <div class="top-item">
                <span class="top-name">{{ $user->username ?? 'Unknown' }}</span>
                <span class="top-value">💰 {{ number_format($user->coins) }}</span>
            </div>
            @endforeach
        </div>

        <h3 class="subsection">By Level</h3>
        <div class="top-list">
            @foreach($topByLevel as $user)
            <div class="top-item">
                <span class="top-name">{{ $user->username ?? 'Unknown' }}</span>
                <span class="top-value">Lv.{{ $user->level }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .stat-card { display: flex; align-items: center; gap: 1rem; }
    .stat-icon { font-size: 2rem; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; background: rgba(108, 99, 255, 0.15); border-radius: 10px; }
    .analytics-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    @media (max-width: 1024px) { .analytics-grid { grid-template-columns: 1fr; } }
    
    .level-chart { display: flex; flex-direction: column; gap: 0.5rem; }
    .level-bar-container { display: flex; align-items: center; gap: 0.5rem; }
    .level-label { min-width: 50px; font-size: 0.8rem; color: var(--text-secondary); }
    .level-bar-bg { flex: 1; height: 20px; background: var(--bg-secondary); border-radius: 10px; overflow: hidden; }
    .level-bar { height: 100%; background: linear-gradient(90deg, #6c63ff, #a855f7); border-radius: 10px; transition: width 0.3s; }
    .level-count { min-width: 40px; text-align: right; font-size: 0.8rem; font-weight: 600; }
    
    .subsection { font-size: 0.85rem; color: var(--text-secondary); margin: 1rem 0 0.5rem; }
    .top-list { display: flex; flex-direction: column; gap: 0.35rem; }
    .top-item { display: flex; justify-content: space-between; padding: 0.4rem 0.75rem; background: var(--bg-secondary); border-radius: 6px; font-size: 0.85rem; }
    .top-name { font-weight: 500; }
    .top-value { color: var(--accent); font-weight: 600; }
</style>
@endsection
