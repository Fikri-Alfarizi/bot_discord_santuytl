@extends('layouts.app')

@section('title', 'Admin - Economy')

@section('content')
<div class="page-header">
    <h1 class="page-title">💰 Economy Controls</h1>
    <p class="page-subtitle">Manage coins and economy settings</p>
</div>

{{-- Stats Cards --}}
<div class="stats-grid">
    <div class="card stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-content">
            <div class="card-title">Total Coins</div>
            <div class="card-value">{{ number_format($stats['total_coins']) }}</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon">⭐</div>
        <div class="stat-content">
            <div class="card-title">Total XP</div>
            <div class="card-value">{{ number_format($stats['total_xp']) }}</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon">💵</div>
        <div class="stat-content">
            <div class="card-title">Avg Coins/User</div>
            <div class="card-value">{{ number_format($stats['avg_coins']) }}</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon">📊</div>
        <div class="stat-content">
            <div class="card-title">Avg Level</div>
            <div class="card-value">{{ $stats['avg_level'] }}</div>
        </div>
    </div>
</div>

<div class="economy-grid">
    {{-- Give Coins to User --}}
    <div class="card">
        <h2 style="margin-bottom: 1rem;">🎁 Give Coins to User</h2>
        <form action="{{ route('admin.economy.give') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Discord User ID</label>
                <input type="text" name="user_id" class="form-input" placeholder="e.g. 1155782329332146238" required>
            </div>
            <div class="form-group">
                <label class="form-label">Amount (can be negative to remove)</label>
                <input type="number" name="amount" class="form-input" placeholder="1000" required>
            </div>
            <button type="submit" class="btn btn-primary">💰 Give Coins</button>
        </form>
    </div>

    {{-- Mass Give --}}
    <div class="card">
        <h2 style="margin-bottom: 1rem;">🌍 Mass Give Coins</h2>
        <p class="card-desc">Give coins to ALL users in the database.</p>
        <form action="{{ route('admin.economy.massGive') }}" method="POST" 
              onsubmit="return confirm('Give coins to ALL users?')">
            @csrf
            <div class="form-group">
                <label class="form-label">Amount per user (max 100,000)</label>
                <input type="number" name="amount" class="form-input" min="1" max="100000" value="1000" required>
            </div>
            <button type="submit" class="btn btn-primary">🌍 Give to All</button>
        </form>
    </div>

    {{-- Top Richest --}}
    <div class="card">
        <h2 style="margin-bottom: 1rem;">🤑 Top 10 Richest</h2>
        <div class="rich-list">
            @foreach($richestUsers as $index => $user)
            <div class="rich-item">
                <span class="rich-rank">{{ $index + 1 }}.</span>
                <span class="rich-name">{{ $user->username ?? 'Unknown' }}</span>
                <span class="rich-coins">💰 {{ number_format($user->coins) }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Danger Zone --}}
    <div class="card danger-zone">
        <h2 style="margin-bottom: 1rem;">⚠️ Danger Zone</h2>
        <p class="card-desc">Reset ALL economy data. This cannot be undone!</p>
        <form action="{{ route('admin.economy.reset') }}" method="POST" 
              onsubmit="return prompt('Type RESET to confirm') === 'RESET'">
            @csrf
            <input type="hidden" name="confirm" value="RESET">
            <button type="submit" class="btn btn-danger">🗑️ Reset All Economy</button>
        </form>
    </div>
</div>

<style>
    .stat-card { display: flex; align-items: center; gap: 1rem; }
    .stat-icon { font-size: 2rem; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; background: rgba(108, 99, 255, 0.15); border-radius: 10px; }
    .economy-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
    @media (max-width: 1024px) { .economy-grid { grid-template-columns: 1fr; } }
    .card-desc { color: var(--text-secondary); margin-bottom: 1rem; font-size: 0.9rem; }
    .rich-list { display: flex; flex-direction: column; gap: 0.5rem; }
    .rich-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem; background: var(--bg-secondary); border-radius: 8px; }
    .rich-rank { font-weight: 600; color: var(--accent); min-width: 25px; }
    .rich-name { flex: 1; }
    .rich-coins { color: #fbbf24; font-weight: 600; }
    .danger-zone { border-color: var(--danger); }
    .danger-zone h2 { color: var(--danger); }
    .btn-danger { background: var(--danger); color: white; }
</style>
@endsection
