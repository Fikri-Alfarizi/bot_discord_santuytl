@extends('layouts.app')

@section('title', 'Birthdays - SantuyTL')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-cake-candles text-pink"></i> Birthdays</h1>
    <p class="page-subtitle">Rayakan ulang tahun member komunitasmu secara otomatis.</p>
</div>

<div class="app-layout">
    {{-- Settings Form --}}
    <div class="card">
        <div class="card-header"><h3>Settings</h3></div>
        <div class="card-body">
            <form action="{{ route('birthdays.update', ['guildId' => request()->route('guildId')]) }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label class="form-label">Announcement Channel</label>
                    <select name="birthday_channel_id" class="form-select" required>
                        <option value="" disabled selected>Select Channel...</option>
                        @foreach($channels as $channel)
                            <option value="{{ $channel['id'] }}" {{ ($settings->birthday_channel_id ?? '') == $channel['id'] ? 'selected' : '' }}>
                                #{{ $channel['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Message</label>
                    <textarea name="birthday_message" class="form-textarea" rows="3">{{ $settings->birthday_message ?? 'Happy Birthday {user}! 🎉 We wish you all the best! 🎂' }}</textarea>
                    <small class="text-muted">Use <code>{user}</code> to mention the birthday member.</small>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%">Save Settings</button>
            </form>
            
            <hr class="my-4" style="border-color: var(--border);">
            
            <div class="alert alert-info">
                <i class="fa-solid fa-circle-info"></i> Members can set their birthday using: <br>
                <code>!birthday DD-MM</code> (Example: <code>!birthday 25-12</code>)
            </div>
        </div>
    </div>

    {{-- List --}}
    <div class="card">
        <div class="card-header">
            <h3>Upcoming Birthdays</h3>
        </div>
        <div class="card-body">
            @if($birthdays->isEmpty())
                <div class="text-center p-3 text-secondary">Belum ada data ulang tahun.</div>
            @else
                <table class="table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($birthdays as $b)
                        <tr>
                            <td>{{ $b->user_name }}</td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ \Carbon\Carbon::createFromDate(null, $b->month, $b->day)->format('d F') }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('birthdays.destroy', ['guildId' => request()->route('guildId'), 'id' => $b->id]) }}" method="POST">
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
</div>

<style>
    .app-layout { display: grid; grid-template-columns: 1fr 1.5fr; gap: 1.5rem; }
    @media (max-width: 900px) { .app-layout { grid-template-columns: 1fr; } }
    .form-group { margin-bottom: 1rem; }
    .form-label { display: block; margin-bottom: 0.5rem; }
    .form-input, .form-textarea, .form-select { width: 100%; padding: 0.5rem; background: var(--bg-secondary); border: 1px solid var(--border); color: var(--text-primary); border-radius: 4px; }
    .btn { padding: 0.5rem 1rem; border: none; cursor: pointer; color: white; border-radius: 4px; }
    .btn-primary { background: #ec4899; } /* Pink-500 */
    .btn-danger { background: #ef4444; } 
    .text-pink { color: #ec4899; }
    .table td { padding: 0.5rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
    .badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; color: #fff; }
    .bg-secondary { background: #4b5563; }
    .alert-info { background: rgba(59, 130, 246, 0.1); color: #60a5fa; padding: 1rem; border-radius: 4px; border: 1px solid rgba(59, 130, 246, 0.2); }
</style>
@endsection
