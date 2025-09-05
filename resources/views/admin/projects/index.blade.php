@php use Illuminate\Support\Str; @endphp

@extends('layouts.admin')

@section('title', 'Projects')

@push('css')

@endpush

@section('content')
<div class="main-content">
    <!-- Header -->
    <div class="header">
        <div class="search-bar">
            <i>🔍</i>
            <input type="text" placeholder="Search projects, customers...">
        </div>

        <div class="header-actions">
            <button class="header-btn secondary">
                <i>📤</i> Export
            </button>
            <a href="{{ route('admin.projects.create') }}" class="header-btn primary" role="button">
                <i>➕</i> New Project
            </a>
            <div class="user-avatar">BM</div>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="content-header">
            <h1 class="content-title">Project Management</h1>
            <div class="action-buttons">
                <a href="#" class="btn secondary" role="button">
                    <i>📊</i> Reports
                </a>
                <a href="{{ route('admin.projects.create') }}" class="btn primary" role="button">
                    <i>➕</i> Create Project
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Active Projects</h3>
                <div class="value">{{ $activeProjects }}</div>
                <div class="change">+5% this month</div>
            </div>
            <div class="stat-card">
                <h3>Completed This Month</h3>
                <div class="value">{{ $completedProjectsThisMonth }}</div>
                <div class="change">+12% vs last month</div>
            </div>
            <div class="stat-card">
                <h3>On-Time Delivery</h3>
                <div class="value">94%</div>
                <div class="change">+2% improvement</div>
            </div>
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <div class="value">$847K</div>
                <div class="change">+18% this quarter</div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            {{-- All Projects Tab --}}
            <button class="tab active" data-status="all">All Projects</button>

            {{-- Status Tabs from Helper --}}
            @foreach(get_project_status_list() as $status)
            <button class="tab" data-status="{{ $status['id'] }}">
                {{ $status['text'] }}
            </button>
            @endforeach
        </div>

        <!-- Projects Table -->
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
                        <th>Team</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($projects as $project)
                    <tr data-status="{{ strtolower($project->status) }}">
                        {{-- Project Info + Avatar --}}
                        <td class="project-info">
                            <div class="project-avatar">
                                @php
                                // initials from project name (up to 2 letters)
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

                        {{-- Customer name --}}
                        <td class="customer-name">
                            {{ optional($project->customer)->first_name ?? '-' }}
                            {{ optional($project->customer)->last_name ?? '' }}
                        </td>

                        {{-- Budget --}}
                        <td class="budget">
                            @php
                            $budget = $project->budget;
                            if (is_numeric($budget)) {
                            $budget = '$' . number_format($budget, 0);
                            }
                            @endphp
                            {{ $budget ?? '-' }}
                        </td>

                        {{-- Progress --}}
                        <td>
                            @php
                            $rawProgress = $project->progress ?? '0%';
                            $progressVal = (int) rtrim($rawProgress, '%');
                            $progressVal = max(0, min(100, $progressVal));
                            @endphp

                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $progressVal }}%"></div>
                            </div>
                            <div class="progress-text">{{ $progressVal }}% Complete</div>
                        </td>

                        {{-- Status --}}
                        <td>
                            <span class="status-tag status-{{ Str::slug($project->status ?? 'unknown') }}">
                                {{ $project->status ?? 'Unknown' }}
                            </span>
                        </td>

                        {{-- Timeline --}}
                        <td class="timeline {{ $project->due_date && \Carbon\Carbon::parse($project->due_date)->isPast() ? 'delayed' : 'on-track' }}">
                            @if ($project->due_date)
                            Due {{ \Carbon\Carbon::parse($project->due_date)->format('M j') }}
                            @else
                            {{ $project->timeline }}
                            @endif
                        </td>

                        {{-- Team --}}
                        <td>{{ $project->team ?? 'Unassigned' }}</td>

                        {{-- Actions --}}
                        <td class="actions">
                            <a href="{{ route('admin.projects.show', $project) }}" class="action-btn view">View</a>
                            <a href="{{ route('admin.projects.edit', $project) }}" class="action-btn update">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Empty state --}}
        <div class="no-projects" style="display:none; padding:20px; text-align:center; color:#666;">
            No projects found for this status.
        </div>

    </div>
</div>
@endsection

@push('scripts')

<script>
    $(function() {
        var $tabs = $('.tabs .tab'),
            $rows = $('.projects-table table tbody tr'), // ✅ now targets <tr>

            $empty = $('.no-projects');

        function norm(s) {
            return (s || '').toString().trim().toLowerCase();
        }

        function showStatus(status) {
            status = norm(status);
            if (status === 'all') {
                $rows.show();
            } else {
                $rows.each(function() {
                    $(this).toggle(norm($(this).data('status')) === status);
                });
            }

            // empty state toggle
            $empty.toggle($rows.filter(':visible').length === 0);
        }

        // tab click
        $tabs.on('click', function() {
            var $t = $(this),
                status = $t.data('status') || norm($t.text());

            $tabs.removeClass('active');
            $t.addClass('active');
            showStatus(status);

            localStorage.setItem('projectTab', status);
        });

        // init: restore from localStorage
        var saved = localStorage.getItem('projectTab') || 'all';
        var $init = $tabs.filter('[data-status="' + saved + '"]').first();
        if ($init.length) {
            $init.addClass('active').trigger('click');
        } else {
            $tabs.first().trigger('click');
        }
    });
</script>

@endpush