@extends('layouts.app')

@section('title', 'Moderator - SantuyTL')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-shield-halved"></i> Moderator</h1>
    <p class="page-subtitle">Otomatisasi moderasi server dan manajemen peringatan</p>
</div>

<form action="{{ route('moderator.update', ['guildId' => request()->route('guildId')]) }}" method="POST">
    @csrf
    
    <div class="mod-grid">
        {{-- Anti Bad Word --}}
        <div class="config-section">
            <div class="section-header badword">
                <div class="section-icon"><i class="fa-solid fa-filter"></i></div>
                <div class="section-info">
                    <h2>Anti Bad Word</h2>
                    <p>Filter kata-kata kasar</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="bad_word_enabled" value="1" {{ $rules['bad_word']->enabled ? 'checked' : '' }}>
                    <span class="slider"></span>
                </label>
            </div>
            <div class="section-body">
                <div class="form-group">
                    <label class="form-label">Daftar Kata (pisahkan dengan koma)</label>
                    <textarea name="bad_word_content" class="form-textarea" placeholder="kata1, kata2, kata3">{{ $rules['bad_word']->trigger_content }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Tindakan</label>
                    <select name="bad_word_action" class="form-select">
                        <option value="delete" {{ $rules['bad_word']->action == 'delete' ? 'selected' : '' }}>Hapus Pesan</option>
                        <option value="timeout" {{ $rules['bad_word']->action == 'timeout' ? 'selected' : '' }}>Timeout User (10m)</option>
                        <option value="kick" {{ $rules['bad_word']->action == 'kick' ? 'selected' : '' }}>Kick User</option>
                        <option value="ban" {{ $rules['bad_word']->action == 'ban' ? 'selected' : '' }}>Ban User</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Anti Link --}}
        <div class="config-section">
            <div class="section-header link">
                <div class="section-icon"><i class="fa-solid fa-link"></i></div>
                <div class="section-info">
                    <h2>Anti Link</h2>
                    <p>Cegah link promosi / spam</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="link_enabled" value="1" {{ $rules['link']->enabled ? 'checked' : '' }}>
                    <span class="slider"></span>
                </label>
            </div>
            <div class="section-body">
                <div class="alert alert-info">
                    <i class="fa-solid fa-info-circle"></i> Memblokir semua link http/https
                </div>
                <div class="form-group">
                    <label class="form-label">Tindakan</label>
                    <select name="link_action" class="form-select">
                        <option value="delete" {{ $rules['link']->action == 'delete' ? 'selected' : '' }}>Hapus Pesan</option>
                        <option value="timeout" {{ $rules['link']->action == 'timeout' ? 'selected' : '' }}>Timeout User (10m)</option>
                        <option value="kick" {{ $rules['link']->action == 'kick' ? 'selected' : '' }}>Kick User</option>
                        <option value="ban" {{ $rules['link']->action == 'ban' ? 'selected' : '' }}>Ban User</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Anti Spam --}}
        <div class="config-section">
            <div class="section-header spam">
                <div class="section-icon"><i class="fa-solid fa-shuttle-space"></i></div>
                <div class="section-info">
                    <h2>Anti Spam</h2>
                    <p>Deteksi spam pesan beruntun</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="spam_enabled" value="1" {{ $rules['spam']->enabled ? 'checked' : '' }}>
                    <span class="slider"></span>
                </label>
            </div>
            <div class="section-body">
                <div class="alert alert-info">
                    <i class="fa-solid fa-info-circle"></i> Blokir jika > 5 pesan dalam 5 detik
                </div>
                <div class="form-group">
                    <label class="form-label">Tindakan</label>
                    <select name="spam_action" class="form-select">
                        <option value="delete" {{ $rules['spam']->action == 'delete' ? 'selected' : '' }}>Hapus Pesan</option>
                        <option value="timeout" {{ $rules['spam']->action == 'timeout' ? 'selected' : '' }}>Timeout User (10m)</option>
                        <option value="kick" {{ $rules['spam']->action == 'kick' ? 'selected' : '' }}>Kick User</option>
                        <option value="ban" {{ $rules['spam']->action == 'ban' ? 'selected' : '' }}>Ban User</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-save floating-save">
        <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan Rule
    </button>
</form>

