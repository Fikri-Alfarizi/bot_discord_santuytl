@extends('layouts.app')

@section('title', 'Admin - Edit User')

@section('content')
<div class="page-header">
    <h1 class="page-title">✏️ Edit User</h1>
    <p class="page-subtitle">{{ $user->username ?? $user->id }}</p>
</div>

<div class="edit-grid">
    {{-- Edit Form --}}
    <div class="card">
        <h2 style="margin-bottom: 1.5rem;">📝 User Data</h2>
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Discord ID</label>
                <input type="text" class="form-input" value="{{ $user->id }}" disabled>
            </div>

            <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-input" value="{{ $user->username }}">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Level</label>
                    <input type="number" name="level" class="form-input" value="{{ $user->level }}" min="1">
                </div>
                <div class="form-group">
                    <label class="form-label">XP</label>
                    <input type="number" name="xp" class="form-input" value="{{ $user->xp }}" min="0">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Coins 💰</label>
                    <input type="number" name="coins" class="form-input" value="{{ $user->coins }}" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Seasonal XP ✨</label>
                    <input type="number" name="seasonal_xp" class="form-input" value="{{ $user->seasonal_xp ?? 0 }}" min="0">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Save Changes</button>
                <a href="{{ route('admin.users') }}" class="btn" style="background: var(--bg-secondary);">← Back</a>
            </div>
        </form>
    </div>

    {{-- User Stats --}}
    <div class="stats-panel">
        <div class="card">
            <h3>📊 Quick Stats</h3>
            <div class="stat-item">
                <span>Level</span>
                <strong>{{ $user->level }}</strong>
            </div>
            <div class="stat-item">
                <span>Total XP</span>
                <strong>{{ number_format($user->xp) }}</strong>
            </div>
            <div class="stat-item">
                <span>Coins</span>
                <strong>💰 {{ number_format($user->coins) }}</strong>
            </div>
            <div class="stat-item">
                <span>Job</span>
                <strong>{{ $user->job ?? 'Pengangguran' }}</strong>
            </div>
        </div>

        <div class="card danger-zone">
            <h3>⚠️ Danger Zone</h3>
            <form action="{{ route('admin.users.reset', $user->id) }}" method="POST" 
                  onsubmit="return confirm('Reset ALL data for this user? This cannot be undone!')">
                @csrf
                <button type="submit" class="btn btn-danger" style="width: 100%;">🔄 Reset User Data</button>
            </form>
            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="margin-top: 0.75rem;"
                  onsubmit="return confirm('DELETE this user permanently? This cannot be undone!')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" style="width: 100%;">🗑️ Delete User</button>
            </form>
        </div>
    </div>
</div>

<style>
    .edit-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
    }
    @media (max-width: 1024px) {
        .edit-grid { grid-template-columns: 1fr; }
    }
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    .form-actions {
        display: flex;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }
    .stats-panel {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .stat-item {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--border);
    }
    .stat-item:last-child { border-bottom: none; }
    .danger-zone {
        border-color: var(--danger);
    }
    .danger-zone h3 { color: var(--danger); }
    .btn-danger {
        background: var(--danger);
        color: white;
    }
</style>
@endsection
