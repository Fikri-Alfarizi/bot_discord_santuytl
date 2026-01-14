@extends('layouts.member')

@section('title', 'Santuy Shop')

@section('content')
@section('content')
<div class="market-header">
    <h1 class="page-title"><i class="fa-solid fa-store"></i> Santuy Market</h1>
    <div class="balance-display">
        Your Balance: <i class="fa-solid fa-coins"></i> <span>{{ number_format($userCoins) }}</span>
    </div>
</div>

<div class="items-grid">
    @foreach($items as $item)
    <div class="item-card" style="border-top: 4px solid {{ $item['color'] }}">
        <div class="item-icon" style="color: {{ $item['color'] }}; background: rgba(255,255,255,0.05);">
            <i class="fa-solid {{ $item['icon'] }}"></i>
        </div>
        <div class="item-info">
            <h3>{{ $item['name'] }}</h3>
            <p>{{ $item['description'] }}</p>
            <div class="item-price" style="background: rgba(255,255,255,0.05); color: {{ $item['color'] }}">
                <i class="fa-solid fa-coins"></i> {{ number_format($item['price']) }}
            </div>
            <button class="btn-cute btn-buy" 
                style="background: {{ $item['color'] }}" 
                onclick="buyItem('{{ $item['id'] }}', '{{ $item['name'] }}', {{ $item['price'] }})">
                Buy Now
            </button>
        </div>
    </div>
    @endforeach
</div>

@endsection

@section('styles')
<style>
    .market-header {
        text-align: center;
        margin-bottom: 3rem;
    }
    
    .balance-display {
        background: rgba(255,255,255,0.1);
        display: inline-block;
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        margin-top: 1rem;
        font-weight: 700;
        font-size: 1.2rem;
        color: var(--cute-yellow);
    }

    .items-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 2rem;
    }

    .item-card {
        background: var(--bg-card);
        border-radius: var(--radius-l);
        padding: 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        border: 1px solid rgba(255,255,255,0.05);
        transition: all 0.3s var(--bounce);
        position: relative;
        overflow: hidden;
    }

    .item-card:hover { transform: translateY(-10px) scale(1.02); box-shadow: var(--shadow-soft); }

    .item-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin-bottom: 1.5rem;
        transition: transform 0.3s var(--bounce);
    }
    
    .item-card:hover .item-icon { transform: scale(1.1) rotate(5deg); }

    .item-info h3 { font-size: 1.25rem; margin-bottom: 0.5rem; }
    .item-info p { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem; min-height: 40px; }

    .item-price {
        font-size: 1.25rem;
        font-weight: 800;
        margin-bottom: 1.5rem;
        padding: 0.5rem 1rem;
        border-radius: var(--radius-s);
    }

    .btn-buy {
        width: 100%;
        color: #232136;
        transition: all 0.2s;
        border: none;
    }
    
    .item-card:hover .btn-buy { transform: scale(1.05); }

</style>
@endsection

@section('scripts')
<script>
    async function buyItem(id, name, price) {
        if(!confirm(`Are you sure you want to buy ${name} for ${price} coins?`)) return;
        
        const btn = event.target;
        const originalText = btn.innerText;
        btn.innerText = 'Buying...';
        btn.disabled = true;
        
        try {
            const response = await fetch('{{ route('member.buy') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ item_id: id })
            });
            
            const result = await response.json();
            
            if(result.success) {
                alert('🎉 ' + result.message);
                // Optional: Update balance dynamically or reload
                location.reload();
            } else {
                alert('❌ ' + result.message);
            }
        } catch (error) {
            alert('❌ Network Error: ' + error.message);
        } finally {
            btn.innerText = originalText;
            btn.disabled = false;
        }
    }
</script>
@endsection
