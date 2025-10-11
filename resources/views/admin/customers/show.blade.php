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
        <div class="content bg-content">
            <div class="view-details">
                <div class="content-header mb-20">
                    <h1 class="content-title">Customer Management View</h1>
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

                <div class="tab-content" id="tab-1">
                    <div class="list-view">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Customer</th>
                                    <td>Ajay Rabadiya</td>
                                </tr>
                                <tr>
                                    <th>Contact Info</th>
                                    <td class="contact-info">
                                        <div>
                                            📧 <a href="mailto:ajayrabadiya@gmail.com"
                                                class="email">ajayrabadiya@gmail.com</a>
                                        </div>
                                        <div>
                                            📞 09874561230
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total Value</th>
                                    <td class="total-value">$0.00</td>
                                </tr>
                                <tr>
                                    <th>Projects</th>
                                    <td class="total-value">0 Projects</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="status-tag status-active">
                                            ACTIVE
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Last Contact</th>
                                    <td>Never</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-content" id="tab-2" style="display:none;">
                    <div class="tabs mb-15 nested-tabs">
                        <button class="tab active" data-subtab="all">All Projects</button>
                        <button class="tab" data-subtab="planning">Planning</button>
                        <button class="tab" data-subtab="progress">In Progress</button>
                        <button class="tab" data-subtab="hold">On Hold</button>
                        <button class="tab" data-subtab="completed">Completed</button>
                        <button class="tab" data-subtab="cancelled">Cancelled</button>
                    </div>

                    <div class="nested-content" id="subtab-all">
                        <div class="projects-table custom-table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Project</th>
                                        <th>Customer</th>
                                        <th>Budget</th>
                                        <th>Progress</th>
                                        <th>Status</th>
                                        <th>Timeline</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr data-status="planning">
                                        <td class="project-info">
                                            <div class="project-avatar">P</div>
                                            <div class="project-details">
                                                <h4>Project-1</h4>
                                                <p></p>
                                            </div>
                                        </td>
                                        <td class="customer-name">akshay sorathiya</td>
                                        <td class="budget">$5,000 - $10,000</td>
                                        <td>
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: 0%"></div>
                                            </div>
                                            <div class="progress-text">0% Complete</div>
                                        </td>
                                        <td>
                                            <span class="status-tag status-planning">Planning</span>
                                        </td>
                                        <td class="timeline on-track">1-2 weeks</td>
                                        <td class="actions">
                                            <a href="#" class="action-btn view" title="View"><i class="fa-solid fa-eye"></i></a>
                                            <a href="#" class="action-btn edit" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="nested-content" id="subtab-planning" style="display:none;">
                        <div class="projects-table custom-table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Project</th>
                                        <th>Customer</th>
                                        <th>Budget</th>
                                        <th>Progress</th>
                                        <th>Status</th>
                                        <th>Timeline</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr data-status="planning">
                                        <td class="project-info">
                                            <div class="project-avatar">P</div>
                                            <div class="project-details">
                                                <h4>Project-1</h4>
                                                <p></p>
                                            </div>
                                        </td>
                                        <td class="customer-name">akshay sorathiya</td>
                                        <td class="budget">$5,000 - $10,000</td>
                                        <td>
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: 0%"></div>
                                            </div>
                                            <div class="progress-text">0% Complete</div>
                                        </td>
                                        <td>
                                            <span class="status-tag status-planning">Planning</span>
                                        </td>
                                        <td class="timeline on-track">1-2 weeks</td>
                                        <td class="actions">
                                            <a href="#" class="action-btn view" title="View"><i class="fa-solid fa-eye"></i></a>
                                            <a href="#" class="action-btn edit" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="nested-content" id="subtab-progress" style="display:none;">
                        <div class="projects-table custom-table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Project</th>
                                        <th>Customer</th>
                                        <th>Budget</th>
                                        <th>Progress</th>
                                        <th>Status</th>
                                        <th>Timeline</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            <div class="no-records">No projects found for this status.</div>
                        </div>
                    </div>
                    <div class="nested-content" id="subtab-hold" style="display:none;">
                        <div class="projects-table custom-table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Project</th>
                                        <th>Customer</th>
                                        <th>Budget</th>
                                        <th>Progress</th>
                                        <th>Status</th>
                                        <th>Timeline</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            <div class="no-records">No projects found for this status.</div>
                        </div>
                    </div>
                    <div class="nested-content" id="subtab-completed" style="display:none;">
                        <div class="projects-table custom-table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Project</th>
                                        <th>Customer</th>
                                        <th>Budget</th>
                                        <th>Progress</th>
                                        <th>Status</th>
                                        <th>Timeline</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            <div class="no-records">No projects found for this status.</div>
                        </div>
                    </div>
                    <div class="nested-content" id="subtab-cancelled" style="display:none;">
                        <div class="projects-table custom-table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Project</th>
                                        <th>Customer</th>
                                        <th>Budget</th>
                                        <th>Progress</th>
                                        <th>Status</th>
                                        <th>Timeline</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            <div class="no-records">No projects found for this status.</div>
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