@extends('layouts.admin')

@section('title', 'Customers')

@push('css')

@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <x-header
            :export-url="null"
            :create-url="route('admin.customers.create')"
            export-label="Export Customers"
            create-label="New Customer"
        />


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

            <!-- Tabs -->
            <div class="tabs">
                {{-- <button class="tab active" data-status="all">All Projects</button> --}}
                @php
                // Example function to get customer statuses
            
                    $tabs = [
                        ['id' => 1, 'text' => 'Customer Info'],
                        ['id' => 2, 'text' => 'Projects'],
                    ];
                @endphp

                @foreach ($tabs as $status)
                    @php
                        $activeClass = $status['id'] == '1' ? 'active' : '';
                    @endphp
                    <button class="tab {{ $activeClass }}" data-status="{{ strtolower($status['id']) }}">
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
    </script>
@endpush