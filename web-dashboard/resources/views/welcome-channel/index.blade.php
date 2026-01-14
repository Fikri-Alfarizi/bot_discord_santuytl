@extends('layouts.app')

@section('title', 'Welcome Channel - SantuyTL')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-door-open"></i> Welcome Channel</h1>
    <p class="page-subtitle">Buat pesan sambutan statis atau informasi penting untuk channel tertentu</p>
</div>

<form action="{{ route('welcome-channel.store', ['guildId' => request()->route('guildId')]) }}" method="POST">
    @csrf
    <div class="embed-grid">
        {{-- Editor Panel --}}
        <div class="editor-panel">
            {{-- Channel Selection --}}
            <div class="ios-card">
                <div class="ios-card-header">
                    <span class="ios-card-icon"><i class="fa-solid fa-hashtag"></i></span>
                    <h2>Target Channel</h2>
                </div>
                <div class="ios-list">
                    <div class="ios-list-item">
                        <select name="channel_id" id="channelSelect" class="ios-select full-width" required>
                            <option value="">Pilih Channel...</option>
                            @foreach($channels as $channel)
                                <option value="{{ $channel['id'] }}" {{ isset($welcomeInfo->channel_id) && $welcomeInfo->channel_id == $channel['id'] ? 'selected' : '' }}>
                                    # {{ $channel['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Plain Content --}}
            <div class="ios-card">
                <div class="ios-card-header">
                    <span class="ios-card-icon"><i class="fa-solid fa-comment"></i></span>
                    <h2>Plain Message (Optional)</h2>
                </div>
                <div class="ios-list">
                    <div class="ios-list-item">
                        <textarea name="message_content" id="plainContent" class="form-textarea" 
                                  placeholder="Pesan teks biasa di luar embed...">{{ $welcomeInfo->message_content ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Embed Title & Description --}}
            <div class="ios-card">
                <div class="ios-card-header">
                    <span class="ios-card-icon"><i class="fa-solid fa-file-lines"></i></span>
                    <h2>Embed Content</h2>
                </div>
                <div class="ios-list">
                    <div class="ios-list-item column">
                        <label class="form-label">Title</label>
                        <input type="text" name="embed_title" id="embedTitle" class="form-input" 
                               placeholder="Embed Title" maxlength="256" value="{{ $embedData['title'] ?? '' }}">
                    </div>
                    <div class="ios-list-item column">
                        <label class="form-label">Description</label>
                        <textarea name="embed_description" id="embedDesc" class="form-textarea" 
                                  placeholder="Embed description... (supports **bold**, *italic*, `code`)">{{ $embedData['description'] ?? '' }}</textarea>
                    </div>
                    <div class="ios-list-item">
                        <label class="form-label">Embed Color</label>
                        <div class="color-picker-wrapper">
                            <input type="color" name="embed_color" id="colorPicker" value="{{ $embedData['color'] ?? '#6c63ff' }}">
                            <span class="color-hex" id="colorHex">{{ $embedData['color'] ?? '#6c63ff' }}</span>
                            <div class="color-presets">
                                <button type="button" class="preset" style="background:#6c63ff" data-color="#6c63ff"></button>
                                <button type="button" class="preset" style="background:#5865f2" data-color="#5865f2"></button>
                                <button type="button" class="preset" style="background:#57f287" data-color="#57f287"></button>
                                <button type="button" class="preset" style="background:#fee75c" data-color="#fee75c"></button>
                                <button type="button" class="preset" style="background:#ed4245" data-color="#ed4245"></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Author --}}
            <div class="ios-card">
                <div class="ios-card-header">
                    <span class="ios-card-icon"><i class="fa-solid fa-user"></i></span>
                    <h2>Author (Optional)</h2>
                </div>
                <div class="ios-list">
                    <div class="ios-list-item column">
                        <label class="form-label">Author Name</label>
                        <input type="text" name="embed_author_name" id="authorName" class="form-input" 
                               placeholder="Author name" value="{{ $embedData['author']['name'] ?? '' }}">
                    </div>
                    <div class="ios-list-item column">
                        <label class="form-label">Author Icon URL</label>
                        <input type="url" name="embed_author_icon" id="authorIcon" class="form-input" 
                               placeholder="https://example.com/icon.png" value="{{ $embedData['author']['icon_url'] ?? '' }}">
                    </div>
                </div>
            </div>

            {{-- Images --}}
            <div class="ios-card">
                <div class="ios-card-header">
                    <span class="ios-card-icon"><i class="fa-solid fa-image"></i></span>
                    <h2>Images</h2>
                </div>
                <div class="ios-list">
                    <div class="ios-list-item column">
                        <label class="form-label">Thumbnail URL (kecil, kanan atas)</label>
                        <input type="url" name="embed_thumbnail" id="thumbnailUrl" class="form-input" 
                               placeholder="https://example.com/thumbnail.png" value="{{ $embedData['thumbnail'] ?? '' }}">
                    </div>
                    <div class="ios-list-item column">
                        <label class="form-label">Image URL (besar, di bawah)</label>
                        <input type="url" name="embed_image" id="imageUrl" class="form-input" 
                               placeholder="https://example.com/image.png" value="{{ $embedData['image'] ?? '' }}">
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="ios-card">
                <div class="ios-card-header">
                    <span class="ios-card-icon"><i class="fa-solid fa-thumbtack"></i></span>
                    <h2>Footer</h2>
                </div>
                <div class="ios-list">
                    <div class="ios-list-item column">
                        <label class="form-label">Footer Text</label>
                        <input type="text" name="embed_footer_text" id="footerText" class="form-input" 
                               placeholder="Footer text" value="{{ $embedData['footer']['text'] ?? '' }}">
                    </div>
                    <div class="ios-list-item">
                        <label class="form-label">Add Timestamp</label>
                        <label class="ios-toggle">
                            <input type="checkbox" name="add_timestamp" id="addTimestamp" value="1" 
                                   {{ isset($embedData['timestamp']) && $embedData['timestamp'] ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Preview Panel --}}
        <div class="preview-panel">
            <div class="preview-header">
                <span><i class="fa-solid fa-eye"></i> Live Preview</span>
                <span class="preview-badge">Real-time</span>
            </div>
            <div class="preview-container" id="previewContainer">
                 {{-- Plain Message Preview --}}
                 <div class="preview-plain-message" id="previewPlainMessage" style="display: none;"></div>
                
                 {{-- Discord Embed --}}
                 <div class="discord-embed" id="embedPreview">
                     <div class="embed-color-bar" id="previewColorBar"></div>
                     <div class="embed-content">
                         {{-- Author --}}
                         <div class="embed-author" id="previewAuthor" style="display: none;">
                             <img class="author-icon" id="previewAuthorIcon" src="" alt="" onerror="this.style.display='none'">
                             <span id="previewAuthorName"></span>
                         </div>
                         
                         {{-- Body with thumbnail --}}
                         <div class="embed-body">
                             <div class="embed-text">
                                 <div class="embed-title" id="previewTitle">Embed Title</div>
                                 <div class="embed-description" id="previewDescription">Description akan muncul di sini...</div>
                             </div>
                             <img class="embed-thumbnail" id="previewThumbnail" src="" alt="" style="display: none;" onerror="this.style.display='none'">
                         </div>
                         
                         {{-- Large Image --}}
                         <img class="embed-image" id="previewImage" src="" alt="" style="display: none;" onerror="this.style.display='none'">
                         
                         {{-- Footer --}}
                         <div class="embed-footer" id="previewFooter" style="display: none;">
                             <span id="previewFooterText"></span>
                             <span id="previewTimestamp"></span>
                         </div>
                     </div>
                 </div>
            </div>

            <button type="submit" class="send-button mb-3">
                <i class="fa-solid fa-save"></i>
                Save Configuration
            </button>
