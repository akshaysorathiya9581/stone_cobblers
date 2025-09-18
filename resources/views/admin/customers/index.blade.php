@extends('layouts.admin')

@section('title', 'Customers')

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
            <h1 class="content-title">Customer Management</h1>
            <div class="action-buttons">
                <a href="/reports" class="btn secondary" role="button">
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
                <h3>Active Customers</h3>
                <div class="value">{{ $activeCustomers }}</div>
                <div class="change">+8% this month</div>
            </div>
            <div class="stat-card">
                <h3>VIP Customers</h3>
                <div class="value">{{ $vipCustomers }}</div>
                <div class="change">+5% this month</div>
            </div>
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <div class="value">$2.4M</div>
                <div class="change">+18% vs last year</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab active" data-status="all">All Projects</button>

            @foreach (get_customer_status_list() as $status)
            <button class="tab" data-status="{{ strtolower($status['id']) }}">
                {{ $status['text'] }}
            </button>
            @endforeach
        </div>

        <!-- Customers Table -->
        <div class="customers-table custom-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Contact</th>
                        <th>Total Value</th>
                        <th>Projects</th>
                        <th>Status</th>
                        <th>Last Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer)
                    <tr data-status="{{ strtolower($customer->status) }}">
                        <!-- Customer Info -->
                        <td class="customer-info">
                            <div class="customer-avatar">
                                {{ strtoupper(substr($customer->first_name, 0, 1)) }}{{ strtoupper(substr($customer->last_name, 0, 1)) }}
                            </div>
                            <div class="customer-details">
                                <h4>{{ $customer->first_name }} {{ $customer->last_name }}</h4>
                                <p>{{ $customer->project_name ?? 'No Project' }}</p>
                            </div>
                        </td>

                        <!-- Contact Info -->
                        <td class="contact-info">
                            <div>
                                📧 <a href="mailto:{{ $customer->email }}" class="email">{{ $customer->email }}</a>
                            </div>
                            <div>
                                📞 {{ $customer->phone ?? 'N/A' }}
                            </div>
                        </td>

                        <!-- Total Value -->
                        <td class="total-value">
                            ${{ number_format($customer->total_value ?? 0, 2) }}
                        </td>

                        <!-- Projects Count -->
                        <td class="projects-count">
                            {{ $customer->projects_count ?? 0 }} Projects
                        </td>

                        <!-- Status -->
                        <td>
                            <span class="status-tag status-{{ strtolower($customer->status ?? 'standard') }}">
                                {{ strtoupper($customer->status ?? 'Standard') }}
                            </span>
                        </td>

                        <!-- Last Contact -->
                        <td class="last-contact">
                            {{ $customer->last_contact ? \Carbon\Carbon::parse($customer->last_contact)->diffForHumans() : 'Never' }}
                        </td>

                        <!-- Actions -->
                        <td class="actions">
                            <button class="action-btn view">View</button>
                            <button class="action-btn contact">Contact</button>
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
    $(function() {
        var $tabs = $('.tabs .tab'),
            $rows = $('.customers-table table tbody tr'), // ✅ changed selector
            $empty = $('.no-customers');

        function norm(s) {
            return (s || '').toString().trim();
        }

        // compute badges
        function updateBadges() {
            var counts = {
                all: 0
            };
            $rows.each(function() {
                var st = norm($(this).data('status'));
                counts.all = (counts.all || 0) + 1;
                counts[st] = (counts[st] || 0) + 1;
            });
            $tabs.each(function() {
                var st = $(this).data('status') || 'all';
                $(this).find('.tab-badge').text(counts[st] || 0)
                    .css({
                        'margin-left': '8px',
                        'font-size': '0.85em',
                        'padding': '2px 6px',
                        'border-radius': '999px',
                        'background': '#fff'
                    });
            });
        }

        // show/hide rows by status and update empty state
        function showStatus(status) {
            status = norm(status);
            if (status === 'all') $rows.show();
            else $rows.each(function() {
                $(this).toggle(norm($(this).data('status')) === status);
            });
            // empty state
            $empty.toggle($rows.filter(':visible').length === 0);
        }

        // tab click
        $tabs.on('click', function() {
            var $t = $(this),
                status = $t.data('status') || norm($t.text());
            $tabs.removeClass('active');
            $t.addClass('active');
            showStatus(status);
            localStorage.setItem('customerTab', status);
        });

        // init
        updateBadges();
        var saved = localStorage.getItem('customerTab') || 'all';
        var $init = $tabs.filter('[data-status="' + saved + '"]').first();
        if ($init.length) {
            $init.addClass('active').trigger('click');
        } else {
            $tabs.first().trigger('click');
        }
    });
</script>
@endpush