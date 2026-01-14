@extends('layouts.app')

@section('title', 'Automations - SantuyTL')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-robot"></i> Automations</h1>
    <p class="page-subtitle">Buat bot bereaksi otomatis terhadap event di server.</p>
</div>

<div class="app-layout">
    {{-- List --}}
    <div class="card">
        <div class="card-header">
            <h3>Active Automations</h3>
        </div>
        <div class="card-body">
            @if($automations->isEmpty())
                <div class="text-center p-3 text-secondary">Belum ada automation.</div>
            @else
                <table class="table" style="width: 100%">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Trigger</th>
                            <th>Action</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($automations as $auto)
                        <tr>
                            <td>{{ $auto->name }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $auto->event }}</span>
                                @if($auto->trigger_value) <br><small class="text-muted">{{ Str::limit($auto->trigger_value, 20) }}</small> @endif
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $auto->action_type }}</span>
                                @if($auto->action_value) <br><small class="text-muted">{{ Str::limit($auto->action_value, 20) }}</small> @endif
                            </td>
                            <td>
                                <form action="{{ route('automations.toggle', ['guildId' => request()->route('guildId'), 'id' => $auto->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $auto->is_active ? 'btn-success' : 'btn-secondary' }}">
                                        {{ $auto->is_active ? 'ON' : 'OFF' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('automations.destroy', ['guildId' => request()->route('guildId'), 'id' => $auto->id]) }}" method="POST">
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
        <div class="card-header"><h3>Create Automation</h3></div>
        <div class="card-body">
            <form action="{{ route('automations.store', ['guildId' => request()->route('guildId')]) }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-input" placeholder="e.g. Auto Moderator Role" required>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">When (Event)</label>
                    <select name="event" id="eventSelect" class="form-select" onchange="updateUI()" required>
                        <option value="message_create">Message Contains Keyword</option>
                        <option value="voice_join">User Joins Voice Channel</option>
                    </select>
                </div>

                <div class="form-group mb-3" id="triggerInputGroup">
                    <label class="form-label" id="triggerLabel">Keyword</label>
                    
                    {{-- Text Input for Keyword --}}
                    <input type="text" name="trigger_value" id="triggerText" class="form-input" placeholder="e.g. help">

                    {{-- Channel Select for Voice --}}
                    <select name="trigger_value" id="triggerSelect" class="form-select" style="display:none;" disabled>
                        <option value="" disabled selected>Select Voice Channel...</option>
                        @foreach($channels as $channel)
                            <option value="{{ $channel['id'] }}">{{ $channel['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Do (Action)</label>
                    <select name="action_type" id="actionSelect" class="form-select" onchange="updateUI()" required>
                        <option value="reply">Reply with Message</option>
                        <option value="add_role">Add Role</option>
                        <option value="remove_role">Remove Role</option>
                    </select>
                </div>

                <div class="form-group mb-3" id="actionInputGroup">
                    <label class="form-label" id="actionLabel">Message Content</label>

                    {{-- Text Area for Reply --}}
                    <textarea name="action_value" id="actionText" class="form-textarea" rows="2" placeholder="Hello there!"></textarea>

                    {{-- Role Select --}}
                    <select name="action_value" id="actionSelectRole" class="form-select" style="display:none;" disabled>
                        <option value="" disabled selected>Select Role...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role['id'] }}" style="color: {{ '#' . dechex($role['color']) }}">{{ $role['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%">Create Automation</button>
            </form>
        </div>
    </div>
</div>

<script>
    function updateUI() {
        const event = document.getElementById('eventSelect').value;
        const action = document.getElementById('actionSelect').value;
        
        // Trigger UI
        const triggerLabel = document.getElementById('triggerLabel');
        const triggerText = document.getElementById('triggerText');
        const triggerSelect = document.getElementById('triggerSelect');

        if (event === 'message_create') {
            triggerLabel.innerText = 'Keyword (Contains)';
            triggerText.style.display = 'block';
            triggerText.disabled = false;
            triggerSelect.style.display = 'none';
            triggerSelect.disabled = true;
        } else if (event === 'voice_join') {
            triggerLabel.innerText = 'Voice Channel';
            triggerText.style.display = 'none';
            triggerText.disabled = true;
            triggerSelect.style.display = 'block';
            triggerSelect.disabled = false;
        }

        // Action UI
        const actionLabel = document.getElementById('actionLabel');
        const actionText = document.getElementById('actionText');
        const actionSelectRole = document.getElementById('actionSelectRole');

        if (action === 'reply') {
            actionLabel.innerText = 'Message Content';
            actionText.style.display = 'block';
            actionText.disabled = false;
            actionSelectRole.style.display = 'none';
            actionSelectRole.disabled = true;
        } else {
            actionLabel.innerText = 'Role';
            actionText.style.display = 'none';
            actionText.disabled = true;
            actionSelectRole.style.display = 'block';
            actionSelectRole.disabled = false;
        }
    }
</script>

<style>
    .app-layout { display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.5rem; }
    @media (max-width: 900px) { .app-layout { grid-template-columns: 1fr; } }
    .form-group { margin-bottom: 1rem; }
    .form-label { display: block; margin-bottom: 0.5rem; }
    .form-input, .form-textarea, .form-select { width: 100%; padding: 0.5rem; background: var(--bg-secondary); border: 1px solid var(--border); color: var(--text-primary); border-radius: 4px; }
    .btn { padding: 0.5rem 1rem; border: none; cursor: pointer; color: white; border-radius: 4px; }
    .btn-primary { background: #3b82f6; }
    .btn-danger { background: #ef4444; } .btn-success { background: #10b981; } .btn-secondary { background: #6b7280; }
    .table td { padding: 0.5rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
    .badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; color: #fff; }
    .bg-secondary { background: #4b5563; } .bg-primary { background: #3b82f6; }
</style>
@endsection
