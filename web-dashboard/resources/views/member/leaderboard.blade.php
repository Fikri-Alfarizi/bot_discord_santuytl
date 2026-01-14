@extends('layouts.member')

@section('title', 'Hall of Fame')

@section('content')
<div class="leaderboard-header">
    <h1 class="page-title"><i class="fa-solid fa-trophy"></i> Hall of Fame</h1>
    <p>The mightiest heroes of Santuy World!</p>
</div>

<div class="leaderboard-container">
    {{-- Top 3 Podium --}}
    <div class="podium">
        @if(count($topUsers) > 1)
        <div class="podium-item second">
            <div class="podium-avatar">
                <img src="{{ $topUsers[1]->avatar ?? 'https://cdn.discordapp.com/embed/avatars/1.png' }}" alt="Avatar">
                <div class="podium-rank">2</div>
            </div>
            <div class="podium-name">{{ $topUsers[1]->username ?? 'Player 2' }}</div>
            <div class="podium-score">{{ number_format($topUsers[1]->xp ?? 0) }} XP</div>
        </div>
        @endif

        @if(count($topUsers) > 0)
        <div class="podium-item first">
            <div class="icon-crown"><i class="fa-solid fa-crown"></i></div>
            <div class="podium-avatar">
                <img src="{{ $topUsers[0]->avatar ?? 'https://cdn.discordapp.com/embed/avatars/0.png' }}" alt="Avatar">
                <div class="podium-rank">1</div>
            </div>
            <div class="podium-name">{{ $topUsers[0]->username ?? 'Player 1' }}</div>
            <div class="podium-score">{{ number_format($topUsers[0]->xp ?? 0) }} XP</div>
        </div>
        @endif

        @if(count($topUsers) > 2)
        <div class="podium-item third">
            <div class="podium-avatar">
                <img src="{{ $topUsers[2]->avatar ?? 'https://cdn.discordapp.com/embed/avatars/2.png' }}" alt="Avatar">
                <div class="podium-rank">3</div>
            </div>
            <div class="podium-name">{{ $topUsers[2]->username ?? 'Player 3' }}</div>
            <div class="podium-score">{{ number_format($topUsers[2]->xp ?? 0) }} XP</div>
        </div>
        @endif
    </div>

    {{-- Rest of the list --}}
    <div class="rank-list">
        @foreach($topUsers->slice(3) as $key => $user)
        <div class="rank-item">
            <div class="rank-number">#{{ $loop->iteration + 3 }}</div>
            <div class="rank-user">
                <img src="{{ $user->avatar ?? 'https://cdn.discordapp.com/embed/avatars/0.png' }}" class="rank-avatar">
                <span class="rank-name">{{ $user->username }}</span>
            </div>
            <div class="rank-stats">
                <span class="rank-level">Lvl {{ $user->level }}</span>
                <span class="rank-xp">{{ number_format($user->xp) }} XP</span>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@section('styles')
<style>
    .leaderboard-header { text-align: center; margin-bottom: 3rem; }
    .leaderboard-container { max-width: 800px; margin: 0 auto; }

    /* Podium */
    .podium {
        display: flex;
        justify-content: center;
        align-items: flex-end; /* Align bottom */
        gap: 1rem;
        margin-bottom: 3rem;
        min-height: 250px;
    }

    .podium-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        position: relative;
    }

    .podium-avatar { position: relative; margin-bottom: 0.5rem; transition: transform 0.3s var(--bounce); }
    .podium-avatar img { width: 80px; height: 80px; border-radius: 50%; border: 4px solid var(--bg-card); box-shadow: var(--shadow-soft); }
    
    .podium-item.first .podium-avatar img { width: 100px; height: 100px; border-color: var(--cute-yellow); }
    .podium-item.second .podium-avatar img { border-color: #94a3b8; }
    .podium-item.third .podium-avatar img { border-color: #b45309; }

    .podium-rank {
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 30px;
        height: 30px;
        border-radius: 50%;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.9rem;
        border: 2px solid var(--bg-card);
    }

    .podium-item.first .podium-rank { background: var(--cute-yellow); }
    .podium-item.second .podium-rank { background: #94a3b8; }
    .podium-item.third .podium-rank { background: #b45309; }
    
    .icon-crown {
        color: var(--cute-yellow);
        font-size: 2rem;
        margin-bottom: 0.5rem;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    .podium-name { font-weight: 700; margin-top: 1rem; font-size: 1.1rem; }
    .podium-score { color: var(--text-muted); font-size: 0.9rem; }
    
    .podium-item.first { order: 2; transform: scale(1.1); z-index: 2; margin-bottom: 20px; }
    .podium-item.second { order: 1; }
    .podium-item.third { order: 3; }

    /* Rank List */
    .rank-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        background: var(--bg-card);
        padding: 1.5rem;
        border-radius: var(--radius-l);
        border: 1px solid rgba(255,255,255,0.05);
    }

    .rank-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: rgba(255,255,255,0.03);
        border-radius: var(--radius-m);
        transition: transform 0.2s;
    }

    .rank-item:hover { transform: translateX(5px); background: rgba(255,255,255,0.05); }

    .rank-number {
        width: 40px;
        font-weight: 800;
        color: var(--text-muted);
        font-size: 1.1rem;
    }

    .rank-user { display: flex; align-items: center; gap: 1rem; flex: 1; }
    .rank-avatar { width: 40px; height: 40px; border-radius: 50%; }
    .rank-name { font-weight: 700; }

    .rank-stats { text-align: right; }
    .rank-level { display: block; font-size: 0.8rem; color: var(--cute-pink); font-weight: 700; }
    .rank-xp { display: block; font-size: 0.9rem; color: var(--text-muted); }

    @media (max-width: 600px) {
        .podium { gap: 0.5rem; }
        .podium-avatar img { width: 60px; height: 60px; }
        .podium-item.first .podium-avatar img { width: 80px; height: 80px; }
    }
</style>
@endsection
