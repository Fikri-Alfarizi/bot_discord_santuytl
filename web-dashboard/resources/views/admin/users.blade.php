@extends('layouts.app')

@section('title', 'Admin - Users')

@section('content')
<div class="page-header">
    <h1 class="page-title">👥 User Management</h1>
    <p class="page-subtitle">Manage all bot users</p>
</div>

{{-- Stats Cards --}}
<div class="stats-grid">
    <div class="card stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-content">
            <div class="card-title">Total Users</div>
            <div class="card-value">{{ number_format($stats['total']) }}</div>
        </div>
    </div>
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
        <div class="stat-icon">📈</div>
        <div class="stat-content">
            <div class="card-title">Avg Level</div>
            <div class="card-value">{{ $stats['avg_level'] }}</div>
        </div>
    </div>
</div>

{{-- Search & Filter --}}
<div class="card" style="margin-bottom: 1.5rem;">
    <form action="{{ route('admin.users') }}" method="GET" class="search-form">
        <div class="search-row">
            <input type="text" name="search" placeholder="Search username or ID..." 
                   value="{{ $search }}" class="form-input" style="flex: 1;">
            <select name="sort" class="form-input" style="width: auto;">
                <option value="level" {{ $sortBy == 'level' ? 'selected' : '' }}>Sort by Level</option>
                <option value="coins" {{ $sortBy == 'coins' ? 'selected' : '' }}>Sort by Coins</option>
                <option value="xp" {{ $sortBy == 'xp' ? 'selected' : '' }}>Sort by XP</option>
                <option value="username" {{ $sortBy == 'username' ? 'selected' : '' }}>Sort by Username</option>
            </select>
            <select name="order" class="form-input" style="width: auto;">
                <option value="desc" {{ $order == 'desc' ? 'selected' : '' }}>Descending</option>
                <option value="asc" {{ $order == 'asc' ? 'selected' : '' }}>Ascending</option>
            </select>
            <button type="submit" class="btn btn-primary">🔍 Search</button>
        </div>
    </form>
</div>

{{-- Users Table --}}
<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Level</th>
                    <th>XP</th>
                    <th>Coins</th>
                    <th>Seasonal XP</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td><code style="font-size: 0.75rem;">{{ $user->id }}</code></td>
                    <td><strong>{{ $user->username ?? 'Unknown' }}</strong></td>
                    <td><span class="level-badge">Lv.{{ $user->level }}</span></td>
                    <td>{{ number_format($user->xp) }}</td>
                    <td>💰 {{ number_format($user->coins) }}</td>
                    <td>✨ {{ number_format($user->seasonal_xp ?? 0) }}</td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary">✏️ Edit</a>
                            <form action="{{ route('admin.users.reset', $user->id) }}" method="POST" style="display: inline;" 
                                  onsubmit="return confirm('Reset all data for this user?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-warning">🔄 Reset</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="empty-state">No users found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="pagination-container">
        {{ $users->appends(request()->query())->links('pagination::simple-tailwind') }}
    </div>
</div>

<style>
    .stat-card {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .stat-icon {
        font-size: 2rem;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(108, 99, 255, 0.15);
        border-radius: 10px;
    }
    .search-row {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .level-badge {
        background: linear-gradient(135deg, #6c63ff, #a855f7);
        color: white;
        padding: 0.2rem 0.6rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .action-btns {
        display: flex;
        gap: 0.5rem;
    }
    .btn-sm {
        padding: 0.4rem 0.75rem;
        font-size: 0.75rem;
    }
    .btn-warning {
        background: #ed8936;
        color: white;
    }
    .pagination-container {
        margin-top: 1.5rem;
        display: flex;
        justify-content: center;
    }
    code {
        background: var(--bg-secondary);
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
    }
</style>
@endsection
