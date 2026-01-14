@extends('layouts.app')

@section('title', 'Ticketing System - SantuyTL')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-ticket"></i> Ticketing System</h1>
    <p class="page-subtitle">Kelola sistem tiket untuk support dan laporan member.</p>
</div>

<div class="app-layout">
    {{-- Config Card --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-message"></i> Embed Panel Config</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('ticketing.store', ['guildId' => request()->route('guildId')]) }}" method="POST">
                @csrf
                
                <div class="form-group mb-3">
                    <label class="form-label">Support Role ID (Yang bisa lihat tiket)</label>
                    <input type="text" name="support_role_id" class="form-input" 
                           value="{{ $config->support_role_id ?? '' }}" placeholder="123456789012345678" required>
                    <small class="text-muted">ID Role Moderator/Support.</small>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Ticket Category ID</label>
                    <input type="text" name="category_id" class="form-input" 
                           value="{{ $config->category_id ?? '' }}" placeholder="123456789012345678" required>
                    <small class="text-muted">ID Kategori dimana channel tiket akan dibuat.</small>
                </div>
                
                <div class="form-group mb-3">
                    <label class="form-label">Log Channel ID</label>
                    <input type="text" name="log_channel_id" class="form-input" 
                           value="{{ $config->log_channel_id ?? '' }}" placeholder="123456789012345678" required>
                    <small class="text-muted">Channel untuk mengirim transkrip tiket setelah ditutup.</small>
                </div>

                <hr class="separator">

                <div class="form-group mb-3">
                    <label class="form-label">Embed Title</label>
                    <input type="text" name="ticket_embed_title" class="form-input" 
                           value="{{ $config->ticket_embed_title ?? 'Support Ticket' }}">
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Embed Description</label>
                    <textarea name="ticket_embed_description" class="form-textarea" rows="3">{{ $config->ticket_embed_description ?? 'Klik tombol di bawah untuk membuka tiket bantuan.' }}</textarea>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Embed Color</label>
                    <input type="color" name="ticket_embed_color" 
                           value="{{ $config->ticket_embed_color ?? '#6c63ff' }}" class="form-control-color">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Deploy Panel Card --}}
    <div class="card">
        <div class="card-header">
            <h3><i class="fa-solid fa-paper-plane"></i> Deploy Panel</h3>
        </div>
        <div class="card-body">
            <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                Kirim pesan panel tiket ke channel tertentu agar member bisa mulai membuat tiket.
            </p>

            <form action="{{ route('ticketing.deploy', ['guildId' => request()->route('guildId')]) }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label class="form-label">Channel ID</label>
                    <input type="text" name="channel_id" class="form-input" placeholder="Channel ID target..." required>
                </div>
                
                <button type="submit" class="btn btn-success" style="width: 100%;">
                    <i class="fa-solid fa-rocket"></i> Send Panel
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
    
    @media (max-width: 900px) {
        .app-layout { grid-template-columns: 1fr; }
    }

    .form-group { margin-bottom: 1.25rem; }
    .form-label { display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: 0.9rem; }
    .form-input, .form-textarea {
        width: 100%;
        padding: 0.75rem;
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: 8px;
        color: var(--text-primary);
        font-family: inherit;
    }
    .text-muted { font-size: 0.8rem; color: var(--text-secondary); }
    
    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: 0.2s;
    }
    .btn-primary { background: var(--accent); color: white; }
    .btn-success { background: #10b981; color: white; }
    .btn:hover { opacity: 0.9; transform: translateY(-1px); }

    .separator { border: 0; border-top: 1px solid var(--border); margin: 1.5rem 0; }
</style>
@endsection
