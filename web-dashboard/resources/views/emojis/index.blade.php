@extends('layouts.app')

@section('title', 'Emojis Manager - SantuyTL')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-regular fa-face-grin"></i> Emojis Manager</h1>
    <p class="page-subtitle">Lihat daftar emoji yang ada di server ini.</p>
</div>

@if(empty($emojis))
    <div class="card">
        <div class="card-body text-center" style="padding: 3rem;">
            <i class="fa-regular fa-face-sad-tear" style="font-size: 3rem; margin-bottom: 1rem; color: var(--text-secondary);"></i>
            <h3>Tidak ada emoji ditemukan</h3>
            <p style="color: var(--text-secondary);">Server ini belum memiliki custom emoji atau Bot tidak bisa mengaksesnya.</p>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-icons"></i> Server Emojis ({{ count($emojis) }})</h3>
        </div>
        <div class="card-body">
            <div class="emoji-grid">
                @foreach($emojis as $emoji)
                    <div class="emoji-item">
                        <div class="emoji-preview">
                            @php
                                $ext = $emoji['animated'] ?? false ? 'gif' : 'png';
                                $url = "https://cdn.discordapp.com/emojis/{$emoji['id']}.{$ext}";
                            @endphp
                            <img src="{{ $url }}" alt="{{ $emoji['name'] }}" loading="lazy">
                        </div>
                        <div class="emoji-info">
                            <span class="emoji-name">:{{ $emoji['name'] }}:</span>
                            <button class="copy-btn" onclick="copyToClipboard('<:{{ $emoji['name'] }}:{{ $emoji['id'] }}>')">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Emoji ID copied: ' + text);
        });
    }
</script>

<style>
    .emoji-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 1rem;
    }

    .emoji-item {
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        transition: 0.2s;
    }
    
    .emoji-item:hover {
        transform: translateY(-2px);
        border-color: var(--accent);
    }

    .emoji-preview img {
        width: 48px;
        height: 48px;
        object-fit: contain;
    }

    .emoji-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        width: 100%;
        justify-content: space-between;
        background: rgba(0,0,0,0.1);
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
    }

    .emoji-name {
        font-family: monospace;
        font-size: 0.8rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 80px;
    }

    .copy-btn {
        background: none;
        border: none;
        color: var(--text-secondary);
        cursor: pointer;
        padding: 0;
        font-size: 0.8rem;
    }
    .copy-btn:hover { color: var(--accent); }
</style>
@endsection