</form> 
            {{-- Separate Deploy Form --}}
            <form action="{{ route('welcome-channel.deploy', ['guildId' => request()->route('guildId')]) }}" method="POST">
                @csrf
                <button type="submit" class="deploy-button">
                    <i class="fa-solid fa-paper-plane"></i>
                    Deploy Message
                </button>
            </form>
        </div>
    </div>


<style>
    .embed-grid {
        display: grid;
        grid-template-columns: 1fr 420px;
        gap: 2rem;
        align-items: start;
    }

    @media (max-width: 1200px) {
        .embed-grid { grid-template-columns: 1fr; }
        .preview-panel { order: -1; position: relative !important; }
    }

    .editor-panel {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    /* iOS Cards */
    .ios-card {
        background: var(--bg-card);
        border-radius: 16px;
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

    .ios-card-icon { font-size: 1.25rem; }
    .ios-card-header h2 { font-size: 0.95rem; font-weight: 600; margin: 0; }

    .ios-list { display: flex; flex-direction: column; }

    .ios-list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border);
        gap: 1rem;
    }

    .ios-list-item:last-child { border-bottom: none; }

    .ios-list-item.column {
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
    }

    .form-label { font-size: 0.85rem; color: var(--text-secondary); }

    .form-input, .form-textarea, .ios-select {
        width: 100%;
        padding: 0.75rem 1rem;
        background: var(--bg-secondary);
        border: 1px solid var(--border);
        border-radius: 10px;
        color: var(--text-primary);
        font-size: 0.9rem;
        font-family: inherit;
    }

    .form-input:focus, .form-textarea:focus, .ios-select:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.15);
    }

    .form-textarea { min-height: 80px; resize: vertical; }

    .ios-select.full-width { max-width: 100%; }

    .color-picker-wrapper {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    input[type="color"] {
        width: 45px;
        height: 45px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        padding: 0;
    }

    .color-hex { font-family: monospace; color: var(--text-secondary); font-size: 0.85rem; }

    .color-presets {
        display: flex;
        gap: 0.5rem;
    }

    .preset {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2px solid transparent;
        cursor: pointer;
        transition: all 0.2s;
    }

    .preset:hover { transform: scale(1.15); }

    /* iOS Toggle */
    .ios-toggle {
        position: relative;
        width: 50px;
        height: 30px;
    }

    .ios-toggle input { display: none; }

    .toggle-slider {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: var(--bg-secondary);
        border-radius: 30px;
        cursor: pointer;
        transition: 0.3s;
    }

    .toggle-slider::before {
        content: '';
        position: absolute;
        width: 24px;
        height: 24px;
        left: 3px;
        bottom: 3px;
        background: white;
        border-radius: 50%;
        transition: 0.3s;
    }

    .ios-toggle input:checked + .toggle-slider { background: var(--accent); }
    .ios-toggle input:checked + .toggle-slider::before { transform: translateX(20px); }

    /* Preview Panel */
    .preview-panel {
        position: sticky;
        top: 1rem;
    }

    .preview-header {
        background: var(--bg-card);
        padding: 1rem 1.25rem;
        border-radius: 16px 16px 0 0;
        border: 1px solid var(--border);
        border-bottom: none;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .preview-badge {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 700;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }

    .preview-container {
        background: #36393f;
        padding: 1rem;
        min-height: 280px;
        border: 1px solid var(--border);
        border-top: none;
    }

    .preview-plain-message {
        color: #dcddde;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        white-space: pre-wrap;
    }

    /* Discord Embed Preview */
    .discord-embed {
        display: flex;
        max-width: 100%;
        border-radius: 4px;
        overflow: hidden;
        background: #2f3136;
    }

    .embed-color-bar {
        width: 4px;
        background: #6c63ff;
        flex-shrink: 0;
    }

    .embed-content {
        padding: 0.75rem 1rem;
        flex: 1;
        min-width: 0;
    }

    .embed-author {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
        color: #ffffff;
    }

    .author-icon {
        width: 24px;
        height: 24px;
        border-radius: 50%;
    }

    .embed-body {
        display: flex;
        gap: 1rem;
    }

    .embed-text { flex: 1; min-width: 0; }

    .embed-title {
        font-weight: 600;
        color: #00b0f4;
        margin-bottom: 0.5rem;
        font-size: 1rem;
    }

    .embed-description {
        font-size: 0.9rem;
        color: #dcddde;
        white-space: pre-wrap;
        word-wrap: break-word;
        line-height: 1.4;
    }

    .embed-thumbnail {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 4px;
        flex-shrink: 0;
    }

    .embed-image {
        max-width: 100%;
        max-height: 300px;
        border-radius: 4px;
        margin-top: 1rem;
        object-fit: contain;
    }

    .embed-footer {
        margin-top: 0.75rem;
        font-size: 0.75rem;
        color: #72767d;
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .send-button {
        width: 100%;
        padding: 1rem;
        background: var(--accent);
        border: none;
        border-radius: 0 0 16px 16px;
        color: white;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        transition: all 0.3s;
    }

    .send-button:hover {
        filter: brightness(1.1);
    }
    
    .deploy-button {
        width: 100%;
        padding: 1rem;
        background: #10b981;
        border: none;
        border-radius: 16px;
        color: white;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        transition: all 0.3s;
        margin-top: 1rem;
    }
    
    .deploy-button:hover {
        filter: brightness(1.1);
        transform: translateY(-2px);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all input elements
    const plainContent = document.getElementById('plainContent');
    const embedTitle = document.getElementById('embedTitle');
    const embedDesc = document.getElementById('embedDesc');
    const colorPicker = document.getElementById('colorPicker');
    const authorName = document.getElementById('authorName');
    const authorIcon = document.getElementById('authorIcon');
    const thumbnailUrl = document.getElementById('thumbnailUrl');
    const imageUrl = document.getElementById('imageUrl');
    const footerText = document.getElementById('footerText');
    const addTimestamp = document.getElementById('addTimestamp');

    // Preview elements
    const previewPlainMessage = document.getElementById('previewPlainMessage');
    const previewColorBar = document.getElementById('previewColorBar');
    const previewTitle = document.getElementById('previewTitle');
    const previewDescription = document.getElementById('previewDescription');
    const previewAuthor = document.getElementById('previewAuthor');
    const previewAuthorName = document.getElementById('previewAuthorName');
    const previewAuthorIcon = document.getElementById('previewAuthorIcon');
    const previewThumbnail = document.getElementById('previewThumbnail');
    const previewImage = document.getElementById('previewImage');
    const previewFooter = document.getElementById('previewFooter');
    const previewFooterText = document.getElementById('previewFooterText');
    const previewTimestamp = document.getElementById('previewTimestamp');
    const colorHex = document.getElementById('colorHex');

    // Update preview function
    function updatePreview() {
        // Plain message
        if (plainContent.value) {
            previewPlainMessage.style.display = 'block';
            previewPlainMessage.textContent = plainContent.value;
        } else {
            previewPlainMessage.style.display = 'none';
        }

        // Color
        previewColorBar.style.background = colorPicker.value;
        colorHex.textContent = colorPicker.value;

        // Title
        previewTitle.textContent = embedTitle.value || 'Embed Title';
        previewTitle.style.color = embedTitle.value ? '#00b0f4' : '#72767d';

        // Description
        previewDescription.textContent = embedDesc.value || 'Description akan muncul di sini...';
        previewDescription.style.color = embedDesc.value ? '#dcddde' : '#72767d';

        // Author
        if (authorName.value) {
            previewAuthor.style.display = 'flex';
            previewAuthorName.textContent = authorName.value;
            if (authorIcon.value) {
                previewAuthorIcon.src = authorIcon.value;
                previewAuthorIcon.style.display = 'block';
            } else {
                previewAuthorIcon.style.display = 'none';
            }
        } else {
            previewAuthor.style.display = 'none';
        }

        // Thumbnail
        if (thumbnailUrl.value) {
            previewThumbnail.src = thumbnailUrl.value;
            previewThumbnail.style.display = 'block';
        } else {
            previewThumbnail.style.display = 'none';
        }

        // Image
        if (imageUrl.value) {
            previewImage.src = imageUrl.value;
            previewImage.style.display = 'block';
        } else {
            previewImage.style.display = 'none';
        }

        // Footer
        if (footerText.value || addTimestamp.checked) {
            previewFooter.style.display = 'flex';
            previewFooterText.textContent = footerText.value;
            if (addTimestamp.checked) {
                const now = new Date();
                previewTimestamp.textContent = (footerText.value ? ' • ' : '') + 
                    now.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) + 
                    ' ' + now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            } else {
                previewTimestamp.textContent = '';
            }
        } else {
            previewFooter.style.display = 'none';
        }
    }

    // Bind events to all inputs
    [plainContent, embedTitle, embedDesc, authorName, authorIcon, thumbnailUrl, imageUrl, footerText].forEach(el => {
        el.addEventListener('input', updatePreview);
        el.addEventListener('change', updatePreview);
    });

    colorPicker.addEventListener('input', updatePreview);
    colorPicker.addEventListener('change', updatePreview);
    addTimestamp.addEventListener('change', updatePreview);

    // Color presets
    document.querySelectorAll('.preset').forEach(btn => {
        btn.addEventListener('click', function() {
            colorPicker.value = this.dataset.color;
            updatePreview();
        });
    });

    // Initial update
    updatePreview();

    // Update timestamp every second if enabled
    setInterval(() => {
        if (addTimestamp.checked) {
            updatePreview();
        }
    }, 1000);
});
</script>
@endsection
