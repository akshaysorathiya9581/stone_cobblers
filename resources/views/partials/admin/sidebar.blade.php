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
                            $currentRouteName = request()->route() ? request()->route()->getName() : '';
                            $currentPath = '/' . ltrim(request()->path(), '/');
                            
                            // PRIORITY 1: If item has match_path, use it for EXACT segment matching
                            if (isset($item['match_path']) && !empty($item['match_path'])) {
                                // Extract path segments for exact matching
                                $pathSegments = array_filter(explode('/', $currentPath));
                                $matchSegments = array_filter(explode('/', $item['match_path']));
                                
                                // Check if the match_path segments exist in the current path in exact order
                                $matchFound = false;
                                foreach ($pathSegments as $index => $segment) {
                                    // Check if this segment exactly matches the match_path
                                    if ($segment === $item['match_path']) {
                                        $matchFound = true;
                                        break;
                                    }
                                }
                                
                                $isActive = $matchFound;
                            }
                            
                            // PRIORITY 2: Check exact route match
                            if (!$isActive) {
                                $isActive = request()->routeIs($item['route']);
                            }
                            
                            // PRIORITY 3: Check with wildcard (only if no match_path defined)
                            if (!$isActive && !isset($item['match_path'])) {
                                $isActive = request()->routeIs($item['route'] . '.*');
                            }
                            
                            // PRIORITY 4: Check if current route name starts with menu route name (only if no match_path)
                            if (!$isActive && $currentRouteName && !isset($item['match_path'])) {
                                $isActive = str_starts_with($currentRouteName, str_replace('.index', '', $item['route']));
                            }
                            
                            // PRIORITY 5: URL path comparison (only if no match_path)
                            if (!$isActive && $item['route'] && !isset($item['match_path'])) {
                                try {
                                    $routePath = parse_url(route($item['route']), PHP_URL_PATH);
                                    // Normalize paths for comparison
                                    $routePath = '/' . ltrim($routePath, '/');
                                    $currentPath = '/' . ltrim($currentPath, '/');
                                    
                                    // Check if current path starts with route path
                                    $isActive = str_starts_with($currentPath, $routePath);
                                    
                                    // Additional check: extract base segment and compare
                                    if (!$isActive) {
                                        $routeSegments = array_filter(explode('/', $routePath));
                                        $currentSegments = array_filter(explode('/', $currentPath));
                                        
                                        // Compare first 2-3 segments
                                        if (count($routeSegments) >= 2 && count($currentSegments) >= 2) {
                                            $isActive = $routeSegments[0] === $currentSegments[0] && 
                                                       $routeSegments[1] === $currentSegments[1];
                                        }
                                    }
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
