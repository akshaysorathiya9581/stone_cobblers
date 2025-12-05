@php use Illuminate\Support\Str; @endphp

@extends('layouts.admin')

@section('title', 'Combined Quotes')

@push('css')
    {{-- add any extra css here --}}
@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <x-header :export-url="null" :create-url="null" export-label="" create-label="" />

        <!-- Content -->
        <div class="content">
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <span class="breadcrumb-item" onclick="goToDashboard()">Dashboard</span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-item">Quote</span>
            </div>

            <div class="content-header">
                <h1 class="content-title">Combined Quotes</h1>
                <p class="content-subtitle">Projects with both Kitchen and Vanity quotes</p>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Projects</h3>
                    <div class="value">{{ $projectsWithQuotes->count() }}</div>
                </div>
                <div class="stat-card">
                    <h3>Kitchen Quotes</h3>
                    <div class="value">{{ $projectsWithQuotes->where('kitchen_quote', '!=', null)->count() }}</div>
                </div>
                <div class="stat-card">
                    <h3>Vanity Quotes</h3>
                    <div class="value">{{ $projectsWithQuotes->where('vanity_quote', '!=', null)->count() }}</div>
                </div>
                <div class="stat-card">
                    <h3>Combined Value</h3>
                    <div class="value">
                        ${{ number_format($projectsWithQuotes->sum(function($item) {
                            return ($item['kitchen_quote']->total ?? 0) + ($item['vanity_quote']->total ?? 0);
                        }), 2) }}
                    </div>
                </div>
            </div>

            <!-- Projects Table -->
            <div class="crm-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Customer</th>
                            <th>Kitchen Quote #</th>
                            <th>Vanity Quote #</th>
                            <th>Kitchen Total</th>
                            <th>Vanity Total</th>
                            <th>Combined Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="projects-tbody">
                        @forelse($projectsWithQuotes as $item)
                            @php
                                $project = $item['project'];
                                $kitchenQuote = $item['kitchen_quote'];
                                $vanityQuote = $item['vanity_quote'];
                                $customerName = optional($project->customer)->name ?? 'N/A';
                                $combinedTotal = ($kitchenQuote->total ?? 0) + ($vanityQuote->total ?? 0);
                            @endphp
                            <tr class="table-row" data-project-id="{{ $project->id }}">
                                <td class="project-info">
                                    <div class="project-avatar">
                                        @php
                                            $parts = preg_split('/\s+/', trim($project->name ?? ''));
                                            $initials = strtoupper(
                                                substr($parts[0] ?? '', 0, 1) .
                                                (isset($parts[1]) ? substr($parts[1], 0, 1) : '')
                                            );
                                        @endphp
                                        {{ $initials ?: 'PR' }}
                                    </div>
                                    <div class="project-details">
                                        <h4>{{ $project->name }}</h4>
                                        <p>{{ $project->subtitle ?? Str::limit($project->description ?? '', 60) }}</p>
                                    </div>
                                </td>
                                <td class="customer-name">
                                    {{ $customerName }}
                                </td>
                                <td class="quote-number">
                                    {{ $kitchenQuote->quote_number ?? '—' }}
                                </td>
                                <td class="quote-number">
                                    {{ $vanityQuote->quote_number ?? '—' }}
                                </td>
                                <td class="amount">
                                    ${{ number_format($kitchenQuote->total ?? 0, 2) }}
                                </td>
                                <td class="amount">
                                    ${{ number_format($vanityQuote->total ?? 0, 2) }}
                                </td>
                                <td class="amount combined-total">
                                    <strong>${{ number_format($combinedTotal, 2) }}</strong>
                                </td>
                                <td class="actions">
                                    <button type="button" 
                                            class="btn theme generate-pdf-btn" 
                                            data-project-id="{{ $project->id }}"
                                            title="Generate Combined PDF">
                                        <i class="fa-solid fa-file-pdf"></i> Generate PDF
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-align-center">
                                    <div style="padding: 40px; text-align: center;">
                                        <i class="fa-solid fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 16px;"></i>
                                        <p style="color: #999; font-size: 16px;">No projects found with both kitchen and vanity quotes.</p>
                                        <p style="color: #999; font-size: 14px; margin-top: 8px;">Create kitchen and vanity quotes for the same project to see them here.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // CSRF setup
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        function goToDashboard() {
            window.location.href = '{{ url('/admin/dashboard') }}';
        }

        // Generate Combined PDF using AJAX
        $(document).on('click', '.generate-pdf-btn', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var projectId = $btn.data('project-id');
            
            if (!projectId) {
                if (window.toastr) toastr.error('Project ID not found');
                return;
            }

            // Disable button and show loading state
            var original = $btn.html();
            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Generating...');

            // AJAX request to generate and download PDF
            var url = '{{ url("admin/quotes-combined") }}/' + projectId + '/generate-pdf';
            
            $.ajax({
                url: url,
                method: 'GET',
                xhrFields: {
                    responseType: 'blob' // Important: tell jQuery to expect binary data
                },
                success: function(data, status, xhr) {
                    // Get filename from Content-Disposition header or use default
                    var filename = 'combined-quote.pdf';
                    var disposition = xhr.getResponseHeader('Content-Disposition');
                    if (disposition && disposition.indexOf('attachment') !== -1) {
                        var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                        var matches = filenameRegex.exec(disposition);
                        if (matches != null && matches[1]) {
                            filename = matches[1].replace(/['"]/g, '');
                            // Handle URL encoding
                            filename = decodeURIComponent(filename);
                        }
                    }

                    // Create blob URL
                    var blob = new Blob([data], { type: 'application/pdf' });
                    var url = window.URL.createObjectURL(blob);
                    
                    // Create temporary link and trigger download
                    var link = document.createElement('a');
                    link.href = url;
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    
                    // Clean up
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(url);
                    
                    // Re-enable button
                    $btn.prop('disabled', false).html(original);
                    
                    // Show success message
                    if (window.toastr) {
                        toastr.success('PDF generated and downloaded successfully!');
                    }
                },
                error: function(xhr, status, error) {
                    // Re-enable button
                    $btn.prop('disabled', false).html(original);
                    
                    // Show error message
                    var errorMsg = 'Failed to generate PDF';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.status === 403) {
                        errorMsg = 'You are not authorized to generate this PDF';
                    } else if (xhr.status === 404) {
                        errorMsg = 'Project or quotes not found';
                    } else if (xhr.status === 400) {
                        errorMsg = 'Project must have both kitchen and vanity quotes';
                    }
                    
                    if (window.toastr) {
                        toastr.error(errorMsg);
                    } else {
                        alert(errorMsg);
                    }
                    
                    console.error('PDF generation error:', error, xhr);
                }
            });
        });
    </script>
@endpush

