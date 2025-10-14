@php use Illuminate\Support\Str; @endphp
@extends('layouts.admin')

@section('title', 'Files')

@push('css')

@endpush

@section('content')
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="search-bar">
                <i>🔍</i>
                <input type="text" placeholder="Search projects, customers, status..." id="global-search">
            </div>

            <div class="header-actions">
                <button class="header-btn secondary">
                    <i>📤</i> Export
                </button>
                <a href="{{ route('admin.files.create') }}" class="header-btn primary">➕ Upload Files</a>
                <a href="{{ route('admin.profile.edit') }}" class="user-avatar" aria-label="Open profile">BM</a>
            </div>
        </div>

        <div class="header">
            <h1 class="content-title">File Management</h1>
            <div class="header-actions">
                <a href="{{ route('admin.files.create') }}" class="btn primary">➕ Upload Files</a>
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
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
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
                downloadBase = "{{ url('admin/files') }}";

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                cache: false
            });

            const escapeHtml = s => (s || '').toString().replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m]));
            const formatSize = b => !b ? '0 KB' : (b / 1024 / 1024 >= 1 ? (b / 1024 / 1024).toFixed(1) + ' MB' : Math.round(b / 1024) + ' KB');

            function renderFiles(files) {
                const $grid = $('#filesGrid').empty();
                if (!files?.length) return $grid.html('<div class="empty">No files found</div>');

                files.forEach(f => {
                    const preview = f.image_url ? `<img src="${f.image_url}" alt="${escapeHtml(f.name)}">` : '<div class="icon">📄</div>',
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

            function refreshFilesGrid(page = 1) {
                const category = $('.tab.active').data('status');
                const params = {
                    page,
                    project_id: $('#project-select').val(),
                    user_id: $('#user-select').val(),
                    category: category !== 'all' ? category : undefined, // IMPORTANT: backend expects category
                    search: $('#global-search').val()?.trim()
                };

                $.getJSON(listUrl, params)
                    .done(res => { renderFiles(res.data || res); renderPagination(res); })
                    .fail(() => { toastr.error('Failed to load files'); });
            }

            // Tab click
            $('.tab').on('click', function () {
                $('.tab').removeClass('active');
                $(this).addClass('active');
                refreshFilesGrid();
            });

            // Trigger active tab on load
            $('.tab.active').trigger('click');

            // Pagination click
            $('#pagination').on('click', '.page-btn', function () { refreshFilesGrid($(this).data('page')); });

            // File actions
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

            // Filters
            $('#project-select, #user-select').on('change', refreshFilesGrid);
            $('#global-search').on('input', function () { clearTimeout(window._searchTimer); window._searchTimer = setTimeout(refreshFilesGrid, 300); });
        });
    </script>

@endpush