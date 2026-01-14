@extends('layouts.app')

@section('title', 'Custom Commands - SantuyTL')

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa-solid fa-terminal"></i> Custom Commands</h1>
    <p class="page-subtitle">Buat command otomatis dengan mudah. Select command di kiri untuk edit.</p>
</div>

<div class="split-view-container">
    {{-- Sidebar List --}}
    <div class="command-sidebar">
        <div class="sidebar-header">
            <button type="button" class="btn-new-command" onclick="createNewCommand()">
                <i class="fa-solid fa-plus"></i> New Command
            </button>
        </div>
        <div class="command-list" id="commandList">
            @foreach($commands as $cmd)
                <div class="command-item" onclick='selectCommand(@json($cmd))' id="cmd-{{ $cmd->id }}">
                    <div class="cmd-trigger">!{{ $cmd->trigger }}</div>
                    <div class="cmd-preview">{{ Str::limit($cmd->response ?? ($cmd->embed_data['description'] ?? 'Embed Message'), 30) }}</div>
                </div>
            @endforeach
            
            @if($commands->isEmpty())
                <div class="empty-list">
                    <i class="fa-solid fa-terminal"></i>
                    <p>Belum ada command.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Editor Area --}}
    <div class="command-editor">
        <form action="{{ route('custom-commands.store', ['guildId' => request()->route('guildId')]) }}" method="POST" id="commandForm">
            @csrf
            <input type="hidden" name="id" id="commandId">
            
            <div class="embed-grid">
                {{-- Form Inputs --}}
                <div class="editor-panel">
                    <div class="ios-card">
                        <div class="ios-card-header">
                            <span class="ios-card-icon"><i class="fa-solid fa-bolt"></i></span>
                            <h2>Trigger & Response</h2>
                        </div>
                        <div class="ios-list">
                            <div class="ios-list-item column">
                                <label class="form-label">Trigger (tanpa !)</label>
                                <input type="text" name="trigger" id="triggerInput" class="form-input" 
                                       placeholder="contoh: sosmed" required>
                            </div>
                            <div class="ios-list-item column">
                                <label class="form-label">Plain Response</label>
                                <textarea name="response" id="responseInput" class="form-textarea" 
                                          placeholder="Pesan balasan bot..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="ios-card">
                        <div class="ios-card-header">
                            <span class="ios-card-icon"><i class="fa-solid fa-code"></i></span>
                            <h2>Embed Response</h2>
                        </div>
                        <div class="ios-list">
                            <div class="ios-list-item">
                                <label class="form-label">Enable Embed</label>
                                <label class="ios-toggle">
                                    <input type="checkbox" name="is_embed" id="isEmbedWrapper" value="1">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            
                            <div id="embedFields" style="display:none;">
                                <div class="ios-list-item column">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="embed_title" id="embedTitle" class="form-input" placeholder="Title">
                                </div>
                                <div class="ios-list-item column">
                                    <label class="form-label">Description</label>
                                    <textarea name="embed_description" id="embedDesc" class="form-textarea" placeholder="Description..."></textarea>
                                </div>
                                <div class="ios-list-item">
                                    <label class="form-label">Color</label>
                                    <input type="color" name="embed_color" id="embedColor" value="#6c63ff">
                                </div>
                                <div class="ios-list-item column">
                                    <label class="form-label">Image URL</label>
                                    <input type="url" name="embed_image" id="embedImage" class="form-input" placeholder="https://...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Preview --}}
                <div class="preview-panel">
                    <div class="preview-header">
                        <span><i class="fa-solid fa-eye"></i> Preview</span>
                    </div>
                    <div class="preview-container">
                        {{-- Trigger User Msg --}}
                        <div class="discord-msg user-msg">
                            <img src="https://cdn.discordapp.com/embed/avatars/0.png" class="msg-avatar">
                            <div class="msg-content">
                                <span class="msg-user">User</span>
                                <span class="msg-text">!<span id="previewTrigger">command</span></span>
                            </div>
                        </div>

                        {{-- Bot Response --}}
                        <div class="discord-msg bot-msg">
                            <img src="https://cdn.discordapp.com/embed/avatars/1.png" class="msg-avatar">
                            <div class="msg-content">
                                <span class="msg-user bot">SantuyTL <span class="bot-tag">BOT</span></span>
                                <div class="msg-text" id="previewResponse"></div>
                                
                                {{-- Embed Preview --}}
                                <div class="discord-embed" id="previewEmbed" style="display:none;">
                                    <div class="embed-color-bar" id="previewColorBar"></div>
                                    <div class="embed-content">
                                        <div class="embed-title" id="previewEmbedTitle"></div>
                                        <div class="embed-description" id="previewEmbedDesc"></div>
                                        <img class="embed-image" id="previewEmbedImage" src="" style="display:none;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <button type="submit" class="save-button">
                            <i class="fa-solid fa-save"></i> Save Command
                        </button>
                        <button type="button" class="delete-button" id="deleteBtn" onclick="deleteCommand()" style="display:none;">
                            <i class="fa-solid fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </form>

        {{-- Delete Form --}}
        <form id="deleteForm" action="" method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<style>
    .split-view-container {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 1.5rem;
        height: calc(100vh - 140px);
        overflow: hidden;
    }

    /* Sidebar */
    .command-sidebar {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .sidebar-header {
        padding: 1rem;
        border-bottom: 1px solid var(--border);
    }

    .btn-new-command {
        width: 100%;
        padding: 0.75rem;
        background: var(--accent);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .command-list {
        flex: 1;
        overflow-y: auto;
        padding: 0.5rem;
    }

    .command-item {
        padding: 0.75rem;
        border-radius: 8px;
        cursor: pointer;
        margin-bottom: 0.25rem;
        transition: all 0.2s;
    }

    .command-item:hover { background: var(--bg-secondary); }
    .command-item.active { background: rgba(108, 99, 255, 0.1); border: 1px solid var(--accent); }

    .cmd-trigger { font-weight: 600; color: var(--text-primary); }
    .cmd-preview { font-size: 0.8rem; color: var(--text-secondary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    /* Editor */
    .command-editor {
        overflow-y: auto;
        padding-right: 0.5rem;
    }

    /* Reuse Welcome Channel Styles */
    .embed-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 1.5rem;
        align-items: start;
    }

    @media (max-width: 1200px) {
        .split-view-container { grid-template-columns: 1fr; height: auto; overflow: visible; }
        .command-sidebar { height: 300px; }
        .embed-grid { grid-template-columns: 1fr; }
    }

    /* iOS Cards & Inputs (Copied from welcome-channel) */
    .ios-card { background: var(--bg-card); border-radius: 16px; border: 1px solid var(--border); margin-bottom: 1rem; overflow: hidden; }
    .ios-card-header { padding: 1rem; background: rgba(108, 99, 255, 0.08); border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 0.75rem; }
    .ios-card-header h2 { font-size: 0.95rem; font-weight: 600; margin: 0; }
    .ios-list-item { padding: 1rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; gap: 1rem; }
    .ios-list-item.column { flex-direction: column; align-items: stretch; gap: 0.5rem; }
    .ios-list-item:last-child { border-bottom: none; }
    
    .form-input, .form-textarea {
        width: 100%; padding: 0.75rem; background: var(--bg-secondary); border: 1px solid var(--border);
        border-radius: 10px; color: var(--text-primary); font-family: inherit;
    }
    .form-label { font-size: 0.85rem; color: var(--text-secondary); }
    
    /* Toggle */
    .ios-toggle { position: relative; width: 50px; height: 30px; }
    .ios-toggle input { display: none; }
    .toggle-slider { position: absolute; top:0; left:0; right:0; bottom:0; background: var(--bg-secondary); border-radius: 30px; cursor: pointer; transition: 0.3s; }
    .toggle-slider::before { content:''; position: absolute; width:24px; height:24px; left:3px; bottom:3px; background: white; border-radius: 50%; transition: 0.3s; }
    .ios-toggle input:checked + .toggle-slider { background: var(--accent); }
    .ios-toggle input:checked + .toggle-slider::before { transform: translateX(20px); }

    /* Preview Panel */
    .preview-panel {
        position: sticky; top: 0;
        background: var(--bg-card); border: 1px solid var(--border); border-radius: 16px;
        overflow: hidden;
    }
    .preview-header { padding: 1rem; border-bottom: 1px solid var(--border); font-weight: 600; }
    .preview-container { padding: 1rem; background: #36393f; min-height: 200px; }

    /* Discord Message Style */
    .discord-msg { display: flex; gap: 0.75rem; margin-bottom: 1rem; align-items: start; }
    .msg-avatar { width: 40px; height: 40px; border-radius: 50%; }
    .msg-content { flex: 1; }
    .msg-user { font-weight: 600; color: white; font-size: 0.95rem; margin-right: 0.25rem; }
    .bot-tag { background: #5865f2; color: white; font-size: 0.65rem; padding: 0.1rem 0.3rem; border-radius: 4px; vertical-align: middle; }
    .msg-text { color: #dcddde; font-size: 0.95rem; line-height: 1.4; white-space: pre-wrap; }

    /* Discord Embed */
    .discord-embed { display: flex; margin-top: 0.5rem; background: #2f3136; border-radius: 4px; overflow: hidden; max-width: 100%; }
    .embed-color-bar { width: 4px; background: #6c63ff; flex-shrink: 0; }
    .embed-content { padding: 0.75rem; flex: 1; }
    .embed-title { color: #00b0f4; font-weight: 600; margin-bottom: 0.5rem; }
    .embed-description { color: #dcddde; font-size: 0.9rem; white-space: pre-wrap; margin-bottom: 0.5rem; }
    .embed-image { max-width: 100%; border-radius: 4px; }

    .action-buttons { padding: 1rem; display: flex; gap: 0.5rem; justify-content: flex-end; }
    .save-button { padding: 0.75rem 1.5rem; background: #10b981; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; flex: 1; }
    .delete-button { padding: 0.75rem; background: #ef4444; color: white; border: none; border-radius: 8px; cursor: pointer; }
    .empty-list { text-align: center; padding: 2rem; color: var(--text-secondary); }
</style>

<script>
    const commandForm = document.getElementById('commandForm');
    const deleteBtn = document.getElementById('deleteBtn');
    const deleteForm = document.getElementById('deleteForm');
    
    // Inputs
    const idInput = document.getElementById('commandId');
    const triggerInput = document.getElementById('triggerInput');
    const responseInput = document.getElementById('responseInput');
    const isEmbedInput = document.getElementById('isEmbedWrapper');
    const embedFields = document.getElementById('embedFields');
    
    // Embed inputs
    const embedTitle = document.getElementById('embedTitle');
    const embedDesc = document.getElementById('embedDesc');
    const embedColor = document.getElementById('embedColor');
    const embedImage = document.getElementById('embedImage');

    // Preview
    const previewTrigger = document.getElementById('previewTrigger');
    const previewResponse = document.getElementById('previewResponse');
    const previewEmbed = document.getElementById('previewEmbed');
    const previewEmbedTitle = document.getElementById('previewEmbedTitle');
    const previewEmbedDesc = document.getElementById('previewEmbedDesc');
    const previewEmbedImage = document.getElementById('previewEmbedImage');
    const previewColorBar = document.getElementById('previewColorBar');

    function updatePreview() {
        previewTrigger.textContent = triggerInput.value || 'command';
        previewResponse.textContent = responseInput.value;
        
        if (isEmbedInput.checked) {
            embedFields.style.display = 'block';
            previewEmbed.style.display = 'flex';
            
            previewEmbedTitle.textContent = embedTitle.value;
            previewEmbedDesc.textContent = embedDesc.value;
            previewColorBar.style.background = embedColor.value;
            
            if (embedImage.value) {
                previewEmbedImage.src = embedImage.value;
                previewEmbedImage.style.display = 'block';
            } else {
                previewEmbedImage.style.display = 'none';
            }
        } else {
            embedFields.style.display = 'none';
            previewEmbed.style.display = 'none';
        }
    }

    // Event Listeners
    [triggerInput, responseInput, embedTitle, embedDesc, embedImage].forEach(e => e.addEventListener('input', updatePreview));
    [isEmbedInput, embedColor].forEach(e => e.addEventListener('change', updatePreview));

    function createNewCommand() {
        // Reset form
        commandForm.reset();
        idInput.value = '';
        deleteBtn.style.display = 'none';
        
        // Remove active class from list
        document.querySelectorAll('.command-item').forEach(el => el.classList.remove('active'));
        
        updatePreview();
    }

    function selectCommand(cmd) {
        // Highlight in list
        document.querySelectorAll('.command-item').forEach(el => el.classList.remove('active'));
        document.getElementById('cmd-' + cmd.id).classList.add('active');

        // Populate Form
        idInput.value = cmd.id;
        triggerInput.value = cmd.trigger;
        responseInput.value = cmd.response;
        
        isEmbedInput.checked = cmd.is_embed;
        
        if (cmd.embed_data) {
            embedTitle.value = cmd.embed_data.title || '';
            embedDesc.value = cmd.embed_data.description || '';
            embedColor.value = cmd.embed_data.color || '#6c63ff';
            embedImage.value = cmd.embed_data.image || '';
        } else {
            embedTitle.value = '';
            embedDesc.value = '';
            embedColor.value = '#6c63ff';
            embedImage.value = '';
        }

        deleteBtn.style.display = 'block';
        
        // Update Delete Action
        deleteForm.action = "/dashboard/" + "{{ request()->route('guildId') }}" + "/custom-commands/" + cmd.id;

        updatePreview();
    }
    
    function deleteCommand() {
        if(confirm('Are you sure you want to delete this command?')) {
            deleteForm.submit();
        }
    }

    // Init
    updatePreview();
</script>
@endsection
