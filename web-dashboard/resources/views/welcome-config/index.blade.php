@extends('layouts.app')

@section('title', 'Welcome & Goodbye - SantuyTL')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-hand"></i> Welcome & Goodbye</h1>
    <p class="page-subtitle">Sambut member baru dan kirim pesan saat member keluar</p>
</div>

{{-- Variables Reference --}}
<div class="variables-card">
    <div class="variables-header">
        <i class="fa-solid fa-file-lines"></i> Available Variables
    </div>
    <div class="variables-list">
        <code>{user}</code> - Mention user
        <code>{username}</code> - Username
        <code>{server}</code> - Server name
        <code>{membercount}</code> - Total member
    </div>
</div>

<form action="{{ route('welcome.update', ['guildId' => request()->route('guildId')]) }}" method="POST" id="welcomeForm">
    @csrf

    <div class="config-grid">
        {{-- Welcome Message Section --}}
        <div class="config-section">
            <div class="section-header welcome">
                <div class="section-icon"><i class="fa-solid fa-hand"></i></div>
                <div class="section-info">
                    <h2>Welcome Message</h2>
                    <p>Pesan otomatis saat member bergabung</p>
                </div>
                <label class="toggle-switch">
                    <input type="checkbox" name="welcome_enabled" id="welcomeEnabled" value="1" 
                           {{ ($settings->welcome_enabled ?? false) ? 'checked' : '' }}>
                    <span class="slider"></span>
                </label>
            </div>

            <div class="section-body" id="welcomeBody">
                {{-- Channel --}}
                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-hashtag"></i> Welcome Channel</label>
                    <select name="welcome_channel_id" id="welcomeChannel" class="form-select">
                        <option value="">Pilih Channel...</option>
                        @foreach($channels as $channel)
                            <option value="{{ $channel['id'] }}" {{ ($settings->welcome_channel_id ?? '') == $channel['id'] ? 'selected' : '' }}>
                                # {{ $channel['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Plain Message --}}
                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-comment"></i> Welcome Message</label>
                    <textarea name="welcome_message" id="welcomeMessage" class="form-textarea" 
                              placeholder="Selamat datang {user} di {server}! 🎉">{{ $settings->welcome_message ?? '' }}</textarea>
                </div>

                {{-- Embed Toggle --}}
                <div class="form-group inline">
                    <label class="form-label"><i class="fa-solid fa-code"></i> Enable Embed</label>
                    <label class="toggle-switch small">
                        <input type="checkbox" name="welcome_embed_enabled" id="welcomeEmbedEnabled" value="1"
                               {{ ($settings->welcome_embed_enabled ?? false) ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                </div>

                {{-- Embed Options --}}
                <div class="embed-options" id="welcomeEmbedOptions">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Embed Title</label>
                            <input type="text" name="welcome_embed_title" id="welcomeEmbedTitle" class="form-input"
                                   value="{{ $settings->welcome_embed_title ?? '' }}" placeholder="Welcome to the server!">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Embed Color</label>
                            <div class="color-input-wrapper">
                                <input type="color" name="welcome_embed_color" id="welcomeEmbedColor" 
                                       value="{{ $settings->welcome_embed_color ?? '#5865f2' }}">
                                <span class="color-hex">{{ $settings->welcome_embed_color ?? '#5865f2' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Embed Description</label>
                        <textarea name="welcome_embed_description" id="welcomeEmbedDesc" class="form-textarea"
                                  placeholder="Hey {user}! Selamat bergabung di {server}! 🎊">{{ $settings->welcome_embed_description ?? '' }}</textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label"><i class="fa-solid fa-image"></i> Image URL (besar)</label>
                            <input type="url" name="welcome_embed_image" id="welcomeEmbedImage" class="form-input"
                                   value="{{ $settings->welcome_embed_image ?? '' }}" placeholder="https://example.com/welcome.png">
                        </div>
                        <div class="form-group">
                            <label class="fa-label"><i class="fa-regular fa-square"></i> Thumbnail URL (kecil)</label>
                            <input type="url" name="welcome_embed_thumbnail" id="welcomeEmbedThumbnail" class="form-input"
                                   value="{{ $settings->welcome_embed_thumbnail ?? '' }}" placeholder="https://example.com/icon.png">
                        </div>
                    </div>
                </div>

                {{-- DM Option --}}
                <div class="form-group inline">
                    <label class="form-label"><i class="fa-solid fa-envelope"></i> Send DM to new member</label>
                    <label class="toggle-switch small">
                        <input type="checkbox" name="welcome_dm_enabled" id="welcomeDmEnabled" value="1"
                               {{ ($settings->welcome_dm_enabled ?? false) ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="dm-options" id="welcomeDmOptions">
                    <div class="form-group">
                        <label class="form-label">DM Message</label>
                        <textarea name="welcome_dm_message" id="welcomeDmMessage" class="form-textarea"
                                  placeholder="Hai {username}! Terima kasih sudah join {server}!">{{ $settings->welcome_dm_message ?? '' }}</textarea>
                    </div>
                </div>

                {{-- Test Button --}}
                <button type="button" class="btn btn-test" onclick="testMessage('welcome')">
                    <i class="fa-solid fa-flask"></i> Test Welcome Message
                </button>
            </div>
        </div>

        {{-- Live Preview --}}
        <div class="preview-section">
            <div class="preview-header">
                <span><i class="fa-solid fa-eye"></i> Live Preview</span>
                <span class="preview-badge">Real-time</span>
            </div>
            <div class="preview-container">
                <div class="discord-message">
                    <div class="message-avatar"><i class="fa-solid fa-robot"></i></div>
                    <div class="message-content">
                        <div class="message-author">SantuyTL Bot <span class="bot-badge">BOT</span></div>
                        <div class="message-text" id="previewPlainMessage"></div>
                        
                        <div class="discord-embed" id="previewEmbed" style="display: none;">
                            <div class="embed-color-bar" id="previewColorBar"></div>
                            <div class="embed-body">
                                <div class="embed-main">
                                    <div class="embed-title" id="previewEmbedTitle"></div>
                                    <div class="embed-description" id="previewEmbedDesc"></div>
                                </div>
                                <img class="embed-thumbnail" id="previewThumbnail" src="" style="display: none;">
                            </div>
                            <img class="embed-image" id="previewImage" src="" style="display: none;">
                            <div class="embed-footer">
                                <span id="previewTimestamp"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Goodbye Section --}}
    <div class="config-section" style="margin-top: 2rem;">
        <div class="section-header goodbye">
            <div class="section-icon"><i class="fa-solid fa-door-open"></i></div>
            <div class="section-info">
                <h2>Goodbye Message</h2>
                <p>Pesan otomatis saat member keluar</p>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" name="goodbye_enabled" id="goodbyeEnabled" value="1"
                       {{ ($settings->goodbye_enabled ?? false) ? 'checked' : '' }}>
                <span class="slider"></span>
            </label>
        </div>

        <div class="section-body" id="goodbyeBody">
            <div class="form-group">
                <label class="form-label"><i class="fa-solid fa-hashtag"></i> Goodbye Channel</label>
                <select name="goodbye_channel_id" id="goodbyeChannel" class="form-select">
                    <option value="">Pilih Channel...</option>
                    @foreach($channels as $channel)
                        <option value="{{ $channel['id'] }}" {{ ($settings->goodbye_channel_id ?? '') == $channel['id'] ? 'selected' : '' }}>
                            # {{ $channel['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label"><i class="fa-solid fa-comment"></i> Goodbye Message</label>
                <textarea name="goodbye_message" id="goodbyeMessage" class="form-textarea"
                          placeholder="Goodbye {username}! Semoga kita bertemu lagi 👋">{{ $settings->goodbye_message ?? '' }}</textarea>
            </div>

            <button type="button" class="btn btn-test" onclick="testMessage('goodbye')">
                <i class="fa-solid fa-flask"></i> Test Goodbye Message
            </button>
        </div>
    </div>

    {{-- Auto Role Section --}}
    <div class="config-section" style="margin-top: 2rem;">
        <div class="section-header autorole">
            <div class="section-icon"><i class="fa-solid fa-user-tag"></i></div>
            <div class="section-info">
                <h2>Auto Role</h2>
                <p>Berikan role otomatis ke member baru</p>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" name="auto_role_enabled" id="autoRoleEnabled" value="1"
                       {{ ($settings->auto_role_enabled ?? false) ? 'checked' : '' }}>
                <span class="slider"></span>
            </label>
        </div>

        <div class="section-body" id="autoRoleBody">
            <div class="form-group">
                <label class="form-label"><i class="fa-solid fa-at"></i> Auto Role</label>
                <select name="auto_role_id" id="autoRoleId" class="form-select">
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

    {{-- Save Button --}}
    <div class="save-bar">
        <button type="submit" class="btn btn-save">
            <i class="fa-solid fa-floppy-disk"></i> Save All Settings
        </button>
    </div>
</form>

<style>
    .variables-card {
        background: rgba(88, 101, 242, 0.1);
        border: 1px solid rgba(88, 101, 242, 0.3);
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-bottom: 2rem;
    }
    .variables-header { font-weight: 600; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem; }
    .variables-list { display: flex; flex-wrap: wrap; gap: 0.75rem; }
    .variables-list code { background: var(--bg-secondary); padding: 0.2rem 0.6rem; border-radius: 6px; font-size: 0.8rem; }

    .config-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 2rem;
        align-items: start;
    }
    @media (max-width: 1200px) { 
        .config-grid { grid-template-columns: 1fr; }
        .preview-section { order: -1; }
    }

    .config-section {
        background: var(--bg-card);
        border-radius: 16px;
        border: 1px solid var(--border);
        overflow: hidden;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        border-bottom: 1px solid var(--border);
    }
    .section-header.welcome { background: linear-gradient(135deg, rgba(88, 101, 242, 0.1), rgba(87, 242, 135, 0.05)); }
    .section-header.goodbye { background: linear-gradient(135deg, rgba(237, 66, 69, 0.1), rgba(254, 231, 92, 0.05)); }
    .section-header.autorole { background: linear-gradient(135deg, rgba(235, 69, 158, 0.1), rgba(254, 215, 0, 0.05)); }
    
    .section-icon { font-size: 2rem; }
    .section-info { flex: 1; }
    .section-info h2 { font-size: 1rem; margin: 0 0 0.25rem; }
    .section-info p { font-size: 0.8rem; color: var(--text-secondary); margin: 0; }

    .section-body { padding: 1.25rem; }

    .toggle-switch { position: relative; width: 50px; height: 28px; }
    .toggle-switch.small { width: 44px; height: 24px; }
    .toggle-switch input { display: none; }
    .toggle-switch .slider {
        position: absolute; top: 0; left: 0; right: 0; bottom: 0;
        background: var(--bg-secondary); border-radius: 28px;
        cursor: pointer; transition: 0.3s;
    }
    .toggle-switch .slider::before {
        content: ''; position: absolute;
        width: 22px; height: 22px; left: 3px; bottom: 3px;
        background: white; border-radius: 50%; transition: 0.3s;
    }
    .toggle-switch.small .slider::before { width: 18px; height: 18px; }
    .toggle-switch input:checked + .slider { background: var(--success); }
    .toggle-switch input:checked + .slider::before { transform: translateX(22px); }
    .toggle-switch.small input:checked + .slider::before { transform: translateX(20px); }

    .form-group { margin-bottom: 1rem; }
    .form-group.inline { display: flex; justify-content: space-between; align-items: center; }
    .form-label { display: block; font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.4rem; }
    .form-input, .form-select, .form-textarea {
        width: 100%; padding: 0.7rem 1rem;
        background: var(--bg-secondary); border: 1px solid var(--border);
        border-radius: 10px; color: var(--text-primary); font-size: 0.9rem;
    }
    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none; border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.15);
    }
    .form-textarea { min-height: 80px; resize: vertical; font-family: inherit; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    @media (max-width: 768px) { .form-row { grid-template-columns: 1fr; } }

    .color-input-wrapper { display: flex; align-items: center; gap: 0.75rem; }
    .color-input-wrapper input[type="color"] { width: 45px; height: 40px; border: none; border-radius: 8px; cursor: pointer; }
    .color-hex { font-family: monospace; font-size: 0.85rem; color: var(--text-secondary); }

    .embed-options, .dm-options { 
        background: var(--bg-secondary); border-radius: 10px; 
        padding: 1rem; margin-top: 0.75rem; margin-bottom: 1rem;
    }

    .btn-test {
        background: rgba(88, 101, 242, 0.15); color: #5865f2;
        padding: 0.6rem 1rem; border: none; border-radius: 8px;
        cursor: pointer; font-weight: 600; transition: 0.2s;
    }
    .btn-test:hover { background: rgba(88, 101, 242, 0.25); }

    /* Preview */
    .preview-section { position: sticky; top: 1rem; }
    .preview-header {
        background: var(--bg-card); padding: 1rem;
        border-radius: 12px 12px 0 0; border: 1px solid var(--border); border-bottom: none;
        display: flex; justify-content: space-between; align-items: center;
    }
    .preview-badge { background: rgba(87, 242, 135, 0.15); color: #57f287; padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.65rem; font-weight: 700; }
    .preview-container { background: #36393f; padding: 1rem; border: 1px solid var(--border); border-top: none; border-radius: 0 0 12px 12px; min-height: 200px; }

    .discord-message { display: flex; gap: 1rem; }
    .message-avatar { width: 40px; height: 40px; background: var(--accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; }
    .message-content { flex: 1; min-width: 0; }
    .message-author { font-weight: 600; margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.5rem; }
    .bot-badge { background: #5865f2; color: white; font-size: 0.6rem; padding: 0.1rem 0.35rem; border-radius: 3px; font-weight: 500; }
    .message-text { color: #dcddde; font-size: 0.9rem; margin-bottom: 0.5rem; white-space: pre-wrap; }

    .discord-embed { display: flex; flex-direction: column; background: #2f3136; border-radius: 4px; overflow: hidden; margin-top: 0.25rem; max-width: 100%; }
    .embed-color-bar { width: 100%; height: 4px; background: #5865f2; }
    .embed-body { padding: 0.75rem; display: flex; gap: 1rem; }
    .embed-main { flex: 1; min-width: 0; }
    .embed-title { font-weight: 600; color: #00b0f4; margin-bottom: 0.35rem; }
    .embed-description { font-size: 0.85rem; color: #dcddde; white-space: pre-wrap; }
    .embed-thumbnail { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }
    .embed-image { max-width: 100%; max-height: 200px; border-radius: 0 0 4px 4px; }
    .embed-footer { padding: 0.5rem 0.75rem; font-size: 0.7rem; color: #72767d; }

    .save-bar { margin-top: 2rem; text-align: center; }
    .btn-save {
        background: linear-gradient(135deg, #5865f2, #eb459e);
        color: white; padding: 1rem 3rem; border: none; border-radius: 12px;
        font-size: 1rem; font-weight: 700; cursor: pointer; transition: 0.3s;
    }
    .btn-save:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(88, 101, 242, 0.4); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle visibility based on enable switches
    function toggleSection(enableInput, bodyId) {
        const body = document.getElementById(bodyId);
        if (body) {
            body.style.opacity = enableInput.checked ? '1' : '0.5';
            body.style.pointerEvents = enableInput.checked ? 'auto' : 'none';
        }
    }

    // Embed options toggle
    function toggleEmbedOptions() {
        const enabled = document.getElementById('welcomeEmbedEnabled').checked;
        document.getElementById('welcomeEmbedOptions').style.display = enabled ? 'block' : 'none';
    }

    function toggleDmOptions() {
        const enabled = document.getElementById('welcomeDmEnabled').checked;
        document.getElementById('welcomeDmOptions').style.display = enabled ? 'block' : 'none';
    }

    // Event listeners
    document.getElementById('welcomeEnabled').addEventListener('change', function() { toggleSection(this, 'welcomeBody'); });
    document.getElementById('goodbyeEnabled').addEventListener('change', function() { toggleSection(this, 'goodbyeBody'); });
    document.getElementById('autoRoleEnabled').addEventListener('change', function() { toggleSection(this, 'autoRoleBody'); });
    document.getElementById('welcomeEmbedEnabled').addEventListener('change', toggleEmbedOptions);
    document.getElementById('welcomeDmEnabled').addEventListener('change', toggleDmOptions);

    // Initial states
    toggleSection(document.getElementById('welcomeEnabled'), 'welcomeBody');
    toggleSection(document.getElementById('goodbyeEnabled'), 'goodbyeBody');
    toggleSection(document.getElementById('autoRoleEnabled'), 'autoRoleBody');
    toggleEmbedOptions();
    toggleDmOptions();

    // Live preview
    function updatePreview() {
        const message = document.getElementById('welcomeMessage').value || 'Selamat datang @User di Server! 🎉';
        const embedEnabled = document.getElementById('welcomeEmbedEnabled').checked;
        const embedTitle = document.getElementById('welcomeEmbedTitle').value;
        const embedDesc = document.getElementById('welcomeEmbedDesc').value;
        const embedColor = document.getElementById('welcomeEmbedColor').value;
        const embedImage = document.getElementById('welcomeEmbedImage').value;
        const embedThumbnail = document.getElementById('welcomeEmbedThumbnail').value;

        // Replace placeholders for preview
        const replaceVars = (text) => text
            .replace(/{user}/g, '<@User>')
            .replace(/{username}/g, 'User')
            .replace(/{server}/g, '{{ session("selected_guild.name", "Server") }}')
            .replace(/{membercount}/g, '100');

        // Update plain message
        document.getElementById('previewPlainMessage').textContent = replaceVars(message);

        // Update embed
        const embedEl = document.getElementById('previewEmbed');
        if (embedEnabled && (embedTitle || embedDesc)) {
            embedEl.style.display = 'block';
            document.getElementById('previewColorBar').style.background = embedColor;
            document.getElementById('previewEmbedTitle').textContent = replaceVars(embedTitle);
            document.getElementById('previewEmbedDesc').textContent = replaceVars(embedDesc);
            
            const thumbEl = document.getElementById('previewThumbnail');
            if (embedThumbnail) { thumbEl.src = embedThumbnail; thumbEl.style.display = 'block'; }
            else { thumbEl.style.display = 'none'; }

            const imgEl = document.getElementById('previewImage');
            if (embedImage) { imgEl.src = embedImage; imgEl.style.display = 'block'; }
            else { imgEl.style.display = 'none'; }

            document.getElementById('previewTimestamp').textContent = new Date().toLocaleString('id-ID');
        } else {
            embedEl.style.display = 'none';
        }

        // Update color hex display
        document.querySelector('.color-hex').textContent = embedColor;
    }

    // Bind events
    ['welcomeMessage', 'welcomeEmbedEnabled', 'welcomeEmbedTitle', 'welcomeEmbedDesc', 'welcomeEmbedColor', 'welcomeEmbedImage', 'welcomeEmbedThumbnail'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', updatePreview);
            el.addEventListener('change', updatePreview);
        }
    });

    updatePreview();
});

function testMessage(type) {
    const channelId = type === 'welcome' 
        ? document.getElementById('welcomeChannel').value 
        : document.getElementById('goodbyeChannel').value;
    
    if (!channelId) {
        alert('Pilih channel terlebih dahulu!');
        return;
    }

    // Create hidden form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("welcome.test", ["guildId" => request()->route("guildId")]) }}';
    form.innerHTML = `
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="channel_id" value="${channelId}">
        <input type="hidden" name="type" value="${type}">
    `;
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection
