@extends('layouts.app')

@section('title', 'Reminders - SantuyTL')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-regular fa-clock"></i> Reminders</h1>
    <p class="page-subtitle">Jadwalkan pesan otomatis berulang ke channel server Anda.</p>
</div>

<div class="app-layout">
    {{-- List Card --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-list"></i> Active Reminders</h3>
        </div>
        <div class="card-body">
            @if($reminders->isEmpty())
                <div class="text-center p-3">
                    <p class="text-secondary">Belum ada reminder.</p>
                </div>
            @else
                <table class="table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Channel</th>
                            <th>Message</th>
                            <th>Interval</th>
                            <th>Next Run</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reminders as $r)
                            <tr>
                                <td>
                                    <span class="badge">#{{ $r->channel_id }}</span>
                                </td>
                                <td>{{ Str::limit($r->message, 30) }}</td>
                                <td>{{ $r->interval_minutes }} mins</td>
                                <td>{{ \Carbon\Carbon::parse($r->next_run_at)->diffForHumans() }}</td>
                                <td>
                                    <form action="{{ route('reminders.destroy', ['guildId' => request()->route('guildId'), 'id' => $r->id]) }}" method="POST">
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
            <h3><i class="fa-solid fa-plus"></i> Create Reminder</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('reminders.store', ['guildId' => request()->route('guildId')]) }}" method="POST">
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
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-textarea" rows="4" placeholder="Masukkan pesan reminder..." required></textarea>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Interval</label>
                    <select name="interval_minutes" class="form-select" required>
                        <option value="15">Every 15 Minutes</option>
                        <option value="30">Every 30 Minutes</option>
                        <option value="60">Every 1 Hour</option>
                        <option value="360">Every 6 Hours</option>
                        <option value="720">Every 12 Hours</option>
                        <option value="1440">Every 24 Hours</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-success" style="width: 100%;">
                    <i class="fa-solid fa-clock"></i> Set Reminder
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .app-layout {
        display: grid;
        grid-template-columns: 2fr 1fr;
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
</style>
@endsection
