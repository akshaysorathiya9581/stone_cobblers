@php use Illuminate\Support\Str; @endphp

@extends('layouts.admin')

@section('title', 'Quotes')

@push('css')
    {{-- add any extra css here --}}
@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="search-bar">
                <i>🔍</i>
                <input id="quote-search" type="text" placeholder="Search quotes, customers...">
            </div>

            <div class="header-actions">
                <a href="#" class="header-btn secondary">
                    <i>📤</i> Export
                </a>
                <a href="{{ route('admin.quotes.create') }}" class="header-btn primary">
                    <i>➕</i> New Quote
                </a>
                <div class="user-avatar">{{ auth()->user() ? Str::upper(Str::substr(auth()->user()->name ?? 'U',0,2)) : 'U' }}</div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <span class="breadcrumb-item" onclick="goToDashboard()">Dashboard</span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-item">Quotes</span>
            </div>

            <div class="content-header">
                <h1 class="content-title">Quotes Management</h1>
                <div class="action-buttons">
                    <a href="#" class="btn secondary">
                        <i>📊</i> Reports
                    </a>
                    <a href="{{ route('admin.quotes.create') }}" class="btn primary">
                        <i>➕</i> Create Quote
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Quotes</h3>
                    <div class="value">{{ $quotes->count() }}</div>
                </div>
                <div class="stat-card">
                    <h3>Draft</h3>
                    <div class="value">{{ $quotes->where('status','Draft')->count() }}</div>
                </div>
                <div class="stat-card">
                    <h3>Approved</h3>
                    <div class="value">{{ $quotes->where('status','Approved')->count() }}</div>
                </div>
                <div class="stat-card">
                    <h3>Total Value</h3>
                    <div class="value">${{ number_format($quotes->sum('total'), 2) }}</div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tabs" style="margin-bottom:16px;">
                @php $current = request('status'); @endphp
                <a href="{{ route('admin.quotes.index') }}" class="tab {{ $current ? '' : 'active' }}">All Quotes</a>
                <a href="{{ route('admin.quotes.index', ['status' => 'Draft']) }}" class="tab {{ $current === 'Draft' ? 'active' : '' }}">Draft</a>
                <a href="{{ route('admin.quotes.index', ['status' => 'Sent']) }}" class="tab {{ $current === 'Sent' ? 'active' : '' }}">Sent</a>
                <a href="{{ route('admin.quotes.index', ['status' => 'Approved']) }}" class="tab {{ $current === 'Approved' ? 'active' : '' }}">Approved</a>
                <a href="{{ route('admin.quotes.index', ['status' => 'Rejected']) }}" class="tab {{ $current === 'Rejected' ? 'active' : '' }}">Rejected</a>
                <a href="{{ route('admin.quotes.index', ['status' => 'Expired']) }}" class="tab {{ $current === 'Expired' ? 'active' : '' }}">Expired</a>
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
                            @endphp
                            <tr class="table-row">
                                <td class="customer-info">
                                    <div class="customer-avatar">{{ Str::upper(Str::substr($clientName,0,2)) }}</div>
                                    <div class="customer-details">
                                        <h4>{{ $clientName }}</h4>
                                        <p>{{ $projectTitle }}</p>
                                    </div>
                                </td>
                                <td class="quote-number">{{ $quote->quote_number }}</td>
                                <td class="amount">{{ $quote->total ? '$' . number_format($quote->total,2) : '$0.00' }}</td>
                                <td><span class="status-tag status-{{ Str::lower($quote->status) }}">{{ $quote->status }}</span></td>
                                <td class="date">{{ optional($quote->created_at)->format('M d, Y') }}</td>
                                <td class="date">{{ optional($quote->expires_at)->format('M d, Y') }}</td>
                                <td class="actions">
                                    @if($quote->pdf_path)
                                        <button type="button" class="action-btn view" onclick="openPdf('{{ $pdfRoute }}')">Download</button>
                                    @else
                                        <span class="muted">No PDF</span>
                                    @endif

                                    @if($quote->status === 'Draft')
                                        {{-- <a href="#" class="action-btn edit">Edit</a> --}}
                                        <a href="#" class="action-btn send">Send</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center">No quotes found.</td>
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
        document.getElementById('quote-search').addEventListener('input', function(e){
            var q = e.target.value.trim().toLowerCase();
            var rows = document.querySelectorAll('#quotes-tbody tr.table-row');
            rows.forEach(function(row){
                var txt = row.textContent.toLowerCase();
                row.style.display = txt.indexOf(q) !== -1 ? '' : 'none';
            });
        });
    </script>
@endpush
