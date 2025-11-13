@php use Illuminate\Support\Str; @endphp

@extends('layouts.admin')

@section('title', 'Vanity Quotes')

@push('css')
    {{-- add any extra css here --}}
@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <x-header :export-url="null" :create-url="route('admin.vanity.quotes.create')" export-label="Export Quote"
            create-label="New Vanity Quote" />

        <!-- Content -->
        <div class="content">
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <span class="breadcrumb-item" onclick="goToDashboard()">Dashboard</span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-item">Vanity Quotes</span>
            </div>

            <div class="content-header">
                <h1 class="content-title">Vanity Quotes Management</h1>
                <div class="action-buttons">
                    <a href="#" class="btn secondary">
                        <i>📊</i> Reports
                    </a>
                    <a href="{{ route('admin.vanity.quotes.create') }}" class="btn primary">
                        <i>➕</i> Create Vanity Quote
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Vanity Quotes</h3>
                    <div class="value">{{ $quotes->count() }}</div>
                </div>
                <div class="stat-card">
                    <h3>Draft</h3>
                    <div class="value">{{ $quotes->where('status', 'Draft')->count() }}</div>
                </div>
                <div class="stat-card">
                    <h3>Approved</h3>
                    <div class="value">{{ $quotes->where('status', 'Approved')->count() }}</div>
                </div>
                <div class="stat-card">
                    <h3>Total Value</h3>
                    <div class="value">${{ number_format($quotes->sum('total'), 2) }}</div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tabs mb-15">
                @php $current = request('status'); @endphp
                <a href="{{ route('admin.vanity.quotes.index') }}" class="tab {{ $current ? '' : 'active' }}">All Vanity Quotes</a>
                <a href="{{ route('admin.vanity.quotes.index', ['status' => 'Draft']) }}"
                    class="tab {{ $current === 'Draft' ? 'active' : '' }}">Draft</a>
                <a href="{{ route('admin.vanity.quotes.index', ['status' => 'Sent']) }}"
                    class="tab {{ $current === 'Sent' ? 'active' : '' }}">Sent</a>
                <a href="{{ route('admin.vanity.quotes.index', ['status' => 'Approved']) }}"
                    class="tab {{ $current === 'Approved' ? 'active' : '' }}">Approved</a>
                <a href="{{ route('admin.vanity.quotes.index', ['status' => 'Rejected']) }}"
                    class="tab {{ $current === 'Rejected' ? 'active' : '' }}">Rejected</a>
                <a href="{{ route('admin.vanity.quotes.index', ['status' => 'Expired']) }}"
                    class="tab {{ $current === 'Expired' ? 'active' : '' }}">Expired</a>
            </div>

            <!-- Quotes Table -->
            <div class="crm-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Quote #</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Expires</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="quotes-tbody">
                        @forelse($quotes as $quote)
                            @php
                                $clientName = optional($quote->project->customer)->name ?? ($quote->project->customer->name ?? '—');
                                $projectTitle = optional($quote->project)->name ?? ($quote->project_name ?? '-');
                                $pdfRoute = route('admin.quotes.download', $quote->id);
                                
                                // Prepare email data for mailto link
                                $customer = $quote->project->customer ?? $quote->project->client ?? null;
                                $customerEmail = $customer->email ?? '';
                                $companyName = setting('company_name', config('app.name'));
                                $companyEmail = setting('company_email', auth()->user()->email ?? '');
                                
                                // Email subject
                                $emailSubject = 'Quote ' . $quote->quote_number . ' - ' . $clientName;
                                
                                // Email body - matching the email template design
                                $emailBody = "Hello " . $clientName . ",\n\n";
                                $emailBody .= "We have sent you Quote #" . $quote->quote_number . " with a total amount of $" . number_format($quote->total, 2) . ".\n\n";
                                $emailBody .= "You can download the PDF using the link below:\n";
                                $emailBody .= url($pdfRoute) . "\n\n";
                                $emailBody .= "If you have any questions, reply to this email.\n\n";
                                $emailBody .= "Thanks,\n" . $companyName;
                                
                                // Build mailto link
                                $mailtoLink = 'mailto:' . urlencode($customerEmail);
                                $mailtoLink .= '?subject=' . urlencode($emailSubject);
                                $mailtoLink .= '&body=' . urlencode($emailBody);
                            @endphp
                            <tr class="table-row" id="quote-{{ $quote->id }}" data-quote-id="{{ $quote->id }}">
                                <td class="customer-info">
                                    <div class="customer-avatar">{{ Str::upper(Str::substr($clientName, 0, 2)) }}</div>
                                    <div class="customer-details">
                                        <h4>{{ $clientName }}</h4>
                                        <p>{{ $projectTitle }}</p>
                                    </div>
                                </td>
                                <td class="quote-number">{{ $quote->quote_number }}</td>
                                <td class="amount">{{ $quote->total ? '$' . number_format($quote->total, 2) : '$0.00' }}</td>
                                <td><span class="status-tag status-{{ Str::lower($quote->status) }}">{{ $quote->status }}</span>
                                </td>
                                <td class="date">{{ optional($quote->created_at)->format('M d, Y') }}</td>
                                <td class="date">{{ optional($quote->expires_at)->format('M d, Y') }}</td>
                                <td class="actions">
                                    @if($quote->pdf_path)
                                        <button type="button" class="action-btn download" title="Download" onclick="openPdf('{{ $pdfRoute }}')"><i class="fa-solid fa-download"></i></button>
                                    @else
                                        <span class="muted">No PDF</span>
                                    @endif

                                    @if($quote->status === 'Draft' && $quote->pdf_path)
                                        <a href="{{ $mailtoLink }}" class="action-btn send" title="Send" data-quote-id="{{ $quote->id }}" onclick="handleSendQuote(event, {{ $quote->id }})"><i class="fa-solid fa-paper-plane"></i></a>
                                    @endif

                                    @if(in_array($quote->status, ['Sent', 'Draft']))
                                        <button class="action-btn approve" title="Approve"><i class="fa-regular fa-square-check"></i></button>
                                        <button class="action-btn reject" title="Reject"><i class="fa-solid fa-square-xmark"></i></button>
                                    @endif
                                    
                                    <button class="action-btn delete" title="Delete" data-quote-id="{{ $quote->id }}"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-align-center">No quotes found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Customer Quotes Modal (kept minimal) -->
    <div class="modal modal-medium" id="customerModal" style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Customer Quotes</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>

            <div class="customer-info">
                <div class="customer-avatar" id="modalAvatar">JS</div>
                <div class="customer-details">
                    <h4 id="modalCustomerName">John Smith</h4>
                    <p id="modalCustomerProject">Project</p>
                </div>
            </div>

            <div class="customer-quotes" id="modalCustomerQuotes"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // CSRF setup once
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        function ajaxAction(btn, url, successCallback) {
            var $btn = $(btn);
            if ($btn.data('processing')) return;
            $btn.data('processing', true);

            var original = $btn.html();
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');

            $.post(url)
                .done(function (res) {
                    if (res.status === 'success') {
                        if (window.toastr) toastr.success(res.message);
                        if (typeof successCallback === 'function') successCallback(res);
                    } else {
                        if (window.toastr) toastr.error(res.message || 'Error');
                    }
                })
                .fail(function (xhr) {
                    var msg = 'Server error.';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    if (window.toastr) toastr.error(msg);
                })
                .always(function () {
                    $btn.prop('disabled', false).html(original);
                    $btn.removeData('processing');
                });
        }

        // Handle send quote - open mailto and update status
        function handleSendQuote(event, quoteId) {
            // Allow mailto link to open email client
            // The mailto link will open the email client
            
            // After a short delay, update quote status to "Sent" via AJAX
            setTimeout(function() {
                var sendUrl = "{{ url('admin/quotes') }}/" + quoteId + "/send";
                
                $.ajax({
                    url: sendUrl,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            // Reload the page to show updated status
                            window.location.reload();
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to update quote status:', xhr);
                        // Still reload to show current state
                        window.location.reload();
                    }
                });
            }, 500);
        }

        // Approve quote
        $(document).on('click', '.approve-btn', function (e) {
            e.preventDefault();
            var $btn = $(this);
            var $row = $btn.closest('tr');
            var quoteId = $row.data('quote-id') || $row.attr('id')?.split('-').pop();
            var url = "{{ url('admin/quotes') }}/" + quoteId + "/approve";

            ajaxAction($btn, url, function (res) {
                $row.find('.status-tag').removeClass().addClass('status-tag status-' + res.status_label.toLowerCase()).text(res.status_label);
            });
        });

        // Reject quote
        $(document).on('click', '.reject-btn', function (e) {
            e.preventDefault();
            var $btn = $(this);
            var $row = $btn.closest('tr');
            var quoteId = $row.data('quote-id') || $row.attr('id')?.split('-').pop();
            var url = "{{ url('admin/quotes') }}/" + quoteId + "/reject";

            ajaxAction($btn, url, function (res) {
                $row.find('.status-tag').removeClass().addClass('status-tag status-' + res.status_label.toLowerCase()).text(res.status_label);
            });
        });

        // Delete quote
        $(document).on('click', '.action-btn.delete', function (e) {
            e.preventDefault();
            var $btn = $(this);
            var $row = $btn.closest('tr');
            var quoteId = $btn.data('quote-id') || $row.data('quote-id') || $row.attr('id')?.split('-').pop();
            
            if (!quoteId) {
                if (window.toastr) toastr.error('Quote ID not found');
                return;
            }

            // Get quote number for confirmation message
            var quoteNumber = $row.find('.quote-number').text().trim() || 'this quote';
            
            if (!confirm('Are you sure you want to delete ' + quoteNumber + '?\n\nThis action cannot be undone and will also delete all associated quote items.')) {
                return;
            }

            var original = $btn.html();
            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

            $.ajax({
                url: "{{ url('admin/quotes') }}/" + quoteId,
                method: 'DELETE',
                dataType: 'json',
                data: { _token: $('meta[name="csrf-token"]').attr('content') }
            })
            .done(function (res) {
                if (res.status === 'success' || res.success) {
                    if (window.toastr) toastr.success(res.message || 'Quote deleted successfully');
                    // Fade out and remove the row
                    $row.fadeOut(300, function() {
                        $(this).remove();
                        // Update stats if no quotes left
                        if ($('#quotes-tbody tr.table-row').length === 0) {
                            $('#quotes-tbody').html('<tr><td colspan="7" class="text-align-center">No quotes found.</td></tr>');
                        }
                    });
                } else {
                    if (window.toastr) toastr.error(res.message || 'Failed to delete quote');
                    $btn.prop('disabled', false).html(original);
                }
            })
            .fail(function (xhr) {
                var msg = 'Failed to delete quote';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                if (window.toastr) toastr.error(msg);
                $btn.prop('disabled', false).html(original);
            });
        });

        function openPdf(url) {
            // open the download route in a new tab; browser will display inline if content-disposition = inline
            window.open(url, '_blank', 'noopener');
        }

        function goToDashboard() {
            window.location.href = '{{ url('/admin/dashboard') }}';
        }

        function showCustomerQuotes(customerName) {
            // optional: you can implement AJAX to fetch quotes for a customer
            document.getElementById('customerModal').style.display = 'block';
            document.getElementById('modalTitle').textContent = customerName + ' - Quotes';
            document.getElementById('modalCustomerName').textContent = customerName;
            document.getElementById('modalAvatar').textContent = customerName.split(' ').map(n => n[0]).join('');
        }

        function closeModal() {
            document.getElementById('customerModal').style.display = 'none';
        }

        // Client-side search (simple filter)
        document.getElementById('quote-search').addEventListener('input', function (e) {
            var q = e.target.value.trim().toLowerCase();
            var rows = document.querySelectorAll('#quotes-tbody tr.table-row');
            rows.forEach(function (row) {
                var txt = row.textContent.toLowerCase();
                row.style.display = txt.indexOf(q) !== -1 ? '' : 'none';
            });
        });
    </script>
@endpush