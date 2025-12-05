<div class="header">
    <button class="sidebar-toggle">
        <i class="fas fa-bars toggle-icon"></i>
    </button>
    <!-- <div class="search-bar">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search..." />
    </div> -->

    <div class="header-actions">
        <!-- <a href="{{ $exportUrl }}" class="header-btn secondary">
            <i class="fas fa-download"></i> {{ $exportLabel ?? 'Export' }}
        </a> -->

        @if(!empty($createUrl))
        <a href="{{ $createUrl }}" class="header-btn primary">
            <i class="fas fa-plus"></i> {{ $createLabel ?? 'New' }}
        </a>
        @endif

        <a href="{{ route('admin.profile.edit') }}"
            class="user-avatar">{{ auth()->user() ? Str::upper(Str::substr(auth()->user()->name ?? 'U', 0, 2)) : 'U' }}</a>
    </div>
</div>