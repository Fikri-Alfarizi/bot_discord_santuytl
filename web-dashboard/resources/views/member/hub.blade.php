@extends('layouts.member')

@section('title', 'Santuy World - Home')

@section('content')
<div class="hub-header">
    <div class="welcome-box">
        <h1 class="welcome-title">Hai, {{ $user->name }}! 👋</h1>
        <p class="welcome-text">Selamat datang kembali di Santuy World!</p>
    </div>
    
    {{-- Quick Quick Actions --}}
    <div class="action-grid">
        <a href="{{ route('member.market') }}" class="action-card market">
            <div class="action-icon"><i class="fa-solid fa-store"></i></div>
            <div class="action-label">Market</div>
        </a>
        <a href="{{ route('member.inventory') }}" class="action-card inventory">
            <div class="action-icon"><i class="fa-solid fa-backpack"></i></div>
            <div class="action-label">Inventory</div>
        </a>
        <a href="{{ route('member.leaderboard') }}" class="action-card rank">
            <div class="action-icon"><i class="fa-solid fa-trophy"></i></div>
            <div class="action-label">Rank</div>
        </a>
        <div class="action-card daily" onclick="claimDaily()">
            <div class="action-icon"><i class="fa-solid fa-calendar-check"></i></div>
            <div class="action-label">Daily</div>
            @if($user->last_daily && (now()->timestamp * 1000 - $user->last_daily < 86400000))
            <div class="cooldown-indicator"><i class="fa-solid fa-clock"></i> Wait</div>
            @endif
        </div>
    </div>
</div>

{{-- Stats Row --}}
<div class="stats-row">
    <div class="stat-card coins">
        <div class="stat-icon"><i class="fa-solid fa-coins"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ number_format($user->coins ?? 0) }}</div>
            <div class="stat-label">Coins</div>
        </div>
    </div>
    
    <div class="stat-card level">
        <div class="stat-icon"><i class="fa-solid fa-star"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $user->level ?? 1 }}</div>
            <div class="stat-label">Level</div>
        </div>
    </div>
    
    <div class="stat-card xp">
        <div class="stat-icon"><i class="fa-solid fa-bolt"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ number_format($user->xp ?? 0) }}</div>
            <div class="stat-label">Total XP</div>
        </div>
    </div>
</div>

{{-- Events Section --}}
<h2 class="section-title"><i class="fa-solid fa-newspaper"></i> What's Happening?</h2>
<div class="events-grid">
    @foreach($news as $item)
    <div class="event-card">
        <div class="event-icon" style="background: {{ $item['color'] }}; color: #232136;">
            <i class="fa-solid {{ $item['icon'] }}"></i>
        </div>
        <div class="event-content">
            <h3>{{ $item['title'] }}</h3>
            <p>{{ $item['desc'] }}</p>
            <button class="btn-cute btn-sm" style="margin-top: 0.5rem; background: {{ $item['color'] }}">Check it out!</button>
        </div>
    </div>
    @endforeach
</div>

@endsection

@section('styles')
<style>
    .hub-header {
        margin-bottom: 2rem;
        background: linear-gradient(135deg, rgba(42, 39, 68, 0.8), rgba(42, 39, 68, 0.4));
        padding: 2rem;
        border-radius: var(--radius-l);
        border: 1px solid rgba(255,255,255,0.05);
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .welcome-title {
        font-size: 2.5rem;
        font-weight: 900;
        margin-bottom: 0.5rem;
        background: linear-gradient(135deg, #fff, var(--cute-pink));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .welcome-text { color: var(--text-muted); font-size: 1.1rem; }

    .action-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }

    .action-card {
        background: rgba(255,255,255,0.05);
        border-radius: var(--radius-m);
        padding: 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        color: var(--text-main);
        transition: all 0.3s var(--bounce);
        cursor: pointer;
        position: relative;
    }

    .action-card:hover { transform: translateY(-5px); background: rgba(255,255,255,0.1); }

    .action-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 0.25rem;
    }

    .action-card.market .action-icon { background: rgba(246, 193, 119, 0.2); color: var(--cute-yellow); }
    .action-card.inventory .action-icon { background: rgba(156, 207, 216, 0.2); color: var(--cute-blue); }
    .action-card.rank .action-icon { background: rgba(196, 167, 231, 0.2); color: var(--cute-purple); }
    .action-card.daily .action-icon { background: rgba(235, 111, 146, 0.2); color: var(--cute-red); }

    .cooldown-indicator {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(0,0,0,0.5);
        border-radius: 10px;
        padding: 2px 6px;
        font-size: 0.6rem;
        color: var(--cute-red);
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--bg-card);
        padding: 1.5rem;
        border-radius: var(--radius-l);
        display: flex;
        align-items: center;
        gap: 1rem;
        border: 1px solid rgba(255,255,255,0.05);
        transition: transform 0.3s var(--bounce);
    }
    
    .stat-card:hover { transform: translateY(-5px); }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: var(--radius-m);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-card.coins .stat-icon { background: var(--cute-yellow); color: #232136; }
    .stat-card.level .stat-icon { background: var(--cute-purple); color: #232136; }
    .stat-card.xp .stat-icon { background: var(--cute-blue); color: #232136; }

    .stat-value { font-size: 1.5rem; font-weight: 800; color: #fff; }
    .stat-label { color: var(--text-muted); font-size: 0.9rem; font-weight: 600; }

    .section-title { margin-bottom: 1.5rem; font-size: 1.5rem; display: flex; align-items: center; gap: 0.75rem; color: var(--text-main); }
    
    .events-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .event-card {
        background: var(--bg-card);
        border-radius: var(--radius-l);
        padding: 1.5rem;
        display: flex;
        gap: 1.5rem;
        border: 1px solid rgba(255,255,255,0.05);
        transition: all 0.3s;
    }
    
    .event-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-soft); }

    .event-icon {
        width: 60px;
        height: 60px;
        border-radius: var(--radius-m);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .event-content h3 { margin-bottom: 0.5rem; font-size: 1.1rem; }
    .event-content p { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.5rem; line-height: 1.5; }

    @media (max-width: 768px) {
        .hub-header { padding: 1.5rem; }
        .welcome-title { font-size: 1.75rem; }
        .action-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endsection

@section('scripts')
<script>
    async function claimDaily() {
        const btn = document.querySelector('.action-card.daily');
        if(btn.style.opacity === '0.5') return;
        
        btn.style.opacity = '0.5';
        
        try {
            const response = await fetch('{{ route('member.daily') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const result = await response.json();
            
            if(result.success) {
                alert('🎉 ' + result.message);
                location.reload();
            } else {
                alert('⏳ ' + result.message);
            }
        } catch (error) {
            alert('❌ Error: ' + error.message);
        } finally {
            btn.style.opacity = '1';
        }
    }
</script>
@endsection
