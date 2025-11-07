<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <div class="logo-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22 11C22.5523 11 23 11.4477 23 12C23 12.5523 22.5523 13 22 13L2 13C1.44772 13 1 12.5523 1 12C1 11.4477 1.44772 11 2 11L22 11Z" fill="white"/>
                    <path d="M3 20L3 12C3 11.4477 3.44772 11 4 11C4.55228 11 5 11.4477 5 12L5 20L5.00488 20.0986C5.02757 20.3276 5.12883 20.5429 5.29297 20.707C5.48051 20.8946 5.73478 21 6 21L18 21C18.2652 21 18.5195 20.8946 18.707 20.707C18.8946 20.5195 19 20.2652 19 20V12C19 11.4477 19.4477 11 20 11C20.5523 11 21 11.4477 21 12L21 20C21 20.7957 20.6837 21.5585 20.1211 22.1211C19.5585 22.6837 18.7957 23 18 23L6 23C5.20435 23 4.44152 22.6837 3.87891 22.1211C3.38671 21.6289 3.08291 20.9835 3.01465 20.2969L3 20Z" fill="white"/>
                    <path d="M19.7578 3.03029C20.2935 2.89649 20.8358 3.22211 20.9697 3.75783C21.1035 4.29355 20.7779 4.83581 20.2422 4.96974L4.24221 8.96974C3.70649 9.10354 3.16422 8.77792 3.03029 8.2422C2.8965 7.70648 3.22211 7.16422 3.75783 7.03029L19.7578 3.03029Z" fill="white"/>
                    <path d="M11.5598 1.0903C11.9418 0.994634 12.3392 0.973946 12.7288 1.0317C13.1195 1.08968 13.4947 1.22477 13.8333 1.42819C14.1717 1.63157 14.4668 1.8996 14.7014 2.21725C14.9355 2.5344 15.1046 2.89519 15.1995 3.2778L15.6497 5.0776C15.7836 5.61339 15.4579 6.15654 14.9221 6.29049C14.3866 6.42409 13.8443 6.09831 13.7102 5.56295L13.26 3.76217L13.259 3.76022C13.2275 3.63243 13.1712 3.51164 13.093 3.40573C13.0148 3.29977 12.9159 3.20989 12.803 3.14205C12.6902 3.07435 12.565 3.02951 12.4348 3.01022C12.3046 2.99094 12.1718 2.99761 12.0442 3.02975L12.0403 3.03073L10.0999 3.5112L10.0989 3.51022C9.97359 3.54255 9.8561 3.59994 9.75223 3.67721C9.64682 3.75567 9.55784 3.85441 9.49051 3.96725C9.42328 4.07996 9.37867 4.20459 9.35965 4.33444C9.34541 4.43196 9.34502 4.53128 9.35965 4.62838L9.37918 4.72506L9.38016 4.72897L9.83036 6.53854L9.84989 6.63912C9.92192 7.14088 9.60337 7.62552 9.10086 7.75045C8.56503 7.88343 8.02314 7.55679 7.88993 7.02096L7.43973 5.21139L7.44071 5.21041C7.345 4.82968 7.32341 4.43387 7.38016 4.04537C7.43716 3.65541 7.57079 3.2803 7.77274 2.94186C7.97472 2.60342 8.24174 2.30805 8.55789 2.07272C8.87401 1.83747 9.23342 1.66711 9.61551 1.57077L9.61942 1.56881L11.5598 1.0903Z" fill="white"/>
                </svg>
            </div>
            <span class="logo-text">Stone Cobblers</span>
        </div>
        <button class="sidebar-toggle">
            <i class="fas fa-bars toggle-icon"></i>
        </button>
    </div>
    @if(isset($sidebarMenuSections) && $sidebarMenuSections->count())
        @foreach($sidebarMenuSections as $sectionTitle => $items)
            <div class="nav-section">
                <h3 class="nav-section-title">{{ $sectionTitle }}</h3>

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
                        <a href="#" class="nav-item" data-title="{{ $item['title'] }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="{{ $item['icon'] }}"></i>
                            <span>{{ $item['title'] }}</span>
                        </a>
                    @else
                        <a href="{{ $item['route'] ? $href : '#' }}"
                           class="nav-item {{ $isActive ? 'active' : '' }}" data-title="{{ $item['title'] }}">
                            <i class="{{ $item['icon'] }}"></i>
                            <span>{{ $item['title'] }}</span>
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
