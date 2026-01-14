@extends('layouts.app')

@section('title', 'Temporary Channels - SantuyTL')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-volume-high"></i> Temporary Channels</h1>
    <p class="page-subtitle">Otomatis buat voice channel sementara saat member join ke "Hub Channel".</p>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fa-solid fa-gear"></i> Configuration</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('temp-channels.store', ['guildId' => request()->route('guildId')]) }}" method="POST">
            @csrf
            
            <div class="form-group mb-3">
                <label class="form-label">Hub Channel (Join to Create)</label>
                <select name="hub_channel_id" class="form-select" required>
                    <option value="" disabled selected>Pilih Voice Channel...</option>
                    @foreach($channels as $channel)
                        <option value="{{ $channel['id'] }}" {{ ($config->hub_channel_id ?? '') == $channel['id'] ? 'selected' : '' }}>
                            📢 {{ $channel['name'] }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">Channel yang mentrigger pembuatan channel baru.</small>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Target Category</label>
                <select name="category_id" class="form-select" required>
                    <option value="" disabled selected>Pilih Kategori...</option>
                    @foreach($categories as $category)
                        <option value="{{ $category['id'] }}" {{ ($config->category_id ?? '') == $category['id'] ? 'selected' : '' }}>
                            📂 {{ $category['name'] }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">Kategori dimana channel baru akan dibuat.</small>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Default Name Format</label>
                <input type="text" name="default_name" class="form-input" value="{{ $config->default_name ?? "{user}'s Channel" }}" required>
                <small class="text-muted">Gunakan <code>{user}</code> untuk nama user.</small>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Save Settings
            </button>
        </form>
    </div>
</div>

<style>
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
