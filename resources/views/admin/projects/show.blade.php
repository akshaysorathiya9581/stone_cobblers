@extends('layouts.admin')

@section('title', 'Project Details')

@push('css')

@endpush

@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <x-header :export-url="null" :create-url="route('admin.projects.create')" export-label="Export Project"
            create-label="New Project" />

        <!-- Content -->
        <div class="content bg-content">
            <div class="view-details">
                <div class="content-header mb-20">
                    <h1 class="content-title">Project Management View</h1>
                    <div class="action-buttons">
                        <a href="/admin/customers" class="btn primary" role="button">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M4 10L3.64645 10.3536L3.29289 10L3.64645 9.64645L4 10ZM20.5 18C20.5 18.2761 20.2761 18.5 20 18.5C19.7239 18.5 19.5 18.2761 19.5 18L20.5 18ZM8.64645 15.3536L3.64645 10.3536L4.35355 9.64645L9.35355 14.6464L8.64645 15.3536ZM3.64645 9.64645L8.64645 4.64645L9.35355 5.35355L4.35355 10.3536L3.64645 9.64645ZM4 9.5L14 9.5L14 10.5L4 10.5L4 9.5ZM20.5 16L20.5 18L19.5 18L19.5 16L20.5 16ZM14 9.5C17.5898 9.5 20.5 12.4101 20.5 16L19.5 16C19.5 12.9624 17.0376 10.5 14 10.5L14 9.5Z"
                                    fill="currentColor" />
                            </svg>
                            Back
                        </a>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="tabs mb-20">
                    @php
                        $tabs = [
                            ['id' => 1, 'text' => 'Project Details'],
                            ['id' => 2, 'text' => 'Files'],
                            ['id' => 3, 'text' => 'Quotes'],
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

                <div class="tab-content" id="tab-1">
                    <div class="list-view">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Customer</th>
                                    <td class="name">Ajay Rabadiya</td>
                                </tr>
                                <tr>
                                    <th>Project</th>
                                    <td class="project-info">
                                        <div class="project-avatar">P</div>
                                        <div class="project-details">
                                            <h4>Project-1</h4>
                                            <p></p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Budget</th>
                                    <td class="budget">$5,000 - $10,000</td>
                                </tr>
                                <tr>
                                    <th>Progress</th>
                                    <td>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: 0%"></div>
                                        </div>
                                        <div class="progress-text">0% Complete</div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="status-tag status-planning">
                                            Planning
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Timeline</th>
                                    <td class="timeline on-track">1-2 weeks</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-content" id="tab-2" style="display:none;">
                    <div class="list-view">
                        <div class="project-files">
                            <div class="name">
                                Customer Name: <span>Ajay Rabadiya</span>
                            </div>
                            <div class="files-grid" id="filesGrid">
                                <div class="file-card">
                                    <div class="file-thumb">
                                        <div class="icon">📄</div>
                                    </div>
                                    <div class="file-name">QT-2025-014.pdf</div>
                                    <div class="file-meta">Admin User • 1.2 MB</div>
                                    <div class="file-actions">
                                        <button class="action-btn download" title="Download"><i
                                                class="fa-solid fa-download"></i></button>
                                        <button class="action-btn delete" title="Delete"><i
                                                class="fa-solid fa-trash"></i></button>
                                    </div>
                                </div>
                                <div class="file-card">
                                    <div class="file-thumb">
                                        <div class="icon">📄</div>
                                    </div>
                                    <div class="file-name">QT-2025-001.pdf</div>
                                    <div class="file-meta">Admin User • 1.2 MB</div>
                                    <div class="file-actions">
                                        <button class="action-btn download" title="Download"><i
                                                class="fa-solid fa-download"></i></button>
                                        <button class="action-btn delete" title="Delete"><i
                                                class="fa-solid fa-trash"></i></button>
                                    </div>
                                </div>
                                <div class="file-card">
                                    <div class="file-thumb">
                                        <div class="icon">📄</div>
                                    </div>
                                    <div class="file-name">QT-2025-001.pdf</div>
                                    <div class="file-meta">Admin User • 1.2 MB</div>
                                    <div class="file-actions">
                                        <button class="action-btn download" title="Download"><i
                                                class="fa-solid fa-download"></i></button>
                                        <button class="action-btn delete" title="Delete"><i
                                                class="fa-solid fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-content" id="tab-3" style="display:none;">
                    <div class="tabs mb-15 nested-tabs">
                        <button class="tab active" data-subtab="all">All Quotes</button>
                        <button class="tab" data-subtab="draft">Draft</button>
                        <button class="tab" data-subtab="sent">Sent</button>
                        <button class="tab" data-subtab="approved">Approved</button>
                        <button class="tab" data-subtab="rejected">Rejected</button>
                        <button class="tab" data-subtab="expired">Expired</button>
                    </div>

                    <div class="nested-content" id="subtab-all">
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
                                    <tr class="table-row">
                                        <td class="customer-info">
                                            <div class="customer-avatar">AK</div>
                                            <div class="customer-details">
                                                <h4>akshay sorathiya</h4>
                                                <p>Project-1</p>
                                            </div>
                                        </td>
                                        <td class="quote-number">QT-2025-004</td>
                                        <td class="amount">$36,305.03</td>
                                        <td>
                                            <span class="status-tag status-draft">Draft</span>
                                        </td>
                                        <td class="date">Oct 09, 2025</td>
                                        <td class="date">Nov 08, 2025</td>
                                        <td class="actions">
                                            <button type="button" class="action-btn download" title="Download"><i class="fa-solid fa-download"></i></button>
                                        </td>
                                    </tr>
                                    <tr class="table-row">
                                        <td class="customer-info">
                                            <div class="customer-avatar">AK</div>
                                            <div class="customer-details">
                                                <h4>akshay sorathiya</h4>
                                                <p>Project-1</p>
                                            </div>
                                        </td>
                                        <td class="quote-number">QT-2025-004</td>
                                        <td class="amount">$36,305.03</td>
                                        <td>
                                            <span class="status-tag status-draft">Draft</span>
                                        </td>
                                        <td class="date">Oct 09, 2025</td>
                                        <td class="date">Nov 08, 2025</td>
                                        <td class="actions">
                                            <button type="button" class="action-btn download" title="Download"><i class="fa-solid fa-download"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="nested-content" id="subtab-draft" style="display:none;">
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
                                    <tr class="table-row">
                                        <td class="customer-info">
                                            <div class="customer-avatar">AK</div>
                                            <div class="customer-details">
                                                <h4>akshay sorathiya</h4>
                                                <p>Project-1</p>
                                            </div>
                                        </td>
                                        <td class="quote-number">QT-2025-004</td>
                                        <td class="amount">$36,305.03</td>
                                        <td>
                                            <span class="status-tag status-draft">Draft</span>
                                        </td>
                                        <td class="date">Oct 09, 2025</td>
                                        <td class="date">Nov 08, 2025</td>
                                        <td class="actions">
                                            <button type="button" class="action-btn download" title="Download"><i class="fa-solid fa-download"></i></button>
                                        </td>
                                    </tr>
                                    <tr class="table-row">
                                        <td class="customer-info">
                                            <div class="customer-avatar">AK</div>
                                            <div class="customer-details">
                                                <h4>akshay sorathiya</h4>
                                                <p>Project-1</p>
                                            </div>
                                        </td>
                                        <td class="quote-number">QT-2025-004</td>
                                        <td class="amount">$36,305.03</td>
                                        <td>
                                            <span class="status-tag status-draft">Draft</span>
                                        </td>
                                        <td class="date">Oct 09, 2025</td>
                                        <td class="date">Nov 08, 2025</td>
                                        <td class="actions">
                                            <button type="button" class="action-btn download" title="Download"><i class="fa-solid fa-download"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="nested-content" id="subtab-sent" style="display:none;">
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
                                    <tr>
                                        <td colspan="7" class="text-align-center no-records">No quotes found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="nested-content" id="subtab-approved" style="display:none;">
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
                                    <tr>
                                        <td colspan="7" class="text-align-center no-records">No quotes found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="nested-content" id="subtab-rejected" style="display:none;">
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
                                    <tr>
                                        <td colspan="7" class="text-align-center no-records">No quotes found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="nested-content" id="subtab-expired" style="display:none;">
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
                                    <tr>
                                        <td colspan="7" class="text-align-center no-records">No quotes found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')

    <script>
        $(document).ready(function () {
            // 🔹 Main Tabs
            $('.tabs .tab').click(function () {
                if ($(this).closest('.nested-tabs').length) return;

                // Only affect main-level tabs
                $('.tabs:not(.nested-tabs) .tab').removeClass('active');
                $(this).addClass('active');

                $('.tab-content').hide();

                const status = $(this).data('status');
                $('#tab-' + status).show();
            });

            // 🔹 Nested Tabs inside "Projects"
            $(document).on('click', '.nested-tabs .tab', function () {
                $(this).siblings().removeClass('active');
                $(this).addClass('active');

                const parent = $(this).closest('.tab-content');
                parent.find('.nested-content').hide();

                const subtab = $(this).data('subtab');
                parent.find('#subtab-' + subtab).show();
            });

            // ✅ Default active nested tab handling on page load
            $('.nested-tabs').each(function () {
                const firstTab = $(this).find('.tab.active');
                const parent = $(this).closest('.tab-content');

                if (firstTab.length) {
                    const subtab = firstTab.data('subtab');
                    parent.find('.nested-content').hide();
                    parent.find('#subtab-' + subtab).show();
                }
            });
        });
    </script>

@endpush