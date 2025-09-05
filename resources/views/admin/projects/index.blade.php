@php use Illuminate\Support\Str; @endphp

@extends('layouts.admin')

@section('title', 'Projects')

@push('css')
    <style>
        /* Projects Table */
        .projects-table {
            background: white;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            overflow: hidden;
        }

        .table-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr 1fr 120px;
            padding: 15px 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
            font-weight: 600;
            font-size: 14px;
        }

        .table-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr 1fr 120px;
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
            align-items: center;
            transition: background-color 0.2s;
        }

        .table-row:hover {
            background-color: #f8f9fa;
        }

        .project-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .project-avatar {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background-color: rgb(22, 163, 74);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        .project-details h4 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .project-details p {
            font-size: 12px;
            color: #666;
        }

        .customer-name {
            font-size: 12px;
            color: #666;
        }

        .budget {
            font-weight: 600;
            font-size: 14px;
            color: #333;
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background-color: #e0e0e0;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background-color: rgb(22, 163, 74);
            transition: width 0.3s;
        }

        .progress-text {
            font-size: 12px;
            color: #666;
            margin-top: 2px;
        }

        .status-tag {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            text-align: center;
        }

        .status-planning {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-fabrication {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-installation {
            background-color: #d4edda;
            color: #155724;
        }

        .status-completed {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-on-hold {
            background-color: #f8d7da;
            color: #721c24;
        }

        .timeline {
            font-size: 12px;
            color: #666;
        }

        .timeline.overdue {
            color: #dc3545;
            font-weight: 500;
        }

        .timeline.on-track {
            color: #28a745;
        }

        .timeline.at-risk {
            color: #ffc107;
            font-weight: 500;
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

        .action-btn.view {
            background-color: #e8f5e8;
            color: rgb(22, 163, 74);
        }

        .action-btn.edit {
            background-color: #f8f9fa;
            color: #666;
        }

        .action-btn.update {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            text-decoration: none;
            cursor: pointer;
        }

        .btn i {
            font-style: normal;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .search-bar {
                width: 250px;
            }

            .table-header,
            .table-row {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .table-header {
                display: none;
            }
        }
    </style>
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
            <div class="projects-table">
                <div class="table-header">
                    <div>Project</div>
                    <div>Customer</div>
                    <div>Budget</div>
                    <div>Progress</div>
                    <div>Status</div>
                    <div>Timeline</div>
                    <div>Team</div>
                    <div>Actions</div>
                </div>

                @foreach ($projects as $project)
                    <div class="table-row" data-status="{{ $project->status }}">
                        {{-- Project Info + Avatar (initials) --}}
                        <div class="project-info">
                            <div class="project-avatar">
                                @php
                                    // initials from project name (up to 2 letters)
                                    $parts = preg_split('/\s+/', trim($project->name ?? ''));
                                    $initials = strtoupper(
                                        substr($parts[0] ?? '', 0, 1) .
                                            (isset($parts[1]) ? substr($parts[1], 0, 1) : ''),
                                    );
                                @endphp
                                {{ $initials ?: 'PR' }}
                            </div>

                            <div class="project-details">
                                <h4>{{ $project->name }}</h4>
                                <p>{{ $project->subtitle ?? Str::limit($project->description ?? '', 60) }}</p>
                            </div>
                        </div>

                        {{-- Customer name --}}
                        <div class="customer-name">
                            {{ optional($project->customer)->first_name ?? '-' }}
                            {{ optional($project->customer)->last_name ?? '' }}
                        </div>

                        {{-- Budget display (handles numeric or range string) --}}
                        <div class="budget">
                            @php
                                $budget = $project->budget;
                                if (is_numeric($budget)) {
                                    $budget = '$' . number_format($budget, 0);
                                }
                            @endphp
                            {{ $budget ?? '-' }}
                        </div>

                        {{-- Progress bar --}}
                        <div>
                            @php
                                // project->progress may be '75%' or '75' or numeric
                                $rawProgress = $project->progress ?? '0%';
                                $progressVal = (int) rtrim($rawProgress, '%');
                                $progressVal = max(0, min(100, $progressVal));
                            @endphp

                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $progressVal }}%"></div>
                            </div>
                            <div class="progress-text">{{ $progressVal }}% Complete</div>
                        </div>

                        {{-- Status tag with slugged class --}}
                        <div>
                            <span class="status-tag status-{{ Str::slug($project->status ?? 'unknown') }}">
                                {{ $project->status ?? 'Unknown' }}
                            </span>
                        </div>

                        {{-- Due date with on-track/delayed class --}}
                        <div
                            class="timeline {{ $project->due_date && \Carbon\Carbon::parse($project->due_date)->isPast() ? 'delayed' : 'on-track' }}">
                            @if ($project->due_date)
                                Due {{ \Carbon\Carbon::parse($project->due_date)->format('M j') }}
                            @else
                                {{ $project->timeline }}
                            @endif
                        </div>

                        {{-- Team --}}
                        <div>{{ $project->team ?? 'Unassigned' }}</div>

                        {{-- Actions --}}
                        <div class="actions">
                            <a href="{{ route('admin.projects.show', $project) }}" class="action-btn view">View</a>
                            <a href="{{ route('admin.projects.edit', $project) }}" class="action-btn update">Edit</a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- 🔹 Empty state message --}}
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
                $rows = $('.projects-table .table-row');

            function norm(s) {
                return (s || '').toString().trim();
            }

            function showStatus(status) {
                if (status === 'all') {
                    $rows.show();
                } else {
                    $rows.each(function() {
                        $(this).toggle(norm($(this).data('status')) === status);
                    });
                }

                // show/hide "No projects found"
                if ($rows.filter(':visible').length === 0) {
                    $('.no-projects').show();
                } else {
                    $('.no-projects').hide();
                }
            }

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
