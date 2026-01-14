@extends('layouts.app')

@section('title', 'Settings - SantuyTL')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-gear"></i> Pengaturan Server</h1>
    <p class="page-subtitle">Konfigurasi bot SantuyTL untuk server kamu</p>
</div>

@if(!$selectedGuildId)
<div class="ios-alert warning">
    <span class="ios-alert-icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
    <span>Belum ada server yang dipilih. Logout dan login ulang untuk memilih server.</span>
</div>
@else

<form action="{{ route('dashboard.settings.update', ['guildId' => request()->route('guildId')]) }}" method="POST">
    @csrf

    {{-- Welcome & Auto-Role --}}
    <div class="ios-card">
        <div class="ios-card-header">
            <span class="ios-card-icon"><i class="fa-solid fa-hand"></i></span>
            <h2>Welcome System</h2>
        </div>
        <div class="ios-list">
            <div class="ios-list-item">
                <div class="ios-item-label">
                    <span class="ios-item-icon"><i class="fa-solid fa-hashtag"></i></span>
                    <div>
                        <span class="ios-item-title">Welcome Channel</span>
                        <span class="ios-item-desc">Channel untuk pesan welcome member baru</span>
                    </div>
                </div>
                <select name="welcome_channel_id" class="ios-select">
                    <option value="">Pilih Channel...</option>
                    @foreach($channels as $channel)
                        <option value="{{ $channel['id'] }}" {{ ($settings->welcome_channel_id ?? '') == $channel['id'] ? 'selected' : '' }}>
                            # {{ $channel['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="ios-list-item">
                <div class="ios-item-label">
                    <span class="ios-item-icon"><i class="fa-solid fa-door-open"></i></span>
                    <div>
                        <span class="ios-item-title">Leave Channel</span>
                        <span class="ios-item-desc">Channel untuk notifikasi member keluar</span>
                    </div>
                </div>
                <select name="leave_channel_id" class="ios-select">
                    <option value="">Pilih Channel...</option>
                    @foreach($channels as $channel)
                        <option value="{{ $channel['id'] }}" {{ ($settings->leave_channel_id ?? '') == $channel['id'] ? 'selected' : '' }}>
                            # {{ $channel['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="ios-list-item">
                <div class="ios-item-label">
                    <span class="ios-item-icon"><i class="fa-solid fa-user-tag"></i></span>
                    <div>
                        <span class="ios-item-title">Auto Role</span>
                        <span class="ios-item-desc">Role otomatis saat member join</span>
                    </div>
                </div>
                <select name="auto_role_id" class="ios-select">
                    <option value="">Pilih Role...</option>
                    @foreach($roles as $role)
                        <option value="{{ $role['id'] }}" {{ ($settings->auto_role_id ?? '') == $role['id'] ? 'selected' : '' }}>
                            @ {{ $role['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Logging --}}
    <div class="ios-card">
        <div class="ios-card-header">
            <span class="ios-card-icon"><i class="fa-solid fa-clipboard-list"></i></span>
            <h2>Logging & Moderation</h2>
        </div>
        <div class="ios-list">
            <div class="ios-list-item">
                <div class="ios-item-label">
                    <span class="ios-item-icon"><i class="fa-solid fa-file-lines"></i></span>
                    <div>
                        <span class="ios-item-title">Log Channel</span>
                        <span class="ios-item-desc">Channel untuk log aktivitas bot</span>
                    </div>
                </div>
                <select name="log_channel_id" class="ios-select">
                    <option value="">Pilih Channel...</option>
                    @foreach($channels as $channel)
                        <option value="{{ $channel['id'] }}" {{ ($settings->log_channel_id ?? '') == $channel['id'] ? 'selected' : '' }}>
                            # {{ $channel['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Game & Economy --}}
    <div class="ios-card">
        <div class="ios-card-header">
            <span class="ios-card-icon"><i class="fa-solid fa-gamepad"></i></span>
            <h2>Game & Economy</h2>
        </div>
        <div class="ios-list">
            <div class="ios-list-item">
                <div class="ios-item-label">
                    <span class="ios-item-icon"><i class="fa-solid fa-dice"></i></span>
                    <div>
                        <span class="ios-item-title">Spin Game Channel</span>
                        <span class="ios-item-desc">Channel khusus untuk /spin command</span>
                    </div>
                </div>
                <select name="game_source_channel_id" class="ios-select">
                    <option value="">Semua Channel</option>
                    @foreach($channels as $channel)
                        <option value="{{ $channel['id'] }}" {{ ($settings->game_source_channel_id ?? '') == $channel['id'] ? 'selected' : '' }}>
                            # {{ $channel['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="ios-list-item">
                <div class="ios-item-label">
                    <span class="ios-item-icon"><i class="fa-solid fa-envelope"></i></span>
                    <div>
                        <span class="ios-item-title">Request Channel</span>
                        <span class="ios-item-desc">Channel untuk request game/software</span>
                    </div>
                </div>
                <select name="request_channel_id" class="ios-select">
                    <option value="">Pilih Channel...</option>
                    @foreach($channels as $channel)
                        <option value="{{ $channel['id'] }}" {{ ($settings->request_channel_id ?? '') == $channel['id'] ? 'selected' : '' }}>
                            # {{ $channel['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="ios-list-item">
                <div class="ios-item-label">
                    <span class="ios-item-icon"><i class="fa-solid fa-comments"></i></span>
                    <div>
                        <span class="ios-item-title">General Chat</span>
                        <span class="ios-item-desc">Channel untuk XP dari chatting</span>
                    </div>
                </div>
                <select name="general_chat_channel_id" class="ios-select">
                    <option value="">Semua Channel</option>
                    @foreach($channels as $channel)
                        <option value="{{ $channel['id'] }}" {{ ($settings->general_chat_channel_id ?? '') == $channel['id'] ? 'selected' : '' }}>
                            # {{ $channel['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- News Feed --}}
    <div class="ios-card">
        <div class="ios-card-header">
            <span class="ios-card-icon"><i class="fa-solid fa-newspaper"></i></span>
            <h2>News Feed</h2>
        </div>
        <div class="ios-list">
            <div class="ios-list-item">
                <div class="ios-item-label">
                    <span class="ios-item-icon"><i class="fa-solid fa-rss"></i></span>
                    <div>
                        <span class="ios-item-title">News Channel</span>
                        <span class="ios-item-desc">Channel untuk berita game (Steam, CrackWatch)</span>
                    </div>
                </div>
                <select name="news_channel_id" class="ios-select">
                    <option value="">Nonaktifkan</option>
                    @foreach($channels as $channel)
                        <option value="{{ $channel['id'] }}" {{ ($settings->news_channel_id ?? '') == $channel['id'] ? 'selected' : '' }}>
                            # {{ $channel['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Save Button --}}
    <div class="ios-actions">
        <button type="submit" class="ios-button primary">
            <i class="fa-solid fa-floppy-disk"></i>
            Simpan Pengaturan
        </button>
    </div>
</form>

@if(count($channels) === 0)
<div class="ios-alert info">
    <span class="ios-alert-icon"><i class="fa-solid fa-circle-info"></i></span>
    <span>Tidak dapat memuat channels. Coba logout dan login ulang.</span>
</div>
@endif

@endif

<style>
    /* iOS-style Cards */
    .ios-card {
        background: var(--bg-card);
        border-radius: 16px;
        margin-bottom: 1.25rem;
        overflow: hidden;
        border: 1px solid var(--border);
    }

    .ios-card-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        background: rgba(108, 99, 255, 0.08);
        border-bottom: 1px solid var(--border);
    }

    .ios-card-icon {
        font-size: 1.25rem;
        color: var(--accent);
    }

    .ios-card-header h2 {
        font-size: 0.95rem;
        font-weight: 600;
        margin: 0;
    }

    /* iOS-style List */
    .ios-list {
        display: flex;
        flex-direction: column;
    }

    .ios-list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border);
        gap: 1rem;
    }

    .ios-list-item:last-child {
        border-bottom: none;
    }

    .ios-item-label {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        flex: 1;
        min-width: 0;
    }

    .ios-item-icon {
        font-size: 1.25rem;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(108, 99, 255, 0.12);
        border-radius: 10px;
        flex-shrink: 0;
        color: var(--accent);
    }

    .ios-item-title {
        display: block;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 2px;
    }

    .ios-item-desc {
        display: block;
        font-size: 0.75rem;
        color: var(--text-secondary);
    }

    /* iOS-style Select */
    .ios-select {
        appearance: none;
        -webkit-appearance: none;
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 0.625rem 2rem 0.625rem 0.875rem;
        font-size: 0.85rem;
        color: var(--text-primary);
        min-width: 180px;
        max-width: 220px;
        cursor: pointer;
        transition: all 0.2s;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23a0aec0' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
    }

    .ios-select:hover {
        border-color: var(--accent);
    }

    .ios-select:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.2);
    }

    /* iOS-style Button */
    .ios-actions {
        margin-top: 1.5rem;
        display: flex;
        justify-content: center;
    }

    .ios-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.875rem 2rem;
        border-radius: 14px;
        font-size: 1rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .ios-button.primary {
        background: linear-gradient(135deg, #6c63ff, #a855f7);
        color: white;
        box-shadow: 0 4px 15px rgba(108, 99, 255, 0.35);
    }

    .ios-button.primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(108, 99, 255, 0.5);
    }

    .ios-button.primary:active {
        transform: translateY(0);
    }

    /* iOS-style Alert */
    .ios-alert {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        border-radius: 14px;
        margin-bottom: 1.25rem;
        font-size: 0.9rem;
    }

    .ios-alert.warning {
        background: rgba(237, 137, 54, 0.12);
        border: 1px solid rgba(237, 137, 54, 0.3);
        color: #ed8936;
    }

    .ios-alert.info {
        background: rgba(66, 153, 225, 0.12);
        border: 1px solid rgba(66, 153, 225, 0.3);
        color: #4299e5;
    }

    .ios-alert-icon {
        font-size: 1.25rem;
    }

    /* Responsive */
    @media (max-width: 640px) {
        .ios-list-item {
            flex-direction: column;
            align-items: stretch;
            gap: 0.75rem;
        }

        .ios-select {
            max-width: 100%;
            width: 100%;
        }
    }
</style>
@endsection
