@extends('layouts.app')

@section('title', 'Invite Tracker - SantuyTL')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-link"></i> Invite Tracker</h1>
    <p class="page-subtitle">Leaderboard member yang paling aktif mengundang orang ke server.</p>
</div>

<div class="card">
    <div class="table-container">
        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>User</th>
                    <th>Regular</th>
                    <th>Fake</th>
                    <th>Left</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inviters as $index => $inviter)
                <tr class="leaderboard-row {{ $index < 3 ? 'top-three' : '' }}">
                    <td class="rank-cell">
                        @if($index == 0)
                            <div class="rank-medal gold">
                                <span class="medal-icon"><i class="fa-solid fa-crown"></i></span>
                            </div>
                        @elseif($index == 1)
                            <div class="rank-medal silver">
                                <span class="medal-icon"><i class="fa-solid fa-medal"></i></span>
                            </div>
                        @elseif($index == 2)
                            <div class="rank-medal bronze">
                                <span class="medal-icon"><i class="fa-solid fa-award"></i></span>
                            </div>
                        @else
                            <span class="rank-number-plain">{{ $index + 1 }}</span>
                        @endif
                    </td>
                    <td class="player-cell">
                        <div class="player-avatar">
                            @if($inviter->avatar)
                                <img src="https://cdn.discordapp.com/avatars/{{ $inviter->inviter_id }}/{{ $inviter->avatar }}.png" alt="{{ $inviter->username }}" style="width: 100%; height: 100%; border-radius: 50%;">
                            @else
                                {{ strtoupper(substr($inviter->username ?? 'U', 0, 1)) }}
                            @endif
                        </div>
                        <span class="player-name">{{ $inviter->username ?? 'Unknown' }}</span>
                    </td>
                    <td><span class="badge badge-success">{{ number_format($inviter->regular) }}</span></td>
                    <td><span class="badge badge-warning">{{ number_format($inviter->fake) }}</span></td>
                    <td><span class="badge badge-danger">{{ number_format($inviter->left_count) }}</span></td>
                    <td class="total-cell">{{ number_format($inviter->total) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="empty-state">
                        <div class="empty-content">
                            <div class="empty-icon"><i class="fa-solid fa-users-slash"></i></div>
                            <h3>Belum Ada Data Invite</h3>
                            <p>Data invite akan muncul saat bot mendeteksi member baru yang join via invite link.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    /* Reuse Leaderboard Styles */
    .page-header { margin-bottom: 2rem; }
    .page-subtitle { color: var(--text-secondary); margin-top: 0.25rem; }
    
    .leaderboard-table { width: 100%; }
    .leaderboard-row { transition: all 0.2s; }
    .leaderboard-row:hover { background: rgba(108, 99, 255, 0.1); }
    .leaderboard-row.top-three { background: rgba(108, 99, 255, 0.05); }

    .rank-cell { width: 60px; text-align: center; }
    .rank-medal { display: inline-flex; justify-content: center; }
    .medal-icon { font-size: 1.5rem; }
    .rank-medal.gold .medal-icon { color: #fbbf24; }
    .rank-medal.silver .medal-icon { color: #94a3b8; }
    .rank-medal.bronze .medal-icon { color: #cd7f32; }
    .rank-number-plain { font-size: 1.1rem; font-weight: 600; color: var(--text-secondary); }

    .player-cell { display: flex; align-items: center; gap: 0.75rem; }
    .player-avatar { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #6c63ff, #a855f7); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; overflow: hidden; }
    .player-name { font-weight: 600; }

    .badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600; }
    .badge-success { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .badge-warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .badge-danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

    .total-cell { font-weight: 700; color: var(--accent); }

    .empty-state { text-align: center; padding: 4rem 2rem !important; }
    .empty-content { color: var(--text-secondary); }
    .empty-icon { font-size: 4rem; margin-bottom: 1rem; color: var(--accent); }
</style>
@endsection
