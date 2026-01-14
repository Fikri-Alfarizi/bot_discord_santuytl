@extends('layouts.app')

@section('title', 'Stats Channels - SantuyTL')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-chart-simple"></i> Stats Channels</h1>
    <p class="page-subtitle">Tampilkan statistik server di sidebar channel (Voice Channel yang dikunci).</p>
</div>

<div class="app-layout">
    {{-- List Card --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-list"></i> Active Channels</h3>
        </div>
        <div class="card-body">
            @if($statsChannels->isEmpty())
                <div class="text-center p-3">
                    <p class="text-secondary">Belum ada stats channel.</p>
                </div>
            @else
                <table class="table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Format</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($statsChannels as $sc)
                            <tr>
                                <td>
                                    <span class="badge">{{ ucfirst($sc->type) }}</span>
                                </td>
                                <td><code>{{ $sc->format }}</code></td>
                                <td>
                                    <form action="{{ route('stats-channels.destroy', ['guildId' => request()->route('guildId'), 'id' => $sc->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- Create Card --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-plus"></i> Create New</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('stats-channels.store', ['guildId' => request()->route('guildId')]) }}" method="POST">
                @csrf
                
                <div class="form-group mb-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select" onchange="updateFormatPlaceholder(this)">
                        <option value="members">Total Members</option>
                        <option value="bots">Total Bots</option>
                        <option value="humans">Total Humans</option>
                        <option value="online">Online Members</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Format Name</label>
                    <input type="text" name="format" id="formatInput" class="form-input" value="Members: {count}" required>
                    <small class="text-muted">Gunakan <code>{count}</code> untuk angka.</small>
                </div>
                
                <button type="submit" class="btn btn-success" style="width: 100%;">
                    <i class="fa-solid fa-plus-circle"></i> Create Channel
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function updateFormatPlaceholder(select) {
        const formatInput = document.getElementById('formatInput');
        switch(select.value) {
            case 'members': formatInput.value = 'Members: {count}'; break;
            case 'bots': formatInput.value = 'Bots: {count}'; break;
            case 'humans': formatInput.value = 'Humans: {count}'; break;
            case 'online': formatInput.value = 'Online: {count}'; break;
        }
    }
</script>

<style>
    .app-layout {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
    }
    @media (max-width: 900px) { .app-layout { grid-template-columns: 1fr; } }

    .form-group { margin-bottom: 1.25rem; }
    .form-label { display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem; }
    .form-input, .form-select {
        width: 100%;
        padding: 0.75rem;
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: 8px;
        color: var(--text-primary);
        font-family: inherit;
    }
    .form-select option { background: var(--bg-secondary); }

    .badge {
        padding: 0.25rem 0.75rem;
        background: rgba(108, 99, 255, 0.1);
        color: #6c63ff;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .btn {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: white;
    }
    .btn-danger { background: #ef4444; }
    .btn-success { background: #10b981; }
    .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.8rem; }
    
    .table td, .table th { padding: 0.75rem; text-align: left; border-bottom: 1px solid var(--border); }
    .table th { color: var(--text-secondary); font-weight: 500; font-size: 0.9rem; }
    .text-center { text-align: center; }
    .text-secondary { color: var(--text-secondary); }
    .text-muted { font-size: 0.8rem; color: var(--text-secondary); }
</style>
@endsection
