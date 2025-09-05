@extends('layouts.admin')

@section('title', 'Customers')

@push('css')
    <style>
        /* Customers Table */
        .customers-table {
            background: white;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            overflow: hidden;
        }

        .table-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr 120px;
            padding: 15px 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
            font-weight: 600;
            font-size: 14px;
        }

        .table-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr 120px;
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
            align-items: center;
            transition: background-color 0.2s;
        }

        .table-row:hover {
            background-color: #f8f9fa;
        }

        .customer-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .customer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgb(22, 163, 74);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        .customer-details h4 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .customer-details p {
            font-size: 12px;
            color: #666;
        }

        .contact-info {
            font-size: 12px;
            color: #666;
        }

        .contact-info .email {
            color: rgb(22, 163, 74);
            text-decoration: none;
        }

        .total-value {
            font-weight: 600;
            font-size: 14px;
            color: #28a745;
        }

        .projects-count {
            font-weight: 500;
            font-size: 14px;
            color: #333;
        }

        .status-tag {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            text-align: center;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-prospect {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-vip {
            background-color: #cce5ff;
            color: #004085;
        }

        .last-contact {
            font-size: 12px;
            color: #666;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: background-color 0.2s;
        }

        .action-btn.edit {
            background-color: #e8f5e8;
            color: rgb(22, 163, 74);
        }

        .action-btn.view {
            background-color: #f8f9fa;
            color: #666;
        }

        .action-btn.contact {
            background-color: #d1ecf1;
            color: #0c5460;
        }
    </style>
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

                <a href="/account" class="user-avatar" aria-label="Open profile">BM</a>
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
            <div class="customers-table">
                <div class="table-header">
                    <div>Customer</div>
                    <div>Contact</div>
                    <div>Total Value</div>
                    <div>Projects</div>
                    <div>Status</div>
                    <div>Last Contact</div>
                    <div>Actions</div>
                </div>

                <div class="table">
                    @foreach ($customers as $customer)
                        <div class="table-row" data-status="{{ strtolower($customer->status) }}">
                            <!-- Customer Info -->
                            <div class="customer-info">
                                <div class="customer-avatar">
                                    {{ strtoupper(substr($customer->first_name, 0, 1)) }}{{ strtoupper(substr($customer->last_name, 0, 1)) }}
                                </div>
                                <div class="customer-details">
                                    <h4>{{ $customer->first_name }} {{ $customer->last_name }}</h4>
                                    <p>{{ $customer->project_name ?? 'No Project' }}</p>
                                </div>
                            </div>

                            <!-- Contact Info -->
                            <div class="contact-info">
                                <div>
                                    📧 <a href="mailto:{{ $customer->email }}" class="email">{{ $customer->email }}</a>
                                </div>
                                <div>
                                    📞 {{ $customer->phone ?? 'N/A' }}
                                </div>
                            </div>

                            <!-- Total Value -->
                            <div class="total-value">
                                ${{ number_format($customer->projects_sum_budget ?? 0, 2) }}
                            </div>

                            <!-- Projects Count -->
                            <div class="projects-count">
                                {{ $customer->projects_count ?? 0 }} Projects
                            </div>

                            <!-- Status -->
                            <div>
                                <span class="status-tag status-{{ strtolower($customer->status ?? 'standard') }}">
                                    {{ strtoupper($customer->status ?? 'Standard') }}
                                </span>
                            </div>

                            <!-- Last Contact -->
                            <div class="last-contact">
                                {{ $customer->last_contact ? \Carbon\Carbon::parse($customer->last_contact)->diffForHumans() : 'Never' }}
                            </div>

                            <!-- Actions -->
                            <div class="actions">
                                <button class="action-btn view">View</button>
                                <button class="action-btn contact">Contact</button>
                            </div>
                        </div>
                    @endforeach

                    <!-- Add an empty-state container (after the table) -->
                    <div class="no-customers" style="display:none; padding:18px; text-align:center; color:#666;">
                        No customers found for this status.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            var $tabs = $('.tabs .tab'),
                $rows = $('.customers-table .table-row'),
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

            // optional: if rows change dynamically, call updateBadges() afterwards
        });
    </script>
@endpush
