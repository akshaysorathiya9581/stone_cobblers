<div class="header">
    <div class="search-bar">
        <i>🔍</i>
        <input type="text" placeholder="Search..." />
    </div>

    <div class="header-actions">
        <a href="{{ $exportUrl }}" class="header-btn secondary">
            <i>📤</i> {{ $exportLabel ?? 'Export' }}
        </a>

        <a href="{{ $createUrl }}" class="header-btn primary">
            <i>➕</i> {{ $createLabel ?? 'New' }}
        </a>

        <a href="{{ route('admin.profile.edit') }}" class="user-avatar">{{ auth()->user() ? Str::upper(Str::substr(auth()->user()->name ?? 'U',0,2)) : 'U' }}</a>
    </div>
</div>
