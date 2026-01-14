@extends('layouts.app')

@section('title', 'Admin - Moderation')

@section('content')
<div class="page-header">
    <h1 class="page-title">🛡️ Moderation</h1>
    <p class="page-subtitle">Trust scores and reputation management</p>
</div>

{{-- Stats --}}
<div class="stats-grid">
    <div class="card stat-card">
        <div class="stat-icon">🛡️</div>
        <div class="stat-content">
            <div class="card-title">Trust Entries</div>
            <div class="card-value">{{ number_format($stats['total_trust_entries']) }}</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon">📊</div>
        <div class="stat-content">
            <div class="card-title">Avg Trust Score</div>
            <div class="card-value">{{ $stats['avg_trust'] }}%</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon">⭐</div>
        <div class="stat-content">
            <div class="card-title">Total Rep Points</div>
            <div class="card-value">{{ number_format($stats['total_rep']) }}</div>
        </div>
    </div>
</div>

<div class="mod-grid">
    {{-- Low Trust Users --}}
    <div class="card">
        <h2 style="margin-bottom: 1rem;">⚠️ Low Trust Users</h2>
        <p class="card-desc">Users with trust score below 50%</p>
        @if(count($lowTrust) > 0)
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Score</th>
                        <th>Reason</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowTrust as $user)
                    <tr>
                        <td><code>{{ $user->user_id }}</code></td>
                        <td>
                            <span class="trust-score {{ $user->score < 30 ? 'danger' : 'warning' }}">
                                {{ $user->score }}%
                            </span>
                        </td>
                        <td>{{ $user->reason ?? '-' }}</td>
                        <td>
                            <form action="{{ route('admin.moderation.trust.reset', $user->user_id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">✨ Reset</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state success">
            <span>✅</span>
            <p>No users with low trust scores!</p>
        </div>
        @endif
    </div>

    {{-- Top Reputation --}}
    <div class="card">
        <h2 style="margin-bottom: 1rem;">⭐ Top Reputation</h2>
        <p class="card-desc">Most reputable users</p>
        @if(count($topRep) > 0)
        <div class="rep-list">
            @foreach($topRep as $index => $user)
            <div class="rep-item">
                <span class="rep-rank">{{ $index + 1 }}</span>
                <code class="rep-id">{{ substr($user->user_id, 0, 10) }}...</code>
                <span class="rep-points">⭐ {{ number_format($user->rep_points) }}</span>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <p>No reputation data yet</p>
        </div>
        @endif
    </div>
</div>

{{-- Update Trust Score --}}
<div class="card" style="margin-top: 1.5rem;">
    <h2 style="margin-bottom: 1rem;">🔧 Update Trust Score</h2>
    <form action="{{ route('admin.moderation.trust') }}" method="POST" class="trust-form">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Discord User ID</label>
                <input type="text" name="user_id" class="form-input" placeholder="1155782329332146238" required>
            </div>
            <div class="form-group">
                <label class="form-label">Trust Score (0-100)</label>
                <input type="number" name="score" class="form-input" min="0" max="100" value="100" required>
            </div>
            <div class="form-group">
                <label class="form-label">Reason (optional)</label>
                <input type="text" name="reason" class="form-input" placeholder="e.g. Spamming">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">💾 Update Trust</button>
    </form>
</div>

<style>
    .stat-card { display: flex; align-items: center; gap: 1rem; }
    .stat-icon { font-size: 2rem; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; background: rgba(108, 99, 255, 0.15); border-radius: 10px; }
    .mod-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; }
    @media (max-width: 1024px) { .mod-grid { grid-template-columns: 1fr; } }
    .card-desc { color: var(--text-secondary); margin-bottom: 1rem; font-size: 0.85rem; }
    .trust-score { padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .trust-score.danger { background: rgba(252, 129, 129, 0.2); color: var(--danger); }
    .trust-score.warning { background: rgba(237, 137, 54, 0.2); color: var(--warning); }
    .btn-sm { padding: 0.4rem 0.75rem; font-size: 0.75rem; }
    .empty-state { text-align: center; padding: 2rem; color: var(--text-secondary); }
    .empty-state.success { color: var(--success); }
    .rep-list { display: flex; flex-direction: column; gap: 0.5rem; }
    .rep-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem; background: var(--bg-secondary); border-radius: 8px; }
    .rep-rank { font-weight: 700; color: var(--accent); min-width: 25px; }
    .rep-id { font-size: 0.75rem; flex: 1; }
    .rep-points { color: #fbbf24; font-weight: 600; }
    .form-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
    @media (max-width: 768px) { .form-row { grid-template-columns: 1fr; } }
    code { background: var(--bg-secondary); padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; }
</style>
@endsection
