@php use Illuminate\Support\Str; @endphp
@extends('layouts.admin')

@section('title', 'Upload Files')

@push('css')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .files-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .file-card {
            width: 220px;
            border: 1px solid #eee;
            padding: 10px;
            border-radius: 6px;
            background: #fff;
        }

        .file-thumb img {
            max-width: 100%;
            height: auto;
            display: block;
            margin-bottom: 8px;
        }

        .empty {
            padding: 20px;
            color: #666;
        }

        .ajax-upload-progress {
            height: 6px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 8px;
        }

        .ajax-upload-progress>i {
            display: block;
            height: 100%;
            width: 0%;
            background: #17a2b8;
            transition: width .2s ease;
        }

        .upload-area.dragover {
            border-color: #17a2b8;
            background: #f8feff;
        }
    </style>
@endpush

@section('content')
    <div class="main-content">
        <div class="header">
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
                <a href="{{ route('admin.files.index') }}" class="header-btn secondary">📂 View All Files</a>
            </div>
        </div>

        <div class="content">

            <div class="selector-section">
                <div class="selector-grid">
                    <div class="selector-group">
                        <label for="customer-select">Customer</label>
                        <select id="customer-select" name="customer_id">
                            <option value="">Select Customer</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="selector-group">
                        <label for="project-select">Project</label>
                        <select id="project-select" name="project_id">
                            <option value="">Select Project</option>
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
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(function () {
            const uploadUrl = "{{ route('admin.files.store') }}";

            // ✅ Add CSRF to all AJAX requests
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