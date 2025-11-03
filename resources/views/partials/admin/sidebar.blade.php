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
                        // Check if current route matches the item's route
                        $isActive = false;
                        if (isset($item['route'])) {
                            // Check exact match first
                            $isActive = request()->routeIs($item['route']);
                            
                            // If not exact match, check with wildcard
                            if (!$isActive) {
                                $isActive = request()->routeIs($item['route'] . '.*');
                            }
                            
                            // Also check if URL path starts with the route path
                            if (!$isActive && $item['route']) {
                                try {
                                    $routePath = parse_url(route($item['route']), PHP_URL_PATH);
                                    $currentPath = request()->path();
                                    $isActive = str_starts_with('/' . $currentPath, $routePath);
                                } catch (\Exception $e) {
                                    // Route doesn't exist, skip
                                }
                            }
                        }
                        
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