{{-- Warning History --}}
<div class="config-section" style="margin-top: 2rem;">
    <div class="section-header warn">
        <div class="section-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div class="section-info">
            <h2>Riwayat Peringatan (Warnings)</h2>
            <p>Daftar user yang telah diperingatkan</p>
        </div>
    </div>
    <div class="section-body p-0">
        @if($warns->isEmpty())
            <div class="empty-state">
                <i class="fa-solid fa-check-circle"></i>
                <p>Tidak ada riwayat peringatan.</p>
            </div>
        @else
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Moderator ID</th>
                            <th>Alasan</th>
                            <th>Waktu</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($warns as $warn)
                            <tr>
                                <td>{{ $warn->user_id }}</td>
                                <td>{{ $warn->moderator_id }}</td>
                                <td>{{ $warn->reason }}</td>
                                <td>{{ \Carbon\Carbon::createFromTimestamp($warn->timestamp)->diffForHumans() }}</td>
                                <td>
                                    <form action="{{ route('moderator.destroy', ['guildId' => request()->route('guildId'), 'id' => $warn->id]) }}" method="POST" onsubmit="return confirm('Hapus warning ini?')">
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

<style>
    .mod-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; }
    
    .config-section {
        background: var(--bg-card);
        border-radius: 16px;
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .section-header {
        padding: 1rem;
        display: flex; align-items: center; gap: 1rem;
        border-bottom: 1px solid var(--border);
    }
    .section-header.badword { background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(254, 215, 0, 0.05)); }
    .section-header.link { background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(34, 211, 238, 0.05)); }
    .section-header.spam { background: linear-gradient(135deg, rgba(168, 85, 247, 0.1), rgba(236, 72, 153, 0.05)); }
    .section-header.warn { background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(254, 215, 0, 0.05)); }

    .section-icon { width: 36px; height: 36px; background: rgba(255,255,255,0.1); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
    .section-info { flex: 1; }
    .section-info h2 { font-size: 0.95rem; margin: 0; }
    .section-info p { font-size: 0.75rem; color: var(--text-secondary); margin: 0; }

    .section-body { padding: 1.25rem; }
    .section-body.p-0 { padding: 0; }

    .toggle-switch { position: relative; width: 44px; height: 24px; }
    .toggle-switch input { display: none; }
    .toggle-switch .slider {
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: var(--bg-secondary); border-radius: 24px;
        cursor: pointer; transition: 0.3s;
    }
    .toggle-switch .slider::before {
        content: ''; position: absolute;
        width: 18px; height: 18px; left: 3px; bottom: 3px;
        background: white; border-radius: 50%; transition: 0.3s;
    }
    .toggle-switch input:checked + .slider { background: var(--success); }
    .toggle-switch input:checked + .slider::before { transform: translateX(20px); }

    .form-group { margin-bottom: 1rem; }
    .form-label { display: block; font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 0.4rem; }
    .form-select, .form-textarea {
        width: 100%; padding: 0.6rem;
        background: var(--bg-secondary); border: 1px solid var(--border);
        border-radius: 8px; color: var(--text-primary); font-size: 0.85rem;
    }
    .form-textarea { height: 80px; resize: vertical; }

    .alert { padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.85rem; display: flex; gap: 0.5rem; }
    .alert-info { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }

    .btn-save {
        background: var(--accent); color: white;
        padding: 0.8rem 2rem; border: none; border-radius: 50px;
        font-weight: 600; cursor: pointer; transition: 0.2s;
        box-shadow: 0 4px 15px rgba(108, 99, 255, 0.3);
    }
    .btn-save.floating-save {
        margin-top: 2rem; width: 100%; display: flex; justify-content: center; gap: 0.5rem;
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(108, 99, 255, 0.4); }

    .empty-state { padding: 2rem; text-align: center; color: var(--text-muted); }
    .empty-state i { font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5; }

    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th, .data-table td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid var(--border); font-size: 0.85rem; }
    .data-table th { background: var(--bg-secondary); color: var(--text-secondary); font-weight: 600; }
    .btn-icon { background: none; border: none; color: var(--text-secondary); cursor: pointer; transition: 0.2s; }
    .btn-icon:hover { color: #ed4245; }
</style>
@endsection
