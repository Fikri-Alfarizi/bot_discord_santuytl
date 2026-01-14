@extends('layouts.app')

@section('title', 'Giveaways - SantuyTL')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-gift text-purple"></i> Giveaways</h1>
    <p class="page-subtitle">Buat giveaway untuk komunitasmu dengan mudah.</p>
</div>

<div class="app-layout">
    {{-- List --}}
    <div class="card">
        <div class="card-header">
            <h3>Active & Recent Giveaways</h3>
        </div>
        <div class="card-body">
            @if($giveaways->isEmpty())
                <div class="text-center p-3 text-secondary">Belum ada giveaway.</div>
            @else
                <table class="table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>Prize</th>
                            <th>Status</th>
                            <th>End Time</th>
                            <th>Entries</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($giveaways as $g)
                        <tr>
                            <td>
                                <strong>{{ $g->prize }}</strong>
                                <br><small class="text-muted">#{{ $g->channel_id }}</small>
                            </td>
                            <td>
                                @if($g->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Ended</span>
                                @endif
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($g->end_at)->diffForHumans() }}
                            </td>
                            <td>{{ $g->participant_count }}</td>
                            <td>
                                <form action="{{ route('giveaways.destroy', ['guildId' => request()->route('guildId'), 'id' => $g->id]) }}" method="POST">
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

    {{-- Create Form --}}
    <div class="card">
        <div class="card-header"><h3>Create Giveaway</h3></div>
        <div class="card-body">
            <form action="{{ route('giveaways.store', ['guildId' => request()->route('guildId')]) }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label class="form-label">Prize</label>
                    <input type="text" name="prize" class="form-input" placeholder="e.g. Nitro Classic" required>
                </div>
                
                <div class="form-group mb-3">
                    <label class="form-label">Description (Optional)</label>
                    <textarea name="description" class="form-textarea" rows="2" placeholder="Rules or details..."></textarea>
                </div>

                <div class="row" style="display: flex; gap: 1rem;">
                    <div class="form-group mb-3" style="flex: 1;">
                        <label class="form-label">Winners</label>
                        <input type="number" name="winner_count" class="form-input" value="1" min="1" required>
                    </div>
                </div>

                <label class="form-label">Duration</label>
                <div class="row mb-3" style="display: flex; gap: 1rem;">
                    <div class="form-group" style="flex: 2;">
                        <input type="number" name="duration" class="form-input" value="1" min="1" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <select name="duration_unit" class="form-select">
                            <option value="minutes">Minutes</option>
                            <option value="hours">Hours</option>
                            <option value="days">Days</option>
                        </select>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Channel</label>
                    <select name="channel_id" class="form-select" required>
                        @foreach($channels as $channel)
                            <option value="{{ $channel['id'] }}">#{{ $channel['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%">Start Giveaway</button>
            </form>
        </div>
    </div>
</div>

<style>
    .app-layout { display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.5rem; }
    @media (max-width: 900px) { .app-layout { grid-template-columns: 1fr; } }
    .form-group { margin-bottom: 1rem; }
    .form-label { display: block; margin-bottom: 0.5rem; }
    .form-input, .form-textarea, .form-select { width: 100%; padding: 0.5rem; background: var(--bg-secondary); border: 1px solid var(--border); color: var(--text-primary); border-radius: 4px; }
    .btn { padding: 0.5rem 1rem; border: none; cursor: pointer; color: white; border-radius: 4px; }
    .btn-primary { background: #9b59b6; }
    .btn-danger { background: #ef4444; } 
    .text-purple { color: #9b59b6; }
    .table td { padding: 0.5rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
    .badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; color: #fff; }
    .bg-success { background: #10b981; } .bg-secondary { background: #6b7280; }
</style>
@endsection
