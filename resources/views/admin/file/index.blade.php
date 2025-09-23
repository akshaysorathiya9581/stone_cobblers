@php use Illuminate\Support\Str; @endphp
@extends('layouts.admin')

@section('title','Files')

@push('css')
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
/* (your CSS — same as you used, kept concise) */
.files-grid { display:flex; flex-wrap:wrap; gap:12px; }
.file-card { width:220px; border:1px solid #eee; padding:10px; border-radius:6px; background:#fff; box-shadow:0 1px 2px rgba(0,0,0,0.03); }
.file-thumb img{ max-width:100%; height:auto; display:block; margin-bottom:8px; border-radius:4px; }
.file-name { font-weight:600; margin-bottom:6px; }
.file-meta { color:#666; font-size:13px; margin-bottom:8px; }
.file-actions .file-action-btn { margin-right:6px; padding:6px 8px; border-radius:6px; border:0; cursor:pointer; }
.empty { padding:20px; color:#666; text-align:center; }
.category-tab { cursor:pointer; padding:6px 10px; border-radius:6px; display:inline-block; margin-right:6px; }
.category-tab.active { background:#f1f7ff; border:1px solid #dbeeff; }
.selector-grid { display:flex; gap:12px; align-items:center; }
.selector-group select { padding:8px; border-radius:6px; border:1px solid #ddd; min-width:180px; }
.page-btn { margin:0 4px; padding:6px 10px; border:1px solid #ddd; border-radius:4px; background:#fff; cursor:pointer; }
.page-btn:hover { background:#f1f1f1; }
</style>
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
    {{-- <div class="header" style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
        <div class="search-bar">
            <i>🔍</i>
            <input type="text" placeholder="Search files..." id="global-search">
        </div>

        <div class="header-actions">
            <a href="{{ route('admin.files.create') }}" class="header-btn primary">➕ Upload Files</a>
        </div>
    </div> --}}

    <div class="content" style="margin-top:16px;">
        <div class="content-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h1 class="content-title">File Management</h1>
        </div>

        <div class="selector-section" style="margin-top:12px;">
            <div class="selector-grid">
                <div class="selector-group">
                    <label for="user-select">Customer</label>
                    <select id="user-select" name="user_id">
                        <option value="">All Customers</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="selector-group">
                    <label for="project-select">Project</label>
                    <select id="project-select" name="project_id">
                        <option value="">All Projects</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="categories" style="margin-top:12px;">
            <div class="category-tab active" data-category="all">All Files</div>
            <div class="category-tab" data-category="pdf">PDFs</div>
            <div class="category-tab" data-category="design">Design Files</div>
            <div class="category-tab" data-category="image">Images</div>
            <div class="category-tab" data-category="document">Documents</div>
        </div>

        <div class="files-grid" id="filesGrid" style="margin-top:12px;"></div>

        <div id="pagination" style="margin-top:16px; text-align:center;"></div>
    </div>
</div>

<div id="toast-container" aria-hidden="true"></div>
@endsection

@push('scripts')
<script>
$(function(){
    const listUrl   = "{{ route('admin.files.index') }}"; // JSON endpoint
    const downloadBase = "{{ url('admin/files') }}";

    // Ensure AJAX sends CSRF & wants JSON back
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        },
        cache: false
    });

    function escapeHtml(s){ return (s||'').toString().replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m])); }
    function formatSize(bytes){ if(!bytes) return '0 KB'; var mb=bytes/1024/1024; return mb>=1?mb.toFixed(1)+' MB':Math.round(bytes/1024)+' KB'; }

    // main loader — sends fileable_type/fileable_id for filters
    window.refreshFilesGrid = function(page=1){
        var params = { page: page };
        var projectId = $('#project-select').val();
        var userId = $('#user-select').val();
        var activeCat = $('.category-tab.active').data('category');
        var search = $('#global-search').val();

        // IMPORTANT: translate UI filters to API-friendly keys
        // Give project priority: if project is selected we use project, else if user selected use customer
        
        params.project_id = projectId;
        params.user_id = userId;

        // category (controller expects 'category' already)
        if (activeCat && activeCat !== 'all') params.category = activeCat;

        if (search && search.trim()) params.search = search.trim();

        // GET JSON with params
        $.getJSON(listUrl, params)
            .done(function(res){
                // controller returns paginator JSON (res.data, res.current_page...)
                var files = res.data || res;
                renderFiles(files);
                renderPagination(res);
            })
            .fail(function(xhr){
                toastr.error('Failed to load files');
                console.error('files load error:', xhr.status, xhr.responseText);
            });
    };

    function renderFiles(files){
        var $grid = $('#filesGrid').empty();
        if(!files || !files.length){ $grid.html('<div class="empty">No files found</div>'); return; }

        $.each(files, function(i,f){
            var preview = f.image_url ? '<img src="'+f.image_url+'" alt="'+escapeHtml(f.name)+'">' : '<div style="font-size:28px; margin-bottom:8px;">📄</div>';
            var uploaderName = f.uploader && f.uploader.name ? escapeHtml(f.uploader.name) : '';
            var meta = (uploaderName ? uploaderName+' • ' : '') + formatSize(f.size);

            var $card = $('<div/>',{class:'file-card'}).html(
                '<div class="file-thumb">'+preview+'</div>'+
                '<div class="file-name">'+escapeHtml(f.name)+'</div>'+
                '<div class="file-meta">'+meta+'</div>'+
                '<div class="file-actions">'+
                    '<button class="file-action-btn download" data-id="'+f.id+'">Download</button>'+
                    '<button class="file-action-btn delete" data-id="'+f.id+'" style="color:#dc3545;">Delete</button>'+
                '</div>'
            );
            $grid.append($card);
        });
    }

    function renderPagination(res){
        var $p = $('#pagination').empty();
        if(!res || !res.last_page || res.last_page <= 1) return;

        if(res.prev_page_url){
            $p.append('<button class="page-btn" data-page="'+(res.current_page-1)+'">« Prev</button>');
        }
        $p.append('<span> Page '+res.current_page+' of '+res.last_page+' </span>');
        if(res.next_page_url){
            $p.append('<button class="page-btn" data-page="'+(res.current_page+1)+'">Next »</button>');
        }
    }

    // Pagination click (delegated)
    $('#pagination').on('click','.page-btn',function(){
        refreshFilesGrid($(this).data('page'));
    });

    // Actions
    $('#filesGrid').on('click', '.file-action-btn.download', function(e){
        e.preventDefault();
        var id = $(this).data('id');
        if(!id){ toastr.error('Invalid file id'); return; }
        // Always ask server to authorize/serve file (never trust client-provided urls)
        window.location.href = downloadBase + '/' + encodeURIComponent(id) + '/download';
    });

    $('#filesGrid').on('click', '.file-action-btn.delete', function(){
        var id = $(this).data('id');
        if(!id){ toastr.error('Invalid file id'); return; }
        if(!confirm('Delete this file?')) return;
        $.ajax({
            url: downloadBase + '/' + encodeURIComponent(id),
            type: 'DELETE'
        }).done(function(res){
            toastr.success(res.message || 'Deleted');
            refreshFilesGrid();
        }).fail(function(xhr){
            var msg = 'Delete failed';
            try { msg = (JSON.parse(xhr.responseText) && JSON.parse(xhr.responseText).message) || msg; } catch(e){}
            toastr.error(msg);
            console.error('delete error:', xhr.status, xhr.responseText);
        });
    });

    // Filters -> refresh
    $('.category-tab').on('click', function(){
        $('.category-tab').removeClass('active');
        $(this).addClass('active');
        refreshFilesGrid();
    });

    // When project changes we prefer project filter — but still allow user to change customer
    $('#project-select').on('change', function(){
        // optional UX: when selecting a project, clear customer selection to avoid confusion
        // $('#user-select').val('');
        refreshFilesGrid();
    });
    $('#user-select').on('change', function(){
        // if a project is selected, we keep priority for project; user selection will be used only if no project
        refreshFilesGrid();
    });

    // search debounce
    $('#global-search').on('input', function(){
        clearTimeout(window._searchTimer);
        window._searchTimer = setTimeout(function(){ refreshFilesGrid(); }, 300);
    });

    // initial load
    refreshFilesGrid();
});
</script>
@endpush

