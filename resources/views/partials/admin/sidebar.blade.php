<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <div class="logo-icon">SC</div>
            Stone Cobblers
        </div>
    </div>

    <div class="nav-section">
        <h3>Main</h3>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="icon-dashboard"></i>
            Dashboard
        </a>
        <a href="{{ route('admin.customers.index') }}" class="nav-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
            <i class="icon-customers"></i>
            Customers
        </a>
        <a href="{{ route('admin.projects.index') }}" class="nav-item {{ request()->routeIs('admin.projects.*') ? 'active' : '' }}">
            <i class="icon-projects"></i>
            Projects
        </a>
        <a href="{{ route('admin.quotes.index') }}" class="nav-item {{ request()->routeIs('admin.quotes.*') ? 'active' : '' }}">
            <i class="icon-quotes"></i>
            Quotes
        </a>
    </div>

    <div class="nav-section">
        <h3>Management</h3>
        <a href="#" class="nav-item">
            <i class="icon-reports"></i>
            Reports
        </a>
        <a href="#" class="nav-item">
            <i class="icon-settings"></i>
            Settings
        </a>
        <a href="{{ route('admin.files.index') }}" class="nav-item {{ request()->routeIs('admin.files.*') ? 'active' : '' }}">
            <i class="icon-files"></i>
            Files
        </a>
    </div>

    <div class="nav-section">
        <h3>Quick Access</h3>
        <a href="#" class="nav-item">
            <i class="icon-starred"></i>
            Starred
        </a>
        <a href="#" class="nav-item">
            <i class="icon-pinned"></i>
            Pinned
        </a>
    </div>

    <div class="nav-section">
        <h3>Account</h3>
        <a href="#" class="nav-item"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="icon-logout"></i>
            Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</div>
