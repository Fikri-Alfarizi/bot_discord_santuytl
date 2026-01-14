@extends('layouts.member')

@section('title', 'My Inventory')

@section('content')
<div class="inventory-header">
    <h1 class="page-title"><i class="fa-solid fa-backpack"></i> Inventory</h1>
    <p>Your collection of awesome items!</p>
</div>

@if(count($items) > 0)
<div class="inventory-grid">
    @foreach($items as $item)
    <div class="item-card inv-card" style="border-top: 4px solid {{ $item->color ?? '#64748b' }}">
        <div class="item-icon" style="color: {{ $item->color ?? '#64748b' }}; background: rgba(255,255,255,0.05);">
            <i class="fa-solid {{ $item->icon ?? 'fa-cube' }}"></i>
        </div>
        <div class="item-info">
            <h3>{{ $item->name }}</h3>
            <p>{{ $item->description }}</p>
            @if(isset($item->expires_at))
            <div class="item-expiry">
                <i class="fa-solid fa-clock"></i> Expires: {{ date('d M Y H:i', $item->expires_at/1000) }}
            </div>
            @endif
            <button class="btn-cute btn-sm" style="background: {{ $item->color ?? '#64748b' }}; width: 100%; margin-top: auto;">Use Item</button>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="empty-state">
    <i class="fa-solid fa-box-open"></i>
    <h2>Your backpack is empty!</h2>
    <p>Go to the <a href="{{ route('member.market') }}">Market</a> to buy some cool stuff.</p>
</div>
@endif

@endsection

@section('styles')
<style>
    .inventory-header { text-align: center; margin-bottom: 3rem; }
    
    .inventory-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 2rem;
    }

    .inv-card {
        background: var(--bg-card);
        border-radius: var(--radius-l);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        transition: transform 0.3s var(--bounce);
        min-height: 300px; /* Uniform height */
    }
    
    .inv-card:hover { transform: translateY(-5px); }

    .item-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        align-self: center;
    }

    .item-info { flex: 1; display: flex; flex-direction: column; text-align: center; }
    .item-info h3 { margin-bottom: 0.5rem; }
    .item-info p { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1rem; flex: 1; }

    .item-expiry {
        background: rgba(0,0,0,0.2);
        padding: 0.5rem;
        border-radius: var(--radius-s);
        font-size: 0.8rem;
        color: var(--cute-yellow);
        margin-bottom: 1rem;
    }

    .empty-state {
        text-align: center;
        padding: 4rem;
        background: var(--bg-card);
        border-radius: var(--radius-l);
        border: 2px dashed rgba(255,255,255,0.1);
        color: var(--text-muted);
    }
    
    .empty-state i { font-size: 4rem; margin-bottom: 1.5rem; color: var(--bg-card-hover); }
    .empty-state a { color: var(--cute-pink); text-decoration: none; font-weight: 800; }
</style>
@endsection
