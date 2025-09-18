@extends('layouts.admin')

@section('title','Dashboard')

@push('css')
@endpush

@section('content')
<!-- Main Content -->
<div class="main-content">
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

            <a href="{{ route('admin.profile.edit') }}" class="user-avatar" aria-label="Open profile">BM</a>
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

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab active">All Customers</button>
            <button class="tab">Template</button>
            <button class="tab">Fabricate</button>
            <button class="tab">Install</button>
            <button class="tab">Follow-up</button>
        </div>

        <div class="customers-table custom-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Project</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer)
                        <tr data-status="{{ strtolower($customer->status) }}">
                            <!-- Customer -->
                            <td class="customer-info">
                                <div class="customer-avatar">
                                    {{ strtoupper(substr($customer->first_name, 0, 1)) }}{{ strtoupper(substr($customer->last_name, 0, 1)) }}
                                </div>
                                <div class="customer-details">
                                    <h4>{{ $customer->first_name }} {{ $customer->last_name }}</h4>
                                    <p>{{ $customer->email }}</p>
                                </div>
                            </td>

                            <!-- Project -->
                            <td>
                               {{ $customer->project_name ?? 'No Project' }}
                            </td>

                            <!-- Status -->
                            <td>
                                <span class="status-tag status-{{ strtolower($customer->status ?? 'standard') }}">
                                    {{ strtoupper($customer->status ?? 'Standard') }}
                                </span>
                            </td>

                            <!-- Priority -->
                            <td>
                                <span class="priority-tag priority-{{ strtolower($customer->priority ?? 'normal') }}">
                                    {{ ucfirst($customer->priority ?? 'Normal') }}
                                </span>
                            </td>

                            <!-- Last Updated -->
                            <td>
                                {{ $customer->updated_at ? $customer->updated_at->diffForHumans() : 'Never' }}
                            </td>

                            <!-- Actions -->
                            <td class="actions">
                                <a href="{{ route('admin.customers.show', $customer->id) }}" class="action-btn view">View</a>
                                <a href="{{ route('admin.customers.edit', $customer->id) }}" class="action-btn update">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Empty state -->
            <div class="no-customers" style="display:none; padding:18px; text-align:center; color:#666;">
                No customers found for this status.
            </div>
        </div>


    </div>
</div>
@endsection

@push('scripts')
    <script>
        // Tab functionality
    </script>
@endpush
