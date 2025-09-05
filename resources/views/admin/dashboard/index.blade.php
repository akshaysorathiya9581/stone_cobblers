@extends('layouts.admin')

@section('title','Dashboard')

@push('css')
@endpush

@section('content')
    <!-- Header -->
    <div class="header">
        <div class="search-bar">
            <i>🔍</i>
            <input type="text" placeholder="Search customers, email, phone...">
        </div>

        <div class="header-actions">
            <a href="#export" class="header-btn secondary" role="button">
                <i>📤</i> Export
            </a>

            <a href="{{ route('admin.customers.create') }}" class="header-btn primary" role="button">
                <i>➕</i> New Customer
            </a>

            <a href="/account" class="user-avatar" aria-label="Open profile">BM</a>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="content-header">
            <h1 class="content-title">Dashboard</h1>
            <div class="action-buttons">
                <a href="#" class="btn secondary" role="button">
                    <i>📊</i> Reports
                </a>
                <a href="{{ route('admin.customers.create') }}" class="btn primary" role="button">
                    <i>➕</i> Add Customer
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Customers</h3>
                <div class="value">{{ $totalCustomers }}</div>
                <div class="change">+12% this month</div>
            </div>
            <div class="stat-card">
                <h3>Active Projects</h3>
                <div class="value">{{ $activeProjects }}</div>
                <div class="change">+5% this week</div>
            </div>
            <div class="stat-card">
                <h3>Pending Quotes</h3>
                <div class="value">23</div>
                <div class="change">-3% this week</div>
            </div>
            <div class="stat-card">
                <h3>Revenue This Month</h3>
                <div class="value">$127,450</div>
                <div class="change">+18% vs last month</div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Tab functionality
    </script>
@endpush
