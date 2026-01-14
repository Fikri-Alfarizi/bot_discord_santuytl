@extends('layouts.app')

@section('title', 'Admin - Guilds')

@section('content')
<div class="page-header">
    <h1 class="page-title">🎮 Guild Management</h1>
    <p class="page-subtitle">View and manage all server configurations</p>
</div>

{{-- Stats --}}
<div class="stats-grid">
    <div class="card stat-card">
        <div class="stat-icon">🎮</div>
        <div class="stat-content">
            <div class="card-title">Total Guilds</div>
            <div class="card-value">{{ number_format($stats['total']) }}</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon">👋</div>
        <div class="stat-content">
            <div class="card-title">With Welcome</div>
            <div class="card-value">{{ $stats['with_welcome'] }}</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon">📝</div>
        <div class="stat-content">
            <div class="card-title">With Logs</div>
            <div class="card-value">{{ $stats['with_logs'] }}</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon">📰</div>
        <div class="stat-content">
            <div class="card-title">With News</div>
            <div class="card-value">{{ $stats['with_news'] }}</div>
        </div>
    </div>
</div>

{{-- Guilds Table --}}
<div class="card">
    <h2 style="margin-bottom: 1rem;">📋 All Guild Settings</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Guild ID</th>
                    <th>Welcome</th>
                    <th>Logs</th>
                    <th>News</th>
                    <th>Auto Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($guilds as $guild)
                <tr>
                    <td><code>{{ $guild->guild_id }}</code></td>
                    <td>
                        @if($guild->welcome_channel_id)
                            <span class="status-badge active">✓ Set</span>
                        @else
                            <span class="status-badge">-</span>
                        @endif
                    </td>
                    <td>
                        @if($guild->log_channel_id)
                            <span class="status-badge active">✓ Set</span>
                        @else
                            <span class="status-badge">-</span>
                        @endif
                    </td>
                    <td>
                        @if($guild->news_channel_id)
                            <span class="status-badge active">✓ Set</span>
                        @else
                            <span class="status-badge">-</span>
                        @endif
                    </td>
                    <td>
                        @if($guild->auto_role_id)
                            <span class="status-badge active">✓ Set</span>
                        @else
                            <span class="status-badge">-</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-btns">
                            <button class="btn btn-sm btn-primary" onclick="showDetails('{{ $guild->guild_id }}', {{ json_encode($guild) }})">👁️ View</button>
                            <form action="{{ route('admin.guilds.destroy', $guild->guild_id) }}" method="POST" style="display: inline;"
                                  onsubmit="return confirm('Delete all settings for this guild?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="empty-state">No guild settings found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Guild Details Modal --}}
<div id="guildModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>🎮 Guild Details</h2>
            <button onclick="closeModal()" class="modal-close">&times;</button>
        </div>
        <div class="modal-body" id="modalBody">
            <!-- Filled by JS -->
        </div>
    </div>
</div>

<style>
    .stat-card { display: flex; align-items: center; gap: 1rem; }
    .stat-icon { font-size: 2rem; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; background: rgba(108, 99, 255, 0.15); border-radius: 10px; }
    .status-badge { padding: 0.2rem 0.5rem; border-radius: 20px; font-size: 0.7rem; background: var(--bg-secondary); color: var(--text-secondary); }
    .status-badge.active { background: rgba(72, 187, 120, 0.2); color: var(--success); }
    .action-btns { display: flex; gap: 0.5rem; }
    .btn-sm { padding: 0.4rem 0.75rem; font-size: 0.75rem; }
    .btn-danger { background: var(--danger); color: white; }
    code { background: var(--bg-secondary); padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.7rem; }
    .empty-state { text-align: center; padding: 2rem; color: var(--text-secondary); }
    
    /* Modal */
    .modal { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); display: flex; align-items: center; justify-content: center; z-index: 1000; }
    .modal-content { background: var(--bg-card); border-radius: 16px; width: 90%; max-width: 500px; max-height: 80vh; overflow-y: auto; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid var(--border); }
    .modal-close { background: none; border: none; color: var(--text-secondary); font-size: 1.5rem; cursor: pointer; }
    .modal-body { padding: 1.5rem; }
    .detail-row { display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid var(--border); }
    .detail-row:last-child { border-bottom: none; }
    .detail-label { color: var(--text-secondary); }
    .detail-value { font-weight: 600; font-size: 0.85rem; }
</style>

<script>
function showDetails(guildId, data) {
    const body = document.getElementById('modalBody');
    body.innerHTML = `
        <div class="detail-row"><span class="detail-label">Guild ID</span><span class="detail-value">${guildId}</span></div>
        <div class="detail-row"><span class="detail-label">Welcome Channel</span><span class="detail-value">${data.welcome_channel_id || 'Not set'}</span></div>
        <div class="detail-row"><span class="detail-label">Leave Channel</span><span class="detail-value">${data.leave_channel_id || 'Not set'}</span></div>
        <div class="detail-row"><span class="detail-label">Log Channel</span><span class="detail-value">${data.log_channel_id || 'Not set'}</span></div>
        <div class="detail-row"><span class="detail-label">News Channel</span><span class="detail-value">${data.news_channel_id || 'Not set'}</span></div>
        <div class="detail-row"><span class="detail-label">Request Channel</span><span class="detail-value">${data.request_channel_id || 'Not set'}</span></div>
        <div class="detail-row"><span class="detail-label">Auto Role</span><span class="detail-value">${data.auto_role_id || 'Not set'}</span></div>
    `;
    document.getElementById('guildModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('guildModal').style.display = 'none';
}
</script>
@endsection
