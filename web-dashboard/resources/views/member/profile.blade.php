@extends('layouts.member')

@section('title', 'My Profile')

@section('content')
<div class="profile-container">
    {{-- Profile Header --}}
    <div class="user-header">
        <div class="avatar-wrapper">
            @if($user->avatar)
                <img src="{{ $user->avatar }}" class="main-avatar">
            @else
                <div class="main-avatar placeholder">{{ substr($user->name, 0, 1) }}</div>
            @endif
            <div class="status-indicator online"></div>
        </div>
        <div class="user-details">
            <h1 class="username">{{ $user->name }}</h1>
            <div class="usertag">Member since {{ $user->created_at->format('M Y') }}</div>
            <div class="badges">
                <span class="badge admin"><i class="fa-solid fa-crown"></i> Admin</span>
                <span class="badge early"><i class="fa-solid fa-star"></i> Early Supporter</span>
            </div>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="profile-grid">
        <div class="profile-card main-stats">
            <h2>Statistics</h2>
            <div class="stat-list">
                <div class="stat-item">
                    <span class="label">Level</span>
                    <span class="value">{{ $user->level ?? 1 }}</span>
                </div>
                <div class="stat-item">
                    <span class="label">Total XP</span>
                    <span class="value">{{ number_format($user->xp ?? 0) }}</span>
                </div>
                <div class="stat-item">
                    <span class="label">Balance</span>
                    <span class="value accent">{{ number_format($user->coins ?? 0) }} <i class="fa-solid fa-coins"></i></span>
                </div>
                <div class="stat-item">
                    <span class="label">Messages</span>
                    <span class="value">1,240</span> {{-- Mock --}}
                </div>
            </div>
        </div>

        <div class="profile-card inventory-preview">
            <h2>Inventory (3)</h2>
            <div class="inventory-grid">
                <div class="inv-slot" title="VIP Ticket"><i class="fa-solid fa-ticket"></i></div>
                <div class="inv-slot" title="Fishing Rod"><i class="fa-solid fa-fish"></i></div>
                <div class="inv-slot" title="Mystery Box"><i class="fa-solid fa-box"></i></div>
                <div class="inv-slot empty"></div>
                <div class="inv-slot empty"></div>
                <div class="inv-slot empty"></div>
            </div>
            <button class="btn-cute btn-sm" style="margin-top: 1rem; width: 100%;">View All</button>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .profile-container { max-width: 800px; margin: 0 auto; }

    .user-header {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        margin-bottom: 3rem;
        background: var(--bg-card);
        padding: 3rem 2rem;
        border-radius: var(--radius-l);
        border: 1px solid rgba(255,255,255,0.05);
        position: relative;
        overflow: hidden;
    }
    
    .user-header::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 100px;
        background: linear-gradient(135deg, var(--cute-pink), var(--cute-purple));
        opacity: 0.2;
    }

    .avatar-wrapper {
        position: relative;
        margin-bottom: 1rem;
        z-index: 1;
    }

    .main-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 4px solid var(--bg-card);
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    
    .main-avatar.placeholder {
        background: var(--cute-purple);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: 800;
        color: #fff;
    }

    .status-indicator {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 3px solid var(--bg-card);
    }

    .status-indicator.online { background: #4ade80; }

    .username { font-size: 2rem; font-weight: 900; margin-bottom: 0.25rem; }
    .usertag { color: var(--text-muted); margin-bottom: 1rem; }

    .badges { display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap; }
    .badge {
        background: rgba(255,255,255,0.05);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border: 1px solid rgba(255,255,255,0.05);
    }

    .badge.admin { background: rgba(235, 146, 190, 0.1); color: var(--cute-pink); border-color: rgba(235, 146, 190, 0.2); }
    .badge.early { background: rgba(246, 193, 119, 0.1); color: var(--cute-yellow); border-color: rgba(246, 193, 119, 0.2); }

    .profile-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 1.5rem;
    }

    .profile-card {
        background: var(--bg-card);
        border-radius: var(--radius-l);
        padding: 2rem;
        border: 1px solid rgba(255,255,255,0.05);
    }
    
    .profile-card h2 { margin-bottom: 1.5rem; font-size: 1.25rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }

    .stat-list { display: flex; flex-direction: column; gap: 1rem; }
    .stat-item {
        display: flex;
        justify-content: space-between;
        padding: 1rem;
        background: rgba(255,255,255,0.03);
        border-radius: var(--radius-m);
        align-items: center;
    }
    
    .stat-item .label { color: var(--text-muted); font-weight: 600; }
    .stat-item .value { font-weight: 800; font-size: 1.1rem; }
    .stat-item .value.accent { color: var(--cute-yellow); }

    .inventory-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
    }

    .inv-slot {
        background: rgba(255,255,255,0.03);
        aspect-ratio: 1;
        border-radius: var(--radius-s);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: var(--text-muted);
        transition: all 0.2s;
        border: 2px solid transparent;
    }
    
    .inv-slot:not(.empty):hover {
        background: rgba(255,255,255,0.1);
        color: var(--cute-blue);
        border-color: var(--cute-blue);
        transform: scale(1.05);
    }
    
    .inv-slot.empty { opacity: 0.3; border: 2px dashed rgba(255,255,255,0.1); }

    @media (max-width: 768px) {
        .profile-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection
