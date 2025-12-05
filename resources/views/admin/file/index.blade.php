@php use Illuminate\Support\Str; @endphp
@extends('layouts.admin')

@section('title', 'Files')

@push('css')

@endpush

@section('content')
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <button class="sidebar-toggle">
                <i class="fas fa-bars toggle-icon"></i>
            </button>
            <!-- <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search projects, customers, status..." id="global-search">
            </div> -->

            <div class="header-actions">
                <!-- <button class="header-btn secondary">
                    <i class="fas fa-download"></i> Export
                </button> -->
                <a href="{{ route('admin.files.create') }}" class="header-btn primary"><i class="fas fa-plus"></i> Upload Files</a>
                <a href="{{ route('admin.profile.edit') }}" class="user-avatar" aria-label="Open profile">BM</a>
            </div>
        </div>

        <div class="header header-between">
            <h1 class="content-title">File Management</h1>
            <div class="header-actions">
                <a href="{{ route('admin.files.create') }}" class="btn primary"><i class="fas fa-plus"></i> Upload Files</a>
            </div>
        </div>

        <div class="content">

            <div class="selector-section files">
                <div class="selector-grid">
                    <div class="selector-group">
                        <label for="user-select">Customer</label>
                        <select id="user-select" name="user_id" class="custom-select" data-placeholder="All Customers">
                            <option></option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="selector-group">
                        <label for="project-select">Project</label>
                        <select id="project-select" name="project_id" class="custom-select" data-placeholder="All Projects">
                            <option></option>
                            {{-- @foreach($projects as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach --}}
                        </select>
                    </div>
                </div>
            </div>

            <div class="tabs mt-12">
                <div class="tab active" data-status="all">All Files</div>
                <div class="tab" data-status="pdf">PDFs</div>
                <div class="tab" data-status="design">Design Files</div>
                <div class="tab" data-status="image">Images</div>
                <div class="tab" data-status="document">Documents</div>
            </div>

            <div class="files-grid mt-12" id="filesGrid"></div>

            <div id="pagination" class="mt-16 text-align-center"></div>
        </div>
    </div>

    <div id="toast-container" aria-hidden="true"></div>
@endsection

@push('scripts')
    <script>
        $(function () {
            const listUrl = "{{ route('admin.files.index') }}",
                downloadBase = "{{ url('admin/files') }}",
                projectsByCustomerUrl = "{{ route('admin.customers.projects', ['customer' => '%%CUSTOMER%%']) }}"; // we'll replace token.

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                cache: false
            });

            // helpers
            const escapeHtml = s => (s || '').toString().replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m]));
            const formatSize = b => !b ? '0 KB' : (b / 1024 / 1024 >= 1 ? (b / 1024 / 1024).toFixed(1) + ' MB' : Math.round(b / 1024) + ' KB');

            // renderFiles / renderPagination (unchanged from your existing code)
            function renderFiles(files) {
                const $grid = $('#filesGrid').empty();
                if (!files?.length) return $grid.html('<div class="empty">No files found</div>');

                files.forEach(f => {
                    const preview = f.image_url ? `<img src="${f.image_url}" alt="${escapeHtml(f.name)}">` : '<div class="icon"><i class="fas fa-file"></i></div>',
                        uploader = f.uploader?.name ? escapeHtml(f.uploader.name) + ' • ' : '';
                    const $card = $(`
                        <div class="file-card">
                            <div class="file-thumb">${preview}</div>
                            <div class="file-name">${escapeHtml(f.name)}</div>
                            <div class="file-meta">${uploader}${formatSize(f.size)}</div>
                            <div class="file-actions">
                                <button class="action-btn download" data-id="${f.id}" title="Download"><i class="fa-solid fa-download"></i></button>
                                <button class="action-btn delete" data-id="${f.id}" title="Delete"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                    `);
                    $grid.append($card);
                });
            }

            function renderPagination(res) {
                const $p = $('#pagination').empty();
                if (!res?.last_page || res.last_page <= 1) return;
                if (res.prev_page_url) $p.append(`<button class="page-btn" data-page="${res.current_page - 1}">« Prev</button>`);
                $p.append(`<span> Page ${res.current_page} of ${res.last_page} </span>`);
                if (res.next_page_url) $p.append(`<button class="page-btn" data-page="${res.current_page + 1}">Next »</button>`);
            }

            // safe Select2 init helper (re-inits without duplicating handlers)
            function initSelect2($el) {
                if (!$.fn.select2) return;
                // destroy then init to avoid double-init errors (Select2 v4.x)
                try {
                    if ($el.data('select2')) $el.select2('destroy');
                } catch (e) {
                    // ignore
                }
                $el.select2({
                    placeholder: $el.data('placeholder') || '',
                    allowClear: true,
                    width: 'resolve'
                });
            }

            // initialize both selects on load
            initSelect2($('#user-select'));
            initSelect2($('#project-select'));

            // Populate project select from response array [{id,name},...]
            function populateProjects(projects) {
                const $project = $('#project-select');
                // keep placeholder option (an empty <option>) for allowClear to work
                $project.empty();
                $project.append('<option></option>');

                if (projects && projects.length) {
                    projects.forEach(p => {
                        // escape values for safety
                        const id = String(p.id).replace(/"/g, '&quot;'),
                            name = escapeHtml(p.name);
                        $project.append(`<option value="${id}">${name}</option>`);
                    });
                }
                // re-init Select2 so it picks up new options
                initSelect2($project);

                // if you want the project select to auto-open after loading (UX), uncomment:
                // $project.select2('open');
            }

            // When customer changes, fetch projects
            async function fetchProjectsForCustomer(customerId) {
                const $proj = $('#project-select');

                // Reset project select & show temporary loading option
                $proj.prop('disabled', true);
                $proj.empty().append('<option></option>'); // placeholder for Select2 allowClear
                initSelect2($proj);
                // optional: show single "Loading..." option inside original select so Select2 shows it
                $proj.append('<option value="_loading" disabled>Loading...</option>');
                initSelect2($proj);

                if (!customerId) {
                    // no customer -> clear projects
                    $proj.val(null).trigger('change');
                    $proj.prop('disabled', false);
                    return;
                }

                // build URL (replace token)
                const url = projectsByCustomerUrl.replace('%%CUSTOMER%%', encodeURIComponent(customerId));

                try {
                    const res = await $.getJSON(url);
                    if (res?.ok && Array.isArray(res.data)) {
                        populateProjects(res.data);
                    } else if (Array.isArray(res)) {
                        // accept both shapes {ok,data} or plain array
                        populateProjects(res);
                    } else {
                        populateProjects([]);
                    }
                } catch (err) {
                    console.error('Failed to load projects', err);
                    populateProjects([]);
                    toastr.error('Failed to load projects');
                } finally {
                    $proj.prop('disabled', false);
                }
            }

            // Refresh files grid (unchanged)
            function refreshFilesGrid(page = 1) {
                const category = $('.tab.active').data('status');
                const params = {
                    page,
                    project_id: $('#project-select').val() || undefined,
                    user_id: $('#user-select').val() || undefined,
                    category: category !== 'all' ? category : undefined,
                    search: $('#global-search').val()?.trim() || undefined
                };

                $.getJSON(listUrl, params)
                    .done(res => {
                        const files = res.data ?? res;
                        renderFiles(files || []);
                        renderPagination(res);
                    })
                    .fail(() => { toastr.error('Failed to load files'); });
            }

            // Tab click handler (unchanged)
            $('.tab').on('click', function () {
                $('.tab').removeClass('active');
                $(this).addClass('active');
                refreshFilesGrid(1);
            });

            // initial render
            $('.tab.active').trigger('click');

            // pagination & file actions (unchanged)
            $('#pagination').on('click', '.page-btn', function () { refreshFilesGrid($(this).data('page')); });

            $('#filesGrid').on('click', '.action-btn.download', function () {
                const id = $(this).data('id');
                if (!id) return toastr.error('Invalid file id');
                window.location.href = `${downloadBase}/${encodeURIComponent(id)}/download`;
            });

            $('#filesGrid').on('click', '.action-btn.delete', function () {
                const id = $(this).data('id');
                if (!id || !confirm('Delete this file?')) return;
                $.ajax({ url: `${downloadBase}/${encodeURIComponent(id)}`, type: 'DELETE' })
                    .done(res => { toastr.success(res.message || 'Deleted'); refreshFilesGrid(); })
                    .fail(xhr => { let msg = 'Delete failed'; try { msg = JSON.parse(xhr.responseText)?.message || msg; } catch (e) { } toastr.error(msg); });
            });

            // Filter change handlers
            const onFilterChange = function () { refreshFilesGrid(1); };
            $('#user-select').on('change', function () {
                const customerId = $(this).val() || null;
                // fetch projects for this customer then refresh files (refresh after project loaded)
                fetchProjectsForCustomer(customerId).then(() => {
                    // reset project filter when customer changes
                    $('#project-select').val(null).trigger('change');
                    refreshFilesGrid(1);
                });
            });

            // project change triggers grid refresh
            $('#project-select').on('change', onFilterChange);

            // also bind select2 events if enabled (keeps consistency)
            if ($.fn.select2) {
                $('#user-select').on('select2:select select2:unselect', function () {
                    $(this).trigger('change');
                });
                $('#project-select').on('select2:select select2:unselect', function () {
                    $(this).trigger('change');
                });
            }

            // search input debounce
            $('#global-search').on('input', function () { clearTimeout(window._searchTimer); window._searchTimer = setTimeout(() => refreshFilesGrid(1), 300); });

            // Optional: if you want to load projects for a pre-selected customer on page load
            const initialCustomer = $('#user-select').val();
            if (initialCustomer) {
                fetchProjectsForCustomer(initialCustomer);
            }
        });
    </script>
@endpush