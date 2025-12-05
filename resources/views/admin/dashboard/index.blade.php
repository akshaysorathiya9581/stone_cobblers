@extends('layouts.admin')

@section('title', 'Dashboard')

@push('css')
@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <button class="sidebar-toggle">
                <i class="fas fa-bars toggle-icon"></i>
            </button>
            <!-- <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search customers, email, phone...">
            </div> -->

            <div class="header-actions">
                <!-- <a href="#export" class="header-btn secondary" role="button">
                    <i class="fas fa-download"></i> Export
                </a> -->

                <a href="{{ route('admin.customers.create') }}" class="header-btn primary" role="button">
                    <i class="fas fa-plus"></i> New Customer
                </a>

                <a href="{{ route('admin.profile.edit') }}" class="user-avatar" aria-label="Open profile">BM</a>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="content-header">
                <h1 class="content-title">Dashboard</h1>
                <div class="action-buttons">
                    <!-- <a href="#" class="btn secondary" role="button">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a> -->
                    <a href="{{ route('admin.customers.create') }}" class="btn primary" role="button">
                        <i class="fas fa-plus"></i> Add Customer
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
                    <div class="value">{{ ($pendingQuotes) ? $pendingQuotes['total'] : 0 }}</div>
                    <div class="change">-3% this week</div>
                </div>
                <div class="stat-card">
                    <h3>Revenue This Month</h3>
                    <div class="value">$127,450</div>
                    <div class="change">+18% vs last month</div>
                </div>
            </div>

            <!-- Company Settings Quick View -->
            <div class="company-settings-card">
                <div class="company-settings-header">
                    <h3 class="company-settings-title">Company Settings</h3>
                    <a href="{{ route('admin.settings.index') }}" class="company-settings-link">Edit Settings <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="company-settings-grid">
                    <div class="company-settings-item">
                        <strong class="company-settings-label">Company:</strong> {{ setting('company_name', 'Stone Cobblers Inc.') }}
                    </div>
                    <div class="company-settings-item">
                        <strong class="company-settings-label">Tax Rate:</strong> {{ setting('tax_rate', 0.08) * 100 }}%
                    </div>
                    <div class="company-settings-item">
                        <strong class="company-settings-label">Quote Prefix:</strong> {{ setting('quote_prefix', 'QT') }}
                    </div>
                    <div class="company-settings-item">
                        <strong class="company-settings-label">Quote Expiry:</strong> {{ setting('quote_expiry_days', 30) }} days
                    </div>
                    <div class="company-settings-item">
                        <strong class="company-settings-label">Currency:</strong> {{ setting('currency_symbol', '$') }} ({{ setting('currency_code', 'USD') }})
                    </div>
                    <div class="company-settings-item">
                        <strong class="company-settings-label">Email:</strong> {{ setting('company_email', 'info@stonecobblers.com') }}
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tabs">
                <button class="tab active" data-status="all">All Customers</button>
                <button class="tab" data-status="template">Template</button>
                <button class="tab" data-status="fabricate">Fabricate</button>
                <button class="tab" data-status="install">Install</button>
                <button class="tab" data-status="followup">Follow-up</button>
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
                                <td class="projects-count">
                                    {{ $customer->project_name ?? 'No Project' }}
                                </td>

                                <!-- Status -->
                                <td>
                                    <span class="status-tag status-{{ strtolower($customer->status ?? 'standard') }}">
                                        {{ strtoupper($customer->status ?? 'Standard') }}
                                    </span>
                                </td>

                                <!-- Priority -->
                                <td class="normal-text">
                                    <span class="priority-tag priority-{{ strtolower($customer->priority ?? 'normal') }}">
                                        {{ ucfirst($customer->priority ?? 'Normal') }}
                                    </span>
                                </td>

                                <!-- Last Updated -->
                                <td class="last-contact">
                                    {{ $customer->updated_at ? $customer->updated_at->diffForHumans() : 'Never' }}
                                </td>

                                <!-- Actions -->
                                <td class="actions">
                                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="action-btn view"
                                        title="View"><i class="fa-solid fa-eye"></i></a>
                                    <a href="{{ route('admin.customers.edit', $customer->id) }}" class="action-btn edit"
                                        title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Empty state -->
                <div class="no-records">No customers found for this status.</div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')

    <script>
        $(document).ready(function () {
            $('.tabs .tab').click(function () {
                $('.tabs .tab').removeClass('active');
                $(this).addClass('active');

                const status = $(this).data('status');

                if (status === 'all') {
                    $('tbody tr').show();
                } else {
                    $('tbody tr').hide();
                    $(`tbody tr[data-status="${status}"]`).show();
                }

                const visibleRows = $('tbody tr:visible').length;
                $('.no-records').toggle(visibleRows === 0);
            });

            // ✅ Run once on page load for default active tab
            const defaultStatus = $('.tabs .tab.active').data('status');
            if (defaultStatus === 'all') {
                $('tbody tr').show();
            } else {
                $('tbody tr').hide();
                $(`tbody tr[data-status="${defaultStatus}"]`).show();
            }
            const visibleRows = $('tbody tr:visible').length;
            $('.no-records').toggle(visibleRows === 0);
        });
    </script>

@endpush