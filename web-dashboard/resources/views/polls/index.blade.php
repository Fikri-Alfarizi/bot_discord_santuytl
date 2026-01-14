@extends('layouts.app')

@section('title', 'Polls - SantuyTL')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-square-poll-vertical"></i> Polls System</h1>
    <p class="page-subtitle">Buat voting interaktif untuk member server.</p>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fa-solid fa-plus"></i> Create New Poll</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('polls.store', ['guildId' => request()->route('guildId')]) }}" method="POST">
            @csrf
            
            <div class="form-group mb-3">
                <label class="form-label">Channel</label>
                <select name="channel_id" class="form-select" required>
                    <option value="" disabled selected>Pilih Channel...</option>
                    @foreach($channels as $channel)
                        <option value="{{ $channel['id'] }}">#{{ $channel['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Pertanyaan</label>
                <input type="text" name="question" class="form-input" placeholder="Contoh: Apa game favorit kalian?" required>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Pilihan Jawaban (Max 5)</label>
                <textarea name="options" class="form-textarea" rows="5" placeholder="Masukkan setiap pilihan di baris baru...&#10;Minecraft&#10;Roblox&#10;Valorant" required></textarea>
                <small class="text-muted">Satu pilihan per baris.</small>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Warna Embed</label>
                <input type="color" name="color" value="#6c63ff" class="form-control-color">
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-paper-plane"></i> Kirim Poll
            </button>
        </form>
    </div>
</div>

<style>
    .form-group { margin-bottom: 1.25rem; }
    .form-label { display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem; }
    .form-input, .form-textarea, .form-select {
        width: 100%;
        padding: 0.75rem;
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: 8px;
        color: var(--text-primary);
        font-family: inherit;
    }
    .form-select option { background: var(--bg-secondary); }
    
    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        background: var(--accent);
        color: white;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: 0.2s;
    }
    .btn:hover { opacity: 0.9; transform: translateY(-1px); }
    .text-muted { font-size: 0.8rem; color: var(--text-secondary); }
</style>
@endsection
