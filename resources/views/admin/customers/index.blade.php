@extends('layouts.admin')

@section('title', 'Customers')

@push('css')

@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <x-header :export-url="null" :create-url="route('admin.customers.create')" export-label="Export Customers"
            create-label="New Customer" />


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
                    <div class="value">{{ format_money_short($totalRevenue ?? 0, '$') }}</div>
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
                                    ${{ number_format($customer->quotes_sum_total ?? 0, 2) }}
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
                                    <button class="action-btn contact" data-id="{{ $customer->id }}" title="Contact"><i
                                            class="fa-solid fa-address-card"></i></button>
                                    <a href="{{ route('admin.customers.show', ['customer' => $customer->id]) }}"
                                        class="action-btn view" title="View"><i class="fa-solid fa-eye"></i></a>
                                    <a href="#" class="action-btn edit" title="Edit"><i
                                            class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="#" class="action-btn delete" title="Delete"><i class="fa-solid fa-trash"></i></a>
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
        $(function () {
            // CSRF setup for all AJAX
            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            // Contact button AJAX
            $(document).on('click', '.contact-btn', function () {
                const $btn = $(this),
                    userId = $btn.data('id'),
                    $row = $('#row-' + userId);

                if ($btn.data('processing')) return;

                const orig = $btn.html();
                $btn.data('processing', true).prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');

                $.post(
                    `{{ route('admin.customers.updateLastContact', ':id') }}`.replace(':id', userId),
                    {},
                    function (res) {
                        if (res.status === 'success') {
                            $row.find('.last-contact').text(res.last_contact);
                            window.toastr ? toastr.success(res.message) : alert(res.message);
                        } else {
                            window.toastr ? toastr.error(res.message || 'Something went wrong.') : alert(res.message || 'Something went wrong.');
                        }
                    },
                    'json'
                ).fail(function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Server error.';
                    window.toastr ? toastr.error(msg) : alert(msg);
                }).always(function () {
                    $btn.prop('disabled', false).html(orig).removeData('processing');
                });
            });

            // Tabs filtering
            const $tabs = $('.tabs .tab'),
                $rows = $('.customers-table table tbody tr'),
                $empty = $('.no-records');

            const norm = s => (s || '').toString().trim();

            function updateBadges() {
                const counts = { all: $rows.length };
                $rows.each((_, r) => {
                    const st = norm($(r).data('status'));
                    counts[st] = (counts[st] || 0) + 1;
                });
                $tabs.each((_, t) => {
                    const st = $(t).data('status') || 'all';
                    $(t).find('.tab-badge').text(counts[st] || 0)
                        .css({ 'margin-left': '8px', 'font-size': '0.85em', 'padding': '2px 6px', 'border-radius': '999px', 'background': '#fff' });
                });
            }

            function showStatus(status) {
                status = norm(status);
                $rows.each(function () {
                    $(this).toggle(status === 'all' || norm($(this).data('status')) === status);
                });
                $empty.toggle($rows.filter(':visible').length === 0);
            }

            $tabs.on('click', function () {
                const $t = $(this),
                    status = $t.data('status') || norm($t.text());
                $tabs.removeClass('active');
                $t.addClass('active');
                showStatus(status);
                localStorage.setItem('customerTab', status);
            });

            // Init
            updateBadges();
            const saved = localStorage.getItem('customerTab') || 'all';
            ($tabs.filter('[data-status="' + saved + '"]').first() || $tabs.first()).addClass('active').trigger('click');
        });
    </script>

@endpush