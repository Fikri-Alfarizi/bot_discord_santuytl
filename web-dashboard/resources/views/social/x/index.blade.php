@extends('layouts.app')

@section('title', 'X (Twitter) Alerts - SantuyTL')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-brands fa-x-twitter"></i> X Alerts</h1>
    <p class="page-subtitle">Kirim notifikasi otomatis saat ada tweet baru.</p>
</div>

<div class="app-layout">
    <div class="card">
        <div class="card-header">
            <h3>Active Alerts</h3>
        </div>
        <div class="card-body">
            @if($alerts->isEmpty())
                <p class="text-secondary p-3 text-center">No alerts.</p>
            @else
                <table class="table" style="width: 100%;">
                    <thead><tr><th>Handle</th><th>Discord Channel</th><th>Action</th></tr></thead>
                    <tbody>
                        @foreach($alerts as $alert)
                            <tr>
                                <td>{{ $alert->identifier }}</td>
                                <td>#{{ $alert->discord_channel_id }}</td>
                                <td>
                                    <form action="{{ route('x.destroy', ['guildId' => request()->route('guildId'), 'id' => $alert->id]) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
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
        <div class="card-header"><h3>Add X Alert</h3></div>
        <div class="card-body">
            <form action="{{ route('x.store', ['guildId' => request()->route('guildId')]) }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label class="form-label">Handle (Username)</label>
                    <input type="text" name="identifier" class="form-input" placeholder="elonmusk" required>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">Post to Channel</label>
                    <select name="discord_channel_id" class="form-select" required>
                        @foreach($channels as $channel) <option value="{{ $channel['id'] }}">#{{ $channel['name'] }}</option> @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-success" style="width: 100%;">Create Alert</button>
            </form>
        </div>
    </div>
</div>
<style>
    .app-layout { display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.5rem; }
    @media (max-width: 900px) { .app-layout { grid-template-columns: 1fr; } }
    .form-group { margin-bottom: 1rem; }
    .form-label { display: block; margin-bottom: 0.5rem; }
    .form-input, .form-select { width: 100%; padding: 0.5rem; background: var(--bg-secondary); border: 1px solid var(--border); color: var(--text-primary); border-radius: 4px; }
    .btn { padding: 0.5rem 1rem; border: none; cursor: pointer; color: white; border-radius: 4px; }
    .btn-danger { background: #ef4444; } .btn-success { background: #10b981; }
    .table td { padding: 0.5rem; border-bottom: 1px solid var(--border); }
</style>
@endsection
