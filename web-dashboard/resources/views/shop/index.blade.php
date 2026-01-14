@extends('layouts.app')

@section('title', 'Shop & Premium - SantuyTL')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-cart-shopping text-accent"></i> Shop Ecosystem</h1>
    <p class="page-subtitle">Beli Coin atau Gunakan Coin untuk Upgrade Server menjadi Pro/Premium.</p>
</div>

<div class="app-layout">
    {{-- Wallet & Status --}}
    <div class="card status-card">
        <div class="wallet-info">
            <div class="label">Your Wallet</div>
            <div class="amount"><i class="fa-solid fa-coins text-warning"></i> {{ number_format($userCoins) }} Coins</div>
        </div>
        <div class="server-status">
            <div class="label">Server Status</div>
            @if($proExpiresAt && \Carbon\Carbon::parse($proExpiresAt)->isFuture())
                <div class="amount text-success"><i class="fa-solid fa-crown"></i> PRO Active</div>
                <div class="expiry">Expires: {{ \Carbon\Carbon::parse($proExpiresAt)->format('d M Y H:i') }}</div>
            @else
                <div class="amount text-secondary"><i class="fa-solid fa-user"></i> Free Plan</div>
                <div class="expiry">Upgrade to unlock full potential</div>
            @endif
        </div>
    </div>

    {{-- Tabs --}}
    <div class="shop-tabs">
        <button class="shop-tab active" onclick="switchTab('pro')"><i class="fa-solid fa-crown"></i> Buy Pro Subscription</button>
        <button class="shop-tab" onclick="switchTab('coins')"><i class="fa-solid fa-coins"></i> Buy Coins</button>
    </div>

    {{-- Pro Packages --}}
    <div id="tab-pro" class="shop-grid">
        @foreach($proPackages as $pkg)
        <div class="shop-card pro">
            <div class="card-icon"><i class="fa-solid fa-crown"></i></div>
            <div class="card-title">{{ $pkg['name'] }}</div>
            <div class="card-price"><i class="fa-solid fa-coins"></i> {{ number_format($pkg['price_coins']) }}</div>
            <div class="card-desc">Unlock all Premium features for {{ $pkg['duration_days'] }} days.</div>
            <form action="{{ route('shop.buyPro', ['guildId' => $guildId]) }}" method="POST">
                @csrf
                <input type="hidden" name="package_id" value="{{ $pkg['id'] }}">
                <button type="submit" class="btn btn-pro" {{ $userCoins < $pkg['price_coins'] ? 'disabled' : '' }}>
                    {{ $userCoins < $pkg['price_coins'] ? 'Not enough coins' : 'Activate Now' }}
                </button>
            </form>
        </div>
        @endforeach
    </div>

    {{-- Coin Packages --}}
    <div id="tab-coins" class="shop-grid" style="display: none;">
        @foreach($coinPackages as $pkg)
        <div class="shop-card coin">
            <div class="card-icon text-warning"><i class="fa-solid {{ $pkg['fa_icon'] }}"></i></div>
            <div class="card-title">{{ $pkg['name'] }}</div>
            <div class="card-price">{{ $pkg['price'] }}</div>
            <div class="card-desc">Get {{ number_format($pkg['coins']) }} coins instantly.</div>
            <form action="{{ route('shop.buyCoins', ['guildId' => $guildId]) }}" method="POST">
                @csrf
                <input type="hidden" name="package_id" value="{{ $pkg['id'] }}">
                <button type="submit" class="btn btn-primary">Purchase</button>
            </form>
        </div>
        @endforeach
    </div>

</div>

<script>
    function switchTab(tab) {
        document.querySelectorAll('.shop-tab').forEach(t => t.classList.remove('active'));
        event.target.closest('.shop-tab').classList.add('active');
        
        document.getElementById('tab-pro').style.display = tab === 'pro' ? 'grid' : 'none';
        document.getElementById('tab-coins').style.display = tab === 'coins' ? 'grid' : 'none';
    }
</script>

<style>
    .status-card { display: flex; justify-content: space-between; align-items: center; background: linear-gradient(135deg, #1f2937, #111827); border: 1px solid var(--border); margin-bottom: 2rem; }
    .wallet-info, .server-status { text-align: center; flex: 1; }
    .wallet-info { border-right: 1px solid var(--border); }
    .label { font-size: 0.85rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem; }
    .amount { font-size: 1.5rem; font-weight: 700; color: white; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
    .expiry { font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.25rem; }
    .text-warning { color: #f59e0b; }
    .text-success { color: #10b981; }
    .text-accent { color: #6c63ff; }

    .shop-tabs { display: flex; gap: 1rem; margin-bottom: 1.5rem; }
    .shop-tab { flex: 1; padding: 1rem; border: 1px solid var(--border); background: var(--bg-secondary); color: var(--text-secondary); border-radius: 12px; cursor: pointer; font-weight: 600; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
    .shop-tab:hover { background: var(--bg-card); color: white; }
    .shop-tab.active { background: var(--accent); color: white; border-color: var(--accent); }

    .shop-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; }
    .shop-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px; padding: 2rem; text-align: center; transition: transform 0.2s; position: relative; overflow: hidden; }
    .shop-card:hover { transform: translateY(-5px); border-color: var(--accent); }
    
    .shop-card.pro { border-top: 4px solid var(--accent); }
    .shop-card.coin { border-top: 4px solid #f59e0b; }

    .card-icon { font-size: 2.5rem; margin-bottom: 1rem; }
    .shop-card.pro .card-icon { color: var(--accent); }
    .card-title { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; }
    .card-price { font-size: 1.5rem; font-weight: 800; color: white; margin-bottom: 1rem; }
    .card-desc { color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1.5rem; min-height: 40px; }

    .btn { width: 100%; padding: 0.75rem; border-radius: 8px; font-weight: 600; cursor: pointer; border: none; transition: all 0.2s; }
    .btn-primary { background: #f59e0b; color: #111; }
    .btn-primary:hover { background: #d97706; }
    .btn-pro { background: var(--accent); color: white; }
    .btn-pro:hover { background: #5a52d5; }
    .btn:disabled { background: #374151; color: #9ca3af; cursor: not-allowed; }
</style>
@endsection
