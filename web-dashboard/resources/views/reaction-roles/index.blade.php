@extends('layouts.app')

@section('title', 'Reaction Roles - SantuyTL')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-icons"></i> Reaction Roles</h1>
    <p class="page-subtitle">Otomatis berikan role saat member bereaksi dengan emoji</p>
</div>

<div class="reaction-grid">
    {{-- Create Form --}}
    <div class="config-section">
        <div class="section-header create">
            <div class="section-icon"><i class="fa-solid fa-plus-circle"></i></div>
            <div class="section-info">
                <h2>Buat Reaction Role Baru</h2>
                <p>Pastikan bot memiliki permission untuk manage roles</p>
            </div>
        </div>
        
        <div class="section-body">
            <form action="{{ route('reaction-roles.store', ['guildId' => request()->route('guildId')]) }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-hashtag"></i> Pilih Channel</label>
                    <select name="channel_id" class="form-select" required>
                        <option value="">Pilih Channel...</option>
                        @foreach($channels as $channel)
                            <option value="{{ $channel['id'] }}"># {{ $channel['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-message"></i> Message ID</label>
                    <input type="text" name="message_id" class="form-input" placeholder="Contoh: 123456789012345678" required>
                    <small class="form-hint">Klik kanan pesan di Discord -> Copy ID (Aktifkan Developer Mode)</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label"><i class="fa-regular fa-face-smile"></i> Emoji</label>
                        <input type="text" name="emoji" class="form-input" placeholder="❤️ atau :custom_emoji:" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-user-tag"></i> Role Reward</label>
                        <select name="role_id" class="form-select" required>
                            <option value="">Pilih Role...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role['id'] }}">@ {{ $role['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-save">
                    <i class="fa-solid fa-save"></i> Simpan Reaction Role
                </button>
            </form>
        </div>
    </div>

    {{-- List --}}
    <div class="config-section">
        <div class="section-header list">
            <div class="section-icon"><i class="fa-solid fa-list-check"></i></div>
            <div class="section-info">
                <h2>Active Reaction Roles</h2>
                <p>Daftar reaction role yang aktif</p>
            </div>
        </div>

        <div class="section-body p-0">
            @if($reactionRoles->isEmpty())
                <div class="empty-state">
                    <i class="fa-solid fa-ghost"></i>
                    <p>Belum ada reaction role yang dibuat.</p>
                </div>
            @else
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Emoji</th>
                                <th>Role</th>
                                <th>Channel</th>
                                <th>Message ID</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reactionRoles as $rr)
                                @php
                                    $roleName = collect($roles)->firstWhere('id', $rr->role_id)['name'] ?? 'Unknown Role';
                                    $channelName = collect($channels)->firstWhere('id', $rr->channel_id)['name'] ?? 'Unknown Channel';
                                @endphp
                                <tr>
                                    <td class="emoji-cell">{{ $rr->emoji }}</td>
                                    <td><span class="role-badge">@ {{ $roleName }}</span></td>
                                    <td># {{ $channelName }}</td>
                                    <td><code>{{ Str::limit($rr->message_id, 10) }}</code></td>
                                    <td>
                                        <form action="{{ route('reaction-roles.destroy', ['guildId' => request()->route('guildId'), 'id' => $rr->id]) }}" method="POST" onsubmit="return confirm('Hapus reaction role ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon delete"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .reaction-grid { display: grid; grid-template-columns: 1fr 1.5fr; gap: 2rem; }
    @media (max-width: 1024px) { .reaction-grid { grid-template-columns: 1fr; } }

    .config-section {
        background: var(--bg-card);
        border-radius: 16px;
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .section-header {
        padding: 1.25rem;
        display: flex; align-items: center; gap: 1rem;
        border-bottom: 1px solid var(--border);
    }
    .section-header.create { background: linear-gradient(135deg, rgba(88, 101, 242, 0.1), rgba(87, 242, 135, 0.05)); }
    .section-header.list { background: linear-gradient(135deg, rgba(235, 69, 158, 0.1), rgba(254, 215, 0, 0.05)); }

    .section-icon { font-size: 1.5rem; width: 40px; height: 40px; background: rgba(255,255,255,0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    .section-info h2 { font-size: 1rem; margin: 0; }
    .section-info p { font-size: 0.8rem; color: var(--text-secondary); margin: 0; }

    .section-body { padding: 1.5rem; }
    .section-body.p-0 { padding: 0; }

    .form-group { margin-bottom: 1rem; }
    .form-label { display: block; font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.4rem; }
    .form-input, .form-select {
        width: 100%; padding: 0.7rem 1rem;
        background: var(--bg-secondary); border: 1px solid var(--border);
        border-radius: 10px; color: var(--text-primary); font-size: 0.9rem;
    }
    .form-hint { display: block; margin-top: 0.25rem; color: var(--text-muted); font-size: 0.75rem; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

    .btn-save {
        width: 100%; margin-top: 1rem;
        background: var(--accent); color: white;
        padding: 0.8rem; border: none; border-radius: 10px;
        font-weight: 600; cursor: pointer; transition: 0.2s;
    }
    .btn-save:hover { filter: brightness(1.1); }

    .empty-state { padding: 3rem; text-align: center; color: var(--text-muted); }
    .empty-state i { font-size: 3rem; margin-bottom: 1rem; opacity: 0.5; }

    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { text-align: left; padding: 1rem; color: var(--text-secondary); font-size: 0.85rem; border-bottom: 1px solid var(--border); }
    .data-table td { padding: 1rem; border-bottom: 1px solid var(--border); font-size: 0.9rem; }
    .data-table tr:last-child td { border-bottom: none; }

    .role-badge { background: rgba(88, 101, 242, 0.15); color: #5865f2; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.8rem; }
    .emoji-cell { font-size: 1.2rem; }
    
    .btn-icon { background: none; border: none; cursor: pointer; color: var(--text-muted); transition: 0.2s; }
    .btn-icon:hover { color: var(--text-primary); }
    .btn-icon.delete:hover { color: #ed4245; }
</style>
@endsection
