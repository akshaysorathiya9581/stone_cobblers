@extends('layouts.admin')

@section('title', 'Project Details')

@push('css')

@endpush

@section('content')
@php
    $user = auth()->user();
    // Helper for human readable file sizes
    if (! function_exists('human_filesize')) {
        function human_filesize($bytes, $decimals = 2) {
            if (!is_numeric($bytes) || $bytes <= 0) return '';
            $sizes = ['B','KB','MB','GB','TB'];
            $i = 0;
            while ($bytes >= 1024 && $i < count($sizes)-1) { $bytes /= 1024; $i++; }
            return round($bytes, $decimals) . ' ' . $sizes[$i];
        }
    }

    // normalize helpers
    $proj = $project_details;
    $cust = $proj->customer ?? null;
    $custName = $cust ? trim(($cust->first_name ?? '') . ' ' . ($cust->last_name ?? '') ) : ($proj->customer_name ?? '—');
    if ($custName === '') $custName = ($cust->name ?? ($proj->customer_name ?? '—'));
@endphp

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
                        <a href="{{ route('admin.projects.index') }}" class="btn primary" role="button">
                            Back
                        </a>
                        @if($user && $user->role === 'admin')
                            <a href="{{ route('admin.projects.edit', $proj->id) }}" class="btn secondary" role="button">Edit</a>
                        @endif
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
                        @php $activeClass = $status['id'] == 1 ? 'active' : ''; @endphp
                        <button class="tab {{ $activeClass }}" data-status="{{ $status['id'] }}">
                            {{ $status['text'] }}
                        </button>
                    @endforeach
                </div>

                <!-- Tab 1: Project Details -->
                <div class="tab-content" id="tab-1">
                    <div class="list-view">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th>Customer</th>
                                    <td class="name">{{ $custName }}</td>
                                </tr>
                                <tr>
                                    <th>Project</th>
                                    <td class="project-info">
                                        <div class="project-avatar">{{ strtoupper(mb_substr($proj->title ?? $proj->name ?? 'P', 0, 1)) }}</div>
                                        <div class="project-details">
                                            <h4>{{ $proj->title ?? $proj->name ?? 'Untitled Project' }}</h4>
                                            <p style="margin:0; color:#666;">{{ Str::limit($proj->summary ?? $proj->description ?? '', 160) }}</p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Budget</th>
                                    <td class="budget">
                                        @php
                                            $budgetText = '—';
                                            if (!empty($proj->budget) && is_numeric($proj->budget)) {
                                                $budgetText = '$' . number_format((float)$proj->budget, 2);
                                            } elseif (!empty($proj->budget_min) || !empty($proj->budget_max)) {
                                                $min = is_numeric($proj->budget_min) ? (float)$proj->budget_min : 0;
                                                $max = is_numeric($proj->budget_max) ? (float)$proj->budget_max : $min;
                                                $budgetText = '$' . number_format($min,0) . ' - $' . number_format($max,0);
                                            }
                                        @endphp
                                        {{ $budgetText }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Progress</th>
                                    <td>
                                        @php $progress = isset($proj->progress) ? intval($proj->progress) : 0; @endphp
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: {{ $progress }}%"></div>
                                        </div>
                                        <div class="progress-text" style="margin-top:6px;">{{ $progress }}% Complete</div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @php
                                            $s = strtolower($proj->status ?? 'planning');
                                            $statusClass = 'status-planning';
                                            if (in_array($s, ['in progress','progress','ongoing'])) $statusClass = 'status-progress';
                                            if (in_array($s, ['hold','on hold'])) $statusClass = 'status-hold';
                                            if (in_array($s, ['completed','complete','done'])) $statusClass = 'status-completed';
                                            if (in_array($s, ['cancelled','canceled'])) $statusClass = 'status-cancelled';
                                        @endphp
                                        <span class="status-tag {{ $statusClass }}">{{ strtoupper($proj->status ?? $s) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Timeline</th>
                                    <td class="timeline on-track">
                                        @php
                                            $timeline = '—';
                                            if (!empty($proj->timeline)) {
                                                $timeline = $proj->timeline;
                                            } elseif (!empty($proj->start_date) || !empty($proj->end_date)) {
                                                $start = $proj->start_date ? (is_string($proj->start_date) ? $proj->start_date : optional($proj->start_date)->format('M d, Y')) : '';
                                                $end = $proj->end_date ? (is_string($proj->end_date) ? $proj->end_date : optional($proj->end_date)->format('M d, Y')) : '';
                                                $timeline = trim($start . ' - ' . $end, ' - ');
                                            }
                                        @endphp
                                        {{ $timeline }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 2: Files (grouped by Customer Name, same display type) -->
                <div class="tab-content" id="tab-2" style="display:none;">
                    <div class="list-view">
                        <div class="project-files">

                            @php
                                // human-readable filesize helper (define here if not global)
                                if (! function_exists('human_filesize')) {
                                    function human_filesize($bytes, $decimals = 2) {
                                        if (!is_numeric($bytes) || $bytes <= 0) return '';
                                        $sizes = ['B','KB','MB','GB','TB'];
                                        $i = 0;
                                        while ($bytes >= 1024 && $i < count($sizes)-1) { $bytes /= 1024; $i++; }
                                        return round($bytes, $decimals) . ' ' . $sizes[$i];
                                    }
                                }

                                // ensure we have a collection
                                $filesCollection = collect($project_files ?? []);
                                // Group files by uploader/customer name (fallback to "Unknown")
                                $filesByCustomer = $filesCollection->groupBy(function($f){
                                    $user = $f->user ?? null;
                                    if ($user && (!empty($user->name) || !empty($user->first_name) || !empty($user->last_name))) {
                                        return trim(($user->name ?? '') ?: (($user->first_name ?? '') . ' ' . ($user->last_name ?? '')));
                                    }
                                    if (!empty($f->uploaded_by_name)) return $f->uploaded_by_name;
                                    if (!empty($f->user_id)) return 'User #' . $f->user_id;
                                    return 'Unknown';
                                });
                            @endphp

                            @if($filesByCustomer->isEmpty())
                                <div class="name">Project: <strong>{{ $proj->title ?? $proj->name ?? '—' }}</strong></div>
                                <div class="no-records" style="margin-top:12px;">No files uploaded for this project.</div>
                            @else
                                @foreach($filesByCustomer as $customerName => $files)
                                    <div style="margin-bottom:18px;">
                                        <div class="name" style="font-weight:600; margin-bottom:10px;">
                                            Customer Name: <span>{{ $customerName }}</span>
                                        </div>

                                        <div class="files-grid" id="filesGrid-{{ \Illuminate\Support\Str::slug($customerName ?: 'unknown') }}">
                                            @foreach($files as $file)
                                                @php
                                                    $filename = $file->filename ?? $file->name ?? ($file->path ? basename($file->path) : 'file');
                                                    $uploader = $file->user->name ?? ($file->uploaded_by_name ?? 'Unknown');
                                                    $size = $file->size ?? $file->filesize ?? null;
                                                @endphp

                                                <div class="file-card" data-file-id="{{ $file->id }}">
                                                    <div class="file-thumb">
                                                        <div class="icon">📄</div>
                                                    </div>

                                                    <div class="file-name">{{ $filename }}</div>

                                                    <div class="file-meta">{{ $uploader }} • {{ $size ? human_filesize($size) : '—' }}</div>

                                                    <div class="file-actions">
                                                        <a href="{{ route('admin.files.download', ['project' => $proj->id, 'file' => $file->id]) }}" class="action-btn download" title="Download">
                                                            <i class="fa-solid fa-download"></i>
                                                        </a>

                                                        @can('delete', $file)
                                                            <form method="POST" action="{{ route('admin.files.destroy', ['project' => $proj->id, 'file' => $file->id]) }}" style="display:inline" onsubmit="return confirm('Delete this file?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button class="action-btn delete" title="Delete" type="submit"><i class="fa-solid fa-trash"></i></button>
                                                            </form>
                                                        @endcan
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                        </div>
                    </div>
                </div>

                <!-- Tab 3: Quotes -->
                <div class="tab-content" id="tab-3" style="display:none;">
                    <div class="tabs mb-15 nested-tabs">
                        <button class="tab active" data-subtab="all">All Quotes</button>
                        <button class="tab" data-subtab="draft">Draft</button>
                        <button class="tab" data-subtab="sent">Sent</button>
                        <button class="tab" data-subtab="approved">Approved</button>
                        <button class="tab" data-subtab="rejected">Rejected</button>
                        <button class="tab" data-subtab="expired">Expired</button>
                    </div>

                    @php
                        $quotesByStatus = $project_quotes->groupBy(function($q){
                            $s = strtolower(trim($q->status ?? 'draft'));
                            if (in_array($s, ['draft','sent','approved','rejected','expired'])) return $s;
                            return 'other';
                        });
                    @endphp

                    <div class="nested-content" id="subtab-all">
                        @if($project_quotes->isEmpty())
                            <div class="no-records">No quotes for this project.</div>
                        @else
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
                                    <tbody>
                                        @foreach($project_quotes as $q)
                                            @php
                                                $qCust = $q->customer ?? null;
                                                $qCustName = $qCust->name ?? ($q->customer_name ?? $custName ?? '—');
                                                $quoteNumber = $q->quote_number ?? ('QT-' . $q->id);
                                                $amount = is_numeric($q->total) ? '$' . number_format((float)$q->total,2) : ($q->total_display ?? '—');
                                                $created = optional($q->created_at)->format('M d, Y') ?? '-';
                                                $expires = optional($q->expires_at)->format('M d, Y') ?? '-';
                                                $qStatus = ucfirst($q->status ?? 'Draft');
                                            @endphp
                                            <tr>
                                                <td class="customer-info">
                                                    <div class="customer-avatar">{{ strtoupper(mb_substr($qCustName,0,2)) }}</div>
                                                    <div class="customer-details">
                                                        <h4>{{ $qCustName }}</h4>
                                                        <p>{{ $proj->title ?? $proj->name ?? '' }}</p>
                                                    </div>
                                                </td>
                                                <td class="quote-number">{{ $quoteNumber }}</td>
                                                <td class="amount">{{ $amount }}</td>
                                                <td><span class="status-tag {{ 'status-'.strtolower($q->status ?? 'draft') }}">{{ $qStatus }}</span></td>
                                                <td class="date">{{ $created }}</td>
                                                <td class="date">{{ $expires }}</td>
                                                <td class="actions">
                                                    {{-- <a href="{{ route('admin.quotes.show', $q->id) }}" class="action-btn" title="View"><i class="fa-solid fa-eye"></i></a> --}}
                                                    <a href="{{ route('admin.quotes.download', $q->id) }}" class="action-btn download" title="Download"><i class="fa-solid fa-download"></i></a>

                                                    @if($user && $user->role === 'admin')
                                                        @if(strtolower($q->status ?? '') !== 'approved')
                                                            <form method="POST" action="{{ route('admin.quotes.approve', $q->id) }}" style="display:inline">
                                                                @csrf
                                                                <button class="action-btn approve" title="Approve" type="submit"><i class="fa-solid fa-check"></i></button>
                                                            </form>
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    @foreach (['draft','sent','approved','rejected','expired'] as $sub)
                        <div class="nested-content" id="subtab-{{ $sub }}" style="display:none;">
                            @php $list = $quotesByStatus->get($sub) ?? collect(); @endphp
                            @if($list->isEmpty())
                                <div class="no-records">No quotes found for this status.</div>
                            @else
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
                                        <tbody>
                                            @foreach($list as $q)
                                                @php
                                                    $qCust = $q->customer ?? null;
                                                    $qCustName = $qCust->name ?? ($q->customer_name ?? '—');
                                                    $quoteNumber = $q->quote_number ?? ('QT-'.$q->id);
                                                    $amount = is_numeric($q->total) ? '$'.number_format((float)$q->total,2) : ($q->total_display ?? '—');
                                                    $created = optional($q->created_at)->format('M d, Y') ?? '-';
                                                    $expires = optional($q->expires_at)->format('M d, Y') ?? '-';
                                                    $qStatus = ucfirst($q->status ?? $sub);
                                                @endphp
                                                <tr>
                                                    <td class="customer-info">
                                                        <div class="customer-avatar">{{ strtoupper(mb_substr($qCustName,0,2)) }}</div>
                                                        <div class="customer-details">
                                                            <h4>{{ $qCustName }}</h4>
                                                            <p>{{ $proj->title ?? $proj->name ?? '' }}</p>
                                                        </div>
                                                    </td>
                                                    <td class="quote-number">{{ $quoteNumber }}</td>
                                                    <td class="amount">{{ $amount }}</td>
                                                    <td><span class="status-tag {{ 'status-'.strtolower($q->status ?? $sub) }}">{{ $qStatus }}</span></td>
                                                    <td class="date">{{ $created }}</td>
                                                    <td class="date">{{ $expires }}</td>
                                                    <td class="actions">
                                                        <a href="{{ route('admin.quotes.show', $q->id) }}" class="action-btn view" title="View"><i class="fa-solid fa-eye"></i></a>
                                                        <a href="{{ route('admin.quotes.download', $q->id) }}" class="action-btn download" title="Download"><i class="fa-solid fa-download"></i></a>
                                                        @if($user && $user->role === 'admin')
                                                            @if(strtolower($q->status ?? '') !== 'approved')
                                                                <form method="POST" action="{{ route('admin.quotes.approve', $q->id) }}" style="display:inline">
                                                                    @csrf
                                                                    <button class="action-btn approve" title="Approve" type="submit"><i class="fa-solid fa-check"></i></button>
                                                                </form>
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @endforeach

                </div> {{-- end tab-3 --}}

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

        // 🔹 Nested Tabs inside "Quotes"
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
