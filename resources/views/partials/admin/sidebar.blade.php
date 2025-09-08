<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <div class="logo-icon">SC</div>
            Stone Cobblers
        </div>
    </div>
    @if(isset($sidebarMenuSections) && $sidebarMenuSections->count())
        @foreach($sidebarMenuSections as $sectionTitle => $items)
            <div class="nav-section">
                <h3>{{ $sectionTitle }}</h3>

                @foreach($items as $item)
                    @php
                        $isActive = isset($item['route']) && request()->routeIs($item['route'] . '*');
                        // special-case logout item: we render a form trigger
                        $isLogout = isset($item['is_logout']) && $item['is_logout'];
                        $href = $item['route'] ? (route($item['route']) ) : '#';
                    @endphp

                    @if($isLogout)
                        <a href="#" class="nav-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="{{ $item['icon'] }}"></i>
                            {{ $item['title'] }}
                        </a>
                    @else
                        <a href="{{ $item['route'] ? $href : '#' }}"
                           class="nav-item {{ $isActive ? 'active' : '' }}">
                            <i class="{{ $item['icon'] }}"></i>
                            {{ $item['title'] }}
                        </a>
                    @endif
                @endforeach
            </div>
        @endforeach
    @endif

    {{-- Logout form (hidden) --}}
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>
