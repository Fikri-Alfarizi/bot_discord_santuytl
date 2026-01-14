@extends('layouts.app')

@section('title', 'Twitch Alerts - SantuyTL')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-brands fa-twitch text-purple"></i> Twitch Alerts</h1>
    <p class="page-subtitle">Kirim notifikasi otomatis saat kamu live di Twitch.</p>
</div>

<div class="app-layout">
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
                            <th>Username</th>
                            <th>Channel</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alerts as $alert)
                            <tr>
                                <td>{{ $alert->identifier }}</td>
                                <td>#{{ $alert->discord_channel_id }}</td>
                                <td>
                                    <form action="{{ route('twitch.destroy', ['guildId' => request()->route('guildId'), 'id' => $alert->id]) }}" method="POST">
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

    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-plus"></i> Add Twitch Alert</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('twitch.store', ['guildId' => request()->route('guildId')]) }}" method="POST">
                @csrf
                
                <div class="form-group mb-3">
                    <label class="form-label">Twitch Username</label>
                    <input type="text" name="identifier" class="form-input" placeholder="e.g. shroud" required>
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
                    <textarea name="message" class="form-textarea" rows="2" placeholder="Hey everyone! {identifier} is live! {link}"></textarea>
                    <small class="text-muted">Available tags: <code>{identifier}</code>, <code>{link}</code></small>
                </div>
                
                <button type="submit" class="btn btn-success" style="width: 100%;">Create Alert</button>
            </form>
        </div>
    </div>
</div>

<style>
    .app-layout { display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.5rem; }
    @media (max-width: 900px) { .app-layout { grid-template-columns: 1fr; } }
    .form-group { margin-bottom: 1.25rem; }
    .form-label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
    .form-input, .form-textarea, .form-select { width: 100%; padding: 0.75rem; background: var(--bg-secondary); border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary); }
    .btn { padding: 0.5rem 1rem; border: none; border-radius: 6px; cursor: pointer; color: white; }
    .btn-danger { background: #ef4444; } .btn-success { background: #10b981; }
    .text-purple { color: #9146FF; }
    .table td, .table th { padding: 0.75rem; text-align: left; border-bottom: 1px solid var(--border); }
</style>
@endsection
