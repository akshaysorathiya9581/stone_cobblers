@extends('layouts.admin')

@section('title', 'Customers')

@push('css')
@endpush

@section('content')
@php
    // small helper to safely format numbers
    if (! function_exists('fmt_num')) {
        function fmt_num($v, $dec = 2) {
            return is_numeric($v) ? number_format((float)$v, (int)$dec) : null;
        }
    }
@endphp

    <div class="main-content">
        <x-header :export-url="null" :create-url="route('admin.customers.create')" export-label="Export Customers"
            create-label="New Customer" />

        <div class="content bg-content">
            <div class="view-details">
                <div class="content-header mb-20">
                    <h1 class="content-title">Customer Management View</h1>
                    <div class="action-buttons">
                        <a href="{{ route('admin.customers.index') }}" class="btn primary" role="button">Back</a>
                        <a href="{{ route('admin.customers.edit', ['customer' => $customer->id]) }}" class="btn secondary" role="button">Edit</a>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="tabs mb-20">
                    @foreach ($tabs as $status)
                        @php $activeClass = $status['id'] == 1 ? 'active' : ''; @endphp
                        <button class="tab {{ $activeClass }}" data-status="{{ $status['id'] }}">
                            {{ $status['text'] }}
                        </button>
                    @endforeach
                </div>

                {{-- TAB: Customer Info --}}
                <div class="tab-content" id="tab-1">
                    <div class="list-view">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Customer</th>
                                    <td>{{ $customer->name ?? ($customer->display_name ?? '—') }}</td>
                                </tr>
                                <tr>
                                    <th>Contact Info</th>
                                    <td class="contact-info">
                                        @if(!empty($customer->email))
                                            <div><i class="fas fa-envelope"></i> <a href="mailto:{{ $customer->email }}" class="email">{{ $customer->email }}</a></div>
                                        @endif
                                        @if(!empty($customer->phone))
                                            <div>📞 {{ $customer->phone }}</div>
                                        @endif
                                        {{-- fallback: show user meta phone if available --}}
                                        @if(empty($customer->phone) && $phone = $customer->getMeta('phone') ?? null)
                                            <div>📞 {{ $phone }}</div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total Value</th>
                                    @php
                                        $tf = is_numeric($totalValue) ? (float)$totalValue : 0.0;
                                    @endphp
                                    <td class="total-value">${{ number_format($tf, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Projects</th>
                                    <td class="total-value">{{ $projectsCount }} Project{{ $projectsCount !== 1 ? 's' : '' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @php
                                            $statusLabel = strtoupper($customer->status ?? 'ACTIVE');
                                        @endphp
                                        <span class="status-tag status-active">{{ $statusLabel }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Last Contact</th>
                                    <td>{{ optional($customer->last_contact_at)->diffForHumans() ?? 'Never' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB: Projects --}}
                <div class="tab-content" id="tab-2" style="display:none;">
                    <div class="tabs mb-15 nested-tabs">
                        @php
                            $subtabs = [
                                'all' => 'All Projects',
                                'planning' => 'Planning',
                                'progress' => 'In Progress',
                                'hold' => 'On Hold',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled'
                            ];
                        @endphp

                        @foreach ($subtabs as $key => $label)
                            <button class="tab {{ $key === 'all' ? 'active' : '' }}" data-subtab="{{ $key }}">{{ $label }}</button>
                        @endforeach
                    </div>

                    {{-- All Projects --}}
                    <div class="nested-content" id="subtab-all">
                        @if($customerProjects->isEmpty())
                            <div class="no-projects">No projects found for this customer.</div>
                        @else
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
                                        @foreach ($customerProjects as $project)
                                            @php
                                                $projStatus = strtolower($project->status ?? 'planning');
                                                $progress = isset($project->progress) ? intval($project->progress) : 0;
                                                
                                                // safe budget text
                                                if (isset($project->budget) && is_numeric($project->budget)) {
                                                    $budgetText = '$' . fmt_num($project->budget, 2);
                                                } elseif (isset($project->budget_min) || isset($project->budget_max)) {
                                                    $min = (isset($project->budget_min) && is_numeric($project->budget_min)) ? (float)$project->budget_min : 0.0;
                                                    $max = (isset($project->budget_max) && is_numeric($project->budget_max)) ? (float)$project->budget_max : $min;
                                                    // show integer range (no decimals) — change to 2 decimals if you prefer
                                                    $budgetText = '$' . fmt_num($min, 0) . ' - $' . fmt_num($max, 0);
                                                } else {
                                                    $budgetText = '—';
                                                }

                                                // timeline
                                                $timeline = $project->timeline ?? ($project->start_date && $project->end_date ? \Carbon\Carbon::parse($project->start_date)->format('M d, Y').' - '.\Carbon\Carbon::parse($project->end_date)->format('M d, Y') : ($project->timeline_text ?? '—'));
                                                // project url (assumes you have admin.projects.show)
                                                $projectUrl = route('admin.projects.show', ['project' => $project->id]);
                                            @endphp
                                            <tr data-status="{{ $projStatus }}">
                                                <td class="project-info">
                                                    <div class="project-avatar">{{ strtoupper(mb_substr($project->title ?? 'P', 0, 1)) }}</div>
                                                    <div class="project-details">
                                                        <h4>{{ $project->name ?? 'Untitled Project' }}</h4>
                                                        <p class="muted">{{ Str::limit($project->subtitle ?? $project->description ?? '', 80) }}</p>
                                                    </div>
                                                </td>
                                                <td class="customer-name">{{ $customer->name ?? $customer->display_name }}</td>
                                                <td class="budget">{{ $budgetText }}</td>
                                                <td>
                                                    <div class="progress-bar">
                                                        <div class="progress-fill" style="width: {{ $progress }}%"></div>
                                                    </div>
                                                    <div class="progress-text">{{ $progress }}% Complete</div>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusClass = 'status-planning';
                                                        if (in_array($projStatus, ['progress','in progress','ongoing'])) $statusClass = 'status-progress';
                                                        if (in_array($projStatus, ['hold','on hold'])) $statusClass = 'status-hold';
                                                        if (in_array($projStatus, ['completed','complete','done'])) $statusClass = 'status-completed';
                                                        if (in_array($projStatus, ['cancelled','canceled'])) $statusClass = 'status-cancelled';
                                                    @endphp
                                                    <span class="status-tag {{ $statusClass }}">{{ strtoupper($project->status ?? $projStatus) }}</span>
                                                </td>
                                                <td class="timeline on-track">{{ $timeline }}</td>
                                                <td class="actions">
                                                    <a href="{{ $projectUrl }}" class="action-btn view" title="View"><i class="fa-solid fa-eye"></i></a>
                                                    <a href="{{ route('admin.projects.edit', ['project' => $project->id]) }}" class="action-btn edit" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- Subtabs: plan/progress/hold/completed/cancelled --}}
                    @foreach (['planning','progress','hold','completed','cancelled'] as $sub)
                        <div class="nested-content" id="subtab-{{ $sub }}" style="display: none;">
                            @php $list = $projectsByStatus->get($sub) ?? collect(); @endphp
                            @if($list->isEmpty())
                                <div class="no-records">No projects found for this status.</div>
                            @else
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
                                            @foreach ($list as $project)
                                                @php
                                                    $projStatus = strtolower($project->status ?? $sub);
                                                    $progress = isset($project->progress) ? intval($project->progress) : 0;

                                                    // safe budget for subtabs
                                                    if (isset($project->budget) && is_numeric($project->budget)) {
                                                        $budgetText = '$' . fmt_num($project->budget, 2);
                                                    } elseif (isset($project->budget_min) || isset($project->budget_max)) {
                                                        $min = (isset($project->budget_min) && is_numeric($project->budget_min)) ? (float)$project->budget_min : 0.0;
                                                        $max = (isset($project->budget_max) && is_numeric($project->budget_max)) ? (float)$project->budget_max : $min;
                                                        $budgetText = '$' . fmt_num($min, 0) . ' - $' . fmt_num($max, 0);
                                                    } else {
                                                        $budgetText = '—';
                                                    }

                                                    $timeline = $project->timeline ?? ($project->start_date && $project->end_date ? \Carbon\Carbon::parse($project->start_date)->format('M d, Y').' - '.\Carbon\Carbon::parse($project->end_date)->format('M d, Y') : ($project->timeline_text ?? '—'));
                                                @endphp
                                                <tr data-status="{{ $projStatus }}">
                                                    <td class="project-info">
                                                        <div class="project-avatar">{{ strtoupper(mb_substr($project->name ?? 'P', 0, 1)) }}</div>
                                                        <div class="project-details">
                                                            <h4>{{ $project->name ?? 'Untitled Project' }}</h4>
                                                            <p class="muted">{{ Str::limit($project->subtitle ?? $project->description ?? '', 80) }}</p>
                                                        </div>
                                                    </td>
                                                    <td class="customer-name">{{ $customer->name ?? $customer->display_name }}</td>
                                                    <td class="budget">{{ $budgetText }}</td>
                                                    <td>
                                                        <div class="progress-bar">
                                                            <div class="progress-fill" style="width: {{ $progress }}%"></div>
                                                        </div>
                                                        <div class="progress-text">{{ $progress }}% Complete</div>
                                                    </td>
                                                    <td>
                                                        <span class="status-tag {{ 'status-' . $sub }}">{{ strtoupper($project->status ?? $projStatus) }}</span>
                                                    </td>
                                                    <td class="timeline on-track">{{ $timeline }}</td>
                                                    <td class="actions">
                                                        <a href="{{ route('admin.projects.show', ['project' => $project->id]) }}" class="action-btn view" title="View"><i class="fa-solid fa-eye"></i></a>
                                                        <a href="{{ route('admin.projects.edit', ['project' => $project->id]) }}" class="action-btn edit" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @endforeach

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
    