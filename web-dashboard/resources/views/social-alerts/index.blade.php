@extends('layouts.app')

@section('title', 'Social Alerts - SantuyTL')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-bell"></i> Social Alerts</h1>
    <p class="page-subtitle">Dapatkan notifikasi otomatis dari YouTube, Twitch, dan RSS Feed.</p>
</div>

<div class="app-layout">
    {{-- List Card --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-list"></i> Active Alerts</h3>
        </div>
        <div class="card-body">
            @if($alerts->isEmpty())
                <div class="text-center p-3">
                    <p class="text-secondary">Belum ada alert aktif.</p>
                </div>
            @else
                <table class="table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Platform</th>
                            <th>ID/Username</th>
                            <th>Channel</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alerts as $alert)
                            <tr>
                                <td>
                                    @if($alert->platform == 'twitch') <i class="fa-brands fa-twitch text-purple"></i> Twitch
                                    @elseif($alert->platform == 'youtube') <i class="fa-brands fa-youtube text-red"></i> YouTube
                                    @elseif($alert->platform == 'rss') <i class="fa-solid fa-rss text-orange"></i> RSS
                                    @else {{ $alert->platform }} @endif
                                </td>
                                <td><code>{{ $alert->identifier }}</code></td>
                                <td>#{{ $alert->discord_channel_id }}</td>
                                <td>
                                    <form action="{{ route('social-alerts.destroy', ['guildId' => request()->route('guildId'), 'id' => $alert->id]) }}" method="POST">
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
            <h3><i class="fa-solid fa-plus"></i> Add New Alert</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('social-alerts.store', ['guildId' => request()->route('guildId')]) }}" method="POST">
                @csrf
                
                <div class="form-group mb-3">
                    <label class="form-label">Platform</label>
                    <select name="platform" class="form-select" id="platformSelect" onchange="updatePlaceholder()" required>
                        <option value="twitch">Twitch (Stream)</option>
                        <option value="youtube">YouTube (Video)</option>
                        <option value="rss">RSS Feed</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Identifier</label>
                    <input type="text" name="identifier" id="identifierInput" class="form-input" placeholder="Username Twitch" required>
                    <small class="text-muted" id="identifierHelp">Masukkan Username Twitch streamer.</small>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Post to Channel</label>
                    <select name="discord_channel_id" class="form-select" required>
                        <option value="" disabled selected>Pilih Text Channel...</option>
                        @foreach($channels as $channel)
                            <option value="{{ $channel['id'] }}">#{{ $channel['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Custom Message</label>
                    <textarea name="message" class="form-textarea" rows="2" placeholder="Guys! aku lagi live nih! {link}"></textarea>
                    <small class="text-muted">Gunakan <code>{link}</code> untuk link konten.</small>
                </div>
                
                <button type="submit" class="btn btn-success" style="width: 100%;">
                    <i class="fa-solid fa-bell"></i> Create Alert
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function updatePlaceholder() {
        const platform = document.getElementById('platformSelect').value;
        const input = document.getElementById('identifierInput');
        const help = document.getElementById('identifierHelp');

        if (platform === 'twitch') {
            input.placeholder = 'Username Twitch (contoh: shroud)';
            help.innerText = 'Masukkan Username Twitch streamer.';
        } else if (platform === 'youtube') {
            input.placeholder = 'Channel ID (contoh: UC...)';
            help.innerHTML = 'Masukkan <b>Channel ID</b> YouTube (bukan handle). Cari di settings YouTube.';
        } else if (platform === 'rss') {
            input.placeholder = 'RSS Feed URL (contoh: https://site.com/feed)';
            help.innerText = 'Masukkan URL RSS Feed yang valid.';
        }
    }
</script>

<style>
    .app-layout {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 1.5rem;
    }
    @media (max-width: 900px) { .app-layout { grid-template-columns: 1fr; } }

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

    .text-purple { color: #a970ff; }
    .text-red { color: #ff0000; }
    .text-orange { color: #ffae00; }

    .btn {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
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
</style>
@endsection
