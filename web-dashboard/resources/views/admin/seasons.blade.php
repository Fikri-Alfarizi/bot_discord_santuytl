@extends('layouts.app')

@section('title', 'Admin - Seasons')

@section('content')
<div class="page-header">
    <h1 class="page-title">🏆 Season Manager</h1>
    <p class="page-subtitle">Manage seasonal competitions</p>
</div>

<div class="seasons-grid">
    {{-- Current Season Status --}}
    <div class="card current-season">
        @if($currentSeason)
        <div class="season-badge active">ACTIVE</div>
        <h2>{{ $currentSeason->name }}</h2>
        <div class="season-info">
            <div class="season-stat">
                <span class="label">Season #</span>
                <span class="value">{{ $currentSeason->season_number }}</span>
            </div>
            <div class="season-stat">
                <span class="label">Started</span>
                <span class="value">{{ date('d M Y', $currentSeason->start_date) }}</span>
            </div>
            <div class="season-stat">
                <span class="label">Duration</span>
                <span class="value">{{ floor((time() - $currentSeason->start_date) / 86400) }} days</span>
            </div>
        </div>
        <form action="{{ route('admin.seasons.end') }}" method="POST" 
              onsubmit="return confirm('End current season? This will finalize rankings.')">
            @csrf
            <button type="submit" class="btn btn-danger" style="width: 100%; margin-top: 1rem;">🏁 End Season</button>
        </form>
        @else
        <div class="season-badge inactive">NO ACTIVE SEASON</div>
        <h2>Start a New Season</h2>
        <form action="{{ route('admin.seasons.start') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Season Name</label>
                <input type="text" name="name" class="form-input" placeholder="e.g. Winter 2024" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">🚀 Start Season</button>
        </form>
        @endif
    </div>

    {{-- Top Players This Season --}}
    <div class="card">
        <h2 style="margin-bottom: 1rem;">🔥 Season Leaderboard</h2>
        @if(count($topPlayers) > 0)
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Player</th>
                        <th>Seasonal XP</th>
                        <th>Level</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topPlayers as $index => $player)
                    <tr>
                        <td>
                            @if($index == 0) 🥇
                            @elseif($index == 1) 🥈
                            @elseif($index == 2) 🥉
                            @else {{ $index + 1 }}
                            @endif
                        </td>
                        <td><strong>{{ $player->username ?? 'Unknown' }}</strong></td>
                        <td>✨ {{ number_format($player->seasonal_xp) }}</td>
                        <td>Lv.{{ $player->level }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="empty-state">
            <p>No seasonal data yet. Start a season to begin tracking!</p>
        </div>
        @endif
    </div>
</div>

{{-- Season History --}}
<div class="card" style="margin-top: 1.5rem;">
    <h2 style="margin-bottom: 1rem;">📜 Season History</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allSeasons as $season)
                <tr>
                    <td>{{ $season->season_number }}</td>
                    <td><strong>{{ $season->name }}</strong></td>
                    <td>{{ $season->start_date ? date('d M Y', $season->start_date) : '-' }}</td>
                    <td>{{ $season->end_date ? date('d M Y', $season->end_date) : '-' }}</td>
                    <td>
                        @if($season->is_active)
                            <span class="status-badge active">Active</span>
                        @else
                            <span class="status-badge ended">Ended</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="empty-state">No seasons yet</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    .seasons-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    @media (max-width: 1024px) { .seasons-grid { grid-template-columns: 1fr; } }
    .current-season { position: relative; }
    .season-badge { position: absolute; top: 1rem; right: 1rem; padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.7rem; font-weight: 700; }
    .season-badge.active { background: rgba(72, 187, 120, 0.2); color: var(--success); }
    .season-badge.inactive { background: rgba(160, 174, 192, 0.2); color: var(--text-secondary); }
    .season-info { display: flex; flex-wrap: wrap; gap: 1.5rem; margin-top: 1rem; }
    .season-stat { display: flex; flex-direction: column; }
    .season-stat .label { font-size: 0.75rem; color: var(--text-secondary); }
    .season-stat .value { font-size: 1.25rem; font-weight: 700; }
    .status-badge { padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.7rem; font-weight: 600; }
    .status-badge.active { background: rgba(72, 187, 120, 0.2); color: var(--success); }
    .status-badge.ended { background: rgba(160, 174, 192, 0.2); color: var(--text-secondary); }
    .empty-state { text-align: center; padding: 2rem; color: var(--text-secondary); }
    .btn-danger { background: var(--danger); color: white; }
</style>
@endsection
