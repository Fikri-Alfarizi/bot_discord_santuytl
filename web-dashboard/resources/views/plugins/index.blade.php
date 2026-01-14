@extends('layouts.app')

@section('title', 'Plugins - SantuyTL')

@section('content')
<div class="plugins-header">
    <h1 class="page-title"><i class="fa-solid fa-puzzle-piece"></i> Plugins</h1>
    <p class="page-subtitle">Aktifkan fitur-fitur untuk meningkatkan server Discord kamu</p>
    
    {{-- Filter Tabs --}}
    <div class="filter-tabs">
        <button class="filter-tab active" data-filter="all">All Plugins</button>
        <button class="filter-tab" data-filter="active">Active</button>
        <button class="filter-tab" data-filter="soon">Coming Soon</button>
        <button class="filter-tab" data-filter="premium">Premium</button>
    </div>
</div>

@foreach($plugins as $category => $items)
<div class="plugin-section" id="section-{{ Str::slug($category) }}">
    <h2 class="section-title">{{ $category }}</h2>
    <div class="plugins-grid">
        @foreach($items as $plugin)
        <div class="plugin-card" 
             data-status="{{ $plugin['status'] }}" 
             data-premium="{{ $plugin['premium'] ? 'true' : 'false' }}">
            <div class="plugin-header">
                <div class="plugin-icon"><i class="{{ $plugin['fa_icon'] ?? 'fa-solid fa-cube' }}"></i></div>
                <div class="plugin-badges">
                    @if($plugin['premium'] ?? false)
                        <span class="badge badge-premium">Premium</span>
                    @endif
                    @if($plugin['new'] ?? false)
                        <span class="badge badge-new">New!</span>
                    @endif
                </div>
            </div>
            <div class="plugin-body">
                <h3 class="plugin-name">{{ $plugin['name'] }}</h3>
                <p class="plugin-desc">{{ $plugin['desc'] }}</p>
            </div>
            <div class="plugin-footer">
                @if($plugin['status'] === 'active')
                    @if(isset($plugin['route']))
                        <a href="{{ route($plugin['route'], ['guildId' => request()->route('guildId')]) }}" class="plugin-btn active">
                            <i class="fa-solid fa-gear"></i> Configure
                        </a>
                    @else
                        <button class="plugin-btn active" disabled>
                            <span class="dot active"></span> Active
                        </button>
                    @endif
                @else
                    <button class="plugin-btn soon" onclick="showComingSoon()">
                        <i class="fa-solid fa-lock"></i> Coming Soon
                    </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endforeach

{{-- Coming Soon Modal --}}
<div id="comingSoonModal" class="modal">
    <div class="modal-content">
        <div class="modal-icon"><i class="fa-solid fa-rocket"></i></div>
        <h2>Coming Soon!</h2>
        <p>Fitur ini sedang dalam pengembangan dan akan segera hadir.</p>
        <button onclick="closeModal()" class="btn btn-primary"><i class="fa-solid fa-check"></i> Mengerti</button>
    </div>
</div>

<style>
    .plugins-header {
        margin-bottom: 2rem;
    }

    .filter-tabs {
        display: flex;
        gap: 0.5rem;
        margin-top: 1.5rem;
        flex-wrap: wrap;
    }

    .filter-tab {
        padding: 0.5rem 1rem;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 20px;
        color: var(--text-secondary);
        cursor: pointer;
        font-size: 0.85rem;
        transition: all 0.2s;
    }

    .filter-tab:hover {
        border-color: var(--accent);
        color: var(--text-primary);
    }

    .filter-tab.active {
        background: var(--accent);
        border-color: var(--accent);
        color: white;
    }

    .plugin-section {
        margin-bottom: 2.5rem;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-secondary);
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--border);
    }

    .plugins-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
    }

    .plugin-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        transition: all 0.2s;
    }

    .plugin-card:hover {
        border-color: var(--accent);
        transform: translateY(-2px);
    }

    .plugin-card.hidden {
        display: none;
    }

    .plugin-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .plugin-icon {
        font-size: 1.75rem;
        width: 56px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(108, 99, 255, 0.1);
        border-radius: 12px;
        color: var(--accent);
    }

    .plugin-badges {
        display: flex;
        gap: 0.35rem;
        flex-wrap: wrap;
    }

    .badge {
        font-size: 0.6rem;
        padding: 0.2rem 0.5rem;
        border-radius: 10px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .badge-premium {
        background: rgba(255, 215, 0, 0.15);
        color: #ffd700;
    }

    .badge-new {
        background: rgba(237, 137, 54, 0.15);
        color: var(--warning);
    }

    .plugin-body {
        flex: 1;
        margin-bottom: 1rem;
    }

    .plugin-name {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .plugin-desc {
        font-size: 0.8rem;
        color: var(--text-secondary);
        line-height: 1.4;
    }

    .plugin-footer {
        margin-top: auto;
    }

    .plugin-btn {
        width: 100%;
        padding: 0.65rem 1rem;
        border-radius: 8px;
        border: none;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.2s;
        text-decoration: none;
    }

    .plugin-btn.active {
        background: rgba(72, 187, 120, 0.15);
        color: var(--success);
    }

    .plugin-btn.active:hover {
        background: rgba(72, 187, 120, 0.25);
    }

    .plugin-btn.soon {
        background: var(--bg-secondary);
        color: var(--text-secondary);
    }

    .plugin-btn.soon:hover {
        background: rgba(108, 99, 255, 0.15);
        color: var(--accent);
    }

    .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .dot.active {
        background: var(--success);
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.8);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal.show {
        display: flex;
    }

    .modal-content {
        background: var(--bg-card);
        padding: 2rem;
        border-radius: 16px;
        text-align: center;
        max-width: 400px;
        margin: 1rem;
    }

    .modal-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        color: var(--accent);
    }

    .modal-content h2 {
        margin-bottom: 0.5rem;
    }

    .modal-content p {
        color: var(--text-secondary);
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .plugins-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    // Filter functionality
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            // Update active tab
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const filter = this.dataset.filter;
            
            document.querySelectorAll('.plugin-card').forEach(card => {
                if (filter === 'all') {
                    card.classList.remove('hidden');
                } else if (filter === 'active') {
                    card.classList.toggle('hidden', card.dataset.status !== 'active');
                } else if (filter === 'soon') {
                    card.classList.toggle('hidden', card.dataset.status !== 'soon');
                } else if (filter === 'premium') {
                    card.classList.toggle('hidden', card.dataset.premium !== 'true');
                }
            });

            // Hide empty sections
            document.querySelectorAll('.plugin-section').forEach(section => {
                const visibleCards = section.querySelectorAll('.plugin-card:not(.hidden)');
                section.style.display = visibleCards.length === 0 ? 'none' : 'block';
            });
        });
    });

    function showComingSoon() {
        document.getElementById('comingSoonModal').classList.add('show');
    }

    function closeModal() {
        document.getElementById('comingSoonModal').classList.remove('show');
    }

    document.getElementById('comingSoonModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
@endsection
