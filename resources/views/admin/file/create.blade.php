@php use Illuminate\Support\Str; @endphp
@extends('layouts.admin')

@section('title', 'Upload Files')

@push('css')
    
@endpush

@section('content')
    <div class="main-content">
        <div class="header">
            <button class="sidebar-toggle">
                <i class="fas fa-bars toggle-icon"></i>
            </button>
            <div class="search-bar">
                <i>🔍</i>
                <input type="text" placeholder="Search projects, customers, status..." id="global-search">
            </div>

            <div class="header-actions">
                <button class="header-btn secondary">
                    <i>📤</i> Export
                </button>
                <a href="{{ route('admin.files.index') }}" class="header-btn primary">📂 View All Files</a>
                <a href="{{ route('admin.profile.edit') }}" class="user-avatar" aria-label="Open profile">BM</a>
            </div>
        </div>

        <div class="header">
            <h1 class="content-title">Upload New Files</h1>
            <div class="header-actions">
                <a href="{{ route('admin.files.index') }}" class="btn primary">📂 View All Files</a>
            </div>
        </div>

        <div class="content">

            <div class="selector-section">
                <div class="selector-grid">
                    <div class="selector-group">
                        <label for="customer-select">Customer</label>
                        <select id="customer-select" name="customer_id" class="form-input custom-select" data-placeholder="Select Customer">
                            <option></option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="selector-group">
                        <label for="project-select">Project</label>
                        <select id="project-select" name="project_id" class="form-input custom-select" data-placeholder="Select Project">
                            <option></option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="upload-section" style="margin-top:12px;">
                <div class="upload-area" id="uploadArea"
                    style="border:2px dashed #e1e1e1; padding:18px; border-radius:8px; text-align:center; cursor:pointer;">
                    <div class="upload-icon">📁</div>
                    <div class="upload-text">Drop files here or click to upload</div>
                    <div class="upload-subtext">Supports PDF, CAD, images, and documents</div>
                    <button class="upload-btn" onclick="triggerFileUpload()">Choose Files</button>
                    <input type="file" id="fileInput" name="files[]" multiple style="display:none">
                </div>
            </div>
        </div>
    </div>

    <div id="toast-container" aria-hidden="true"></div>
@endsection

@push('scripts')
    <script>
        $(function () {
            const uploadUrl = "{{ route('admin.files.store') }}";

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                }
            });

            window.triggerFileUpload = function () { $('#fileInput').click(); };

            // --- Drag & drop ---
            var $uploadArea = $('#uploadArea');
            $uploadArea.on('dragover', e => { e.preventDefault(); $uploadArea.addClass('dragover'); });
            $uploadArea.on('dragleave', e => { e.preventDefault(); $uploadArea.removeClass('dragover'); });
            $uploadArea.on('drop', function (e) {
                e.preventDefault(); $uploadArea.removeClass('dragover');
                const files = e.originalEvent.dataTransfer.files;
                if (files.length) uploadFiles(files);
            });

            $('#fileInput').on('change', function (e) {
                const files = e.target.files;
                if (!files.length) return;
                uploadFiles(files);
                $(this).val(null);
            });

            function uploadFiles(files) {
                const customerId = $('#customer-select').val();
                const projectId = $('#project-select').val();
                if (!customerId && !projectId) {
                    toastr.error('Select a customer or project first.');
                    return;
                }

                var fd = new FormData();
                $.each(files, (i, f) => fd.append('files[]', f));
                if (customerId) fd.append('customer_id', customerId);
                if (projectId) fd.append('project_id', projectId);

                toastr.info('Uploading...', '', { timeOut: 0, extendedTimeOut: 0, closeButton: true });
                var $progress = $('<div/>', { class: 'ajax-upload-progress' }).append($('<i/>').css('width', '0%'));
                $('#toast-container .toast').last().append($progress);

                $.ajax({
                    url: uploadUrl,
                    method: "POST",
                    data: fd,
                    processData: false,
                    contentType: false,
                    xhr: function () {
                        var xhr = $.ajaxSettings.xhr();
                        if (xhr.upload) {
                            xhr.upload.addEventListener('progress', function (e) {
                                if (e.lengthComputable) {
                                    var pct = Math.round((e.loaded / e.total) * 100);
                                    $progress.find('i').css('width', pct + '%');
                                }
                            }, false);
                        }
                        return xhr;
                    },
                    success: function (res) {
                        toastr.clear();
                        toastr.success(res.message || 'Uploaded successfully');
                        window.location.href = "{{ route('admin.files.index') }}";
                    },
                    error: function (xhr) {
                        toastr.clear();
                        let msg = 'Upload failed';
                        try { msg = JSON.parse(xhr.responseText).message } catch (e) { }
                        toastr.error(msg);
                    }
                });
            }
        });
    </script>
@endpush