@extends('layouts.app')

@section('title', 'Level Rewards - SantuyTL')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-trophy"></i> Level Rewards</h1>
    <p class="page-subtitle">Atur role otomatis saat user naik level</p>
</div>

<div class="rewards-container">
    {{-- Add New Reward Form --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-plus-circle"></i> Tambah Reward</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('levels.store', ['guildId' => request()->route('guildId')]) }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label>Level Target</label>
                        <input type="number" name="level" class="form-input" placeholder="Contoh: 5" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Role Reward</label>
                        <select name="role_id" class="form-select" required>
                            <option value="" disabled selected>Pilih Role...</option>
                            @foreach($roles as $role)
                                <option value="{{ $role['id'] }}" style="color: {{ $role['color'] ? '#' . dechex($role['color']) : 'inherit' }}">
                                    {{ $role['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group btn-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save"></i> Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Existing Rewards List --}}
    <div class="card mt-4">
        <div class="card-header">
            <h3><i class="fa-solid fa-list-ul"></i> Daftar Reward Aktif</h3>
        </div>
        <div class="card-body p-0">
            @if($rewards->isEmpty())
                <div class="empty-state">
                    <i class="fa-solid fa-ghost"></i>
                    <p>Belum ada reward yang diset. Tambahin dong puh!</p>
                </div>
            @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Level</th>
                            <th>Role Reward</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rewards as $reward)
                            @php
                                $roleName = 'Unknown Role';
                                $roleColor = 'inherit';
                                foreach($roles as $r) {
                                    if($r['id'] == $reward->role_id) {
                                        $roleName = $r['name'];
                                        $roleColor = $r['color'] ? '#' . dechex($r['color']) : 'inherit';
                                        break;
                                    }
                                }
                            @endphp
                            <tr>
                                <td class="fw-bold">Level {{ $reward->level }}</td>
                                <td>
                                    <span class="role-badge" style="border-color: {{ $roleColor }}; color: {{ $roleColor }}">
                                        @if($roleColor !== 'inherit') <span class="dot" style="background: {{ $roleColor }}"></span> @endif
                                        {{ $roleName }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('levels.destroy', ['guildId' => request()->route('guildId'), 'id' => $reward->id]) }}" method="POST" onsubmit="return confirm('Hapus reward level {{ $reward->level }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon delete"><i class="fa-solid fa-trash"></i></button>
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
    .card { background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border); overflow: hidden; }
    .card-header { padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); background: rgba(255,255,255,0.02); }
    .card-header h3 { font-size: 1rem; margin: 0; display: flex; align-items: center; gap: 0.5rem; }
    .card-body { padding: 1.5rem; }
    .card-body.p-0 { padding: 0; }

    .form-row { display: grid; grid-template-columns: 1fr 2fr auto; gap: 1rem; align-items: end; }
    .form-group label { display: block; font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 0.4rem; }
    .form-input, .form-select {
        width: 100%; padding: 0.8rem; background: var(--bg-secondary);
        border: 1px solid var(--border); border-radius: 8px; color: var(--text-primary);
    }
    
    .btn { padding: 0.8rem 1.5rem; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 0.5rem; }
    .btn-primary { background: var(--accent); color: white; }
    .btn-primary:hover { filter: brightness(1.1); }

    .role-badge { 
        display: inline-flex; align-items: center; gap: 0.4rem; 
        padding: 0.3rem 0.8rem; border-radius: 20px; 
        background: rgba(255,255,255,0.05); border: 1px solid transparent;
        font-size: 0.85rem; font-weight: 500;
    }
    .role-badge .dot { width: 8px; height: 8px; border-radius: 50%; }

    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th, .data-table td { padding: 1rem 1.5rem; text-align: left; border-bottom: 1px solid var(--border); }
    .data-table th { background: rgba(0,0,0,0.2); font-weight: 600; color: var(--text-secondary); font-size: 0.85rem; }
    
    .btn-icon { background: none; border: none; color: var(--text-secondary); cursor: pointer; transition: 0.2s; }
    .btn-icon:hover { color: #ef4444; }

    .empty-state { text-align: center; padding: 3rem; color: var(--text-muted); }
    .empty-state i { font-size: 2.5rem; margin-bottom: 1rem; opacity: 0.5; }

    @media (max-width: 768px) {
        .form-row { grid-template-columns: 1fr; }
    }
</style>
@endsection
