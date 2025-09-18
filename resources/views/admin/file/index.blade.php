@php use Illuminate\Support\Str; @endphp

@extends('layouts.admin')

@section('title', 'Quotes')

@push('css')

@endpush

@section('content')
<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="header">
        <div class="search-bar">
            <i>🔍</i>
            <input type="text" placeholder="Search files, customers, projects...">
        </div>

        <div class="header-actions">
            <button class="header-btn secondary">
                <i>📤</i> Export
            </button>
            <button class="header-btn primary">
                <i>➕</i> Upload Files
            </button>
            <a href="{{ route('admin.profile.edit') }}" class="user-avatar" aria-label="Open profile">BM</a>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="content-header">
            <h1 class="content-title">File Management</h1>
            <div class="action-buttons">
                <button class="btn secondary">
                    <i>📊</i> Storage
                </button>
                <button class="btn primary">
                    <i>➕</i> New Folder
                </button>
            </div>
        </div>

        <!-- Customer/Project Selector -->
        <div class="selector-section">
            <div class="selector-grid">
                <div class="selector-group">
                    <label for="customer-select">Customer</label>
                    <select id="customer-select" name="customer_id" onchange="onCustomerChange()">
                        <option value="">All Customers</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}"
                                {{ (isset($customerId) && $customerId == $c->id) ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="selector-group">
                    <label for="project-select">Project</label>
                    <select id="project-select" name="project_id" onchange="filterFiles()">
                        <option value="">All Projects</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}"
                                {{ (isset($projectId) && $projectId == $p->id) ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- File Upload Area -->
        <div class="upload-section">
            <div class="upload-area" id="uploadArea">
                <div class="upload-icon">📁</div>
                <div class="upload-text">Drop files here or click to upload</div>
                <div class="upload-subtext">Supports PDF, CAD, images, and documents</div>
                <button class="upload-btn" onclick="triggerFileUpload()">Choose Files</button>
                <input type="file" id="fileInput" multiple style="display: none;" onchange="handleFileUpload(event)">
            </div>
        </div>

        <!-- File Categories -->
        <div class="categories">
            <div class="category-tab active" onclick="filterByCategory('all')">All Files</div>
            <div class="category-tab" onclick="filterByCategory('pdf')">PDFs</div>
            <div class="category-tab" onclick="filterByCategory('design')">Design Files</div>
            <div class="category-tab" onclick="filterByCategory('image')">Images</div>
            <div class="category-tab" onclick="filterByCategory('document')">Documents</div>
        </div>

        <!-- Files Grid -->
        <div class="files-grid" id="filesGrid">
            <div class="file-card" onclick="showFileDetails('design-specs.pdf')">
                <div class="file-icon pdf">📄</div>
                <div class="file-name">Design Specifications.pdf</div>
                <div class="file-meta">John Smith • Kitchen Renovation • 2.3 MB</div>
                <div class="file-actions">
                    <button class="file-action-btn download">Download</button>
                    <button class="file-action-btn share">Share</button>
                </div>
            </div>

            <div class="file-card" onclick="showFileDetails('kitchen-layout.dwg')">
                <div class="file-icon design">📐</div>
                <div class="file-name">Kitchen Layout.dwg</div>
                <div class="file-meta">John Smith • Kitchen Renovation • 1.8 MB</div>
                <div class="file-actions">
                    <button class="file-action-btn download">Download</button>
                    <button class="file-action-btn share">Share</button>
                </div>
            </div>

            <div class="file-card" onclick="showFileDetails('stone-samples.jpg')">
                <div class="file-icon image">🖼️</div>
                <div class="file-name">Stone Samples.jpg</div>
                <div class="file-meta">Maria Santos • Bathroom Vanity • 4.1 MB</div>
                <div class="file-actions">
                    <button class="file-action-btn download">Download</button>
                    <button class="file-action-btn share">Share</button>
                </div>
            </div>

            <div class="file-card" onclick="showFileDetails('quote-details.docx')">
                <div class="file-icon document">📝</div>
                <div class="file-name">Quote Details.docx</div>
                <div class="file-meta">Robert Wilson • Outdoor Patio • 0.8 MB</div>
                <div class="file-actions">
                    <button class="file-action-btn download">Download</button>
                    <button class="file-action-btn share">Share</button>
                </div>
            </div>

            <div class="file-card" onclick="showFileDetails('measurements.pdf')">
                <div class="file-icon pdf">📄</div>
                <div class="file-name">Measurements.pdf</div>
                <div class="file-meta">Lisa Davis • Kitchen Island • 1.2 MB</div>
                <div class="file-actions">
                    <button class="file-action-btn download">Download</button>
                    <button class="file-action-btn share">Share</button>
                </div>
            </div>

            <div class="file-card" onclick="showFileDetails('fireplace-design.skp')">
                <div class="file-icon design">📐</div>
                <div class="file-name">Fireplace Design.skp</div>
                <div class="file-meta">Mike Johnson • Fireplace Surround • 3.7 MB</div>
                <div class="file-actions">
                    <button class="file-action-btn download">Download</button>
                    <button class="file-action-btn share">Share</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- File Details Modal -->
<div class="modal" id="fileModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title" id="modalTitle">File Details</h2>
            <button class="close-btn" onclick="closeModal()">&times;</button>
        </div>

        <div class="file-details">
            <div class="detail-row">
                <span class="detail-label">File Name:</span>
                <span class="detail-value" id="fileName">Design Specifications.pdf</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Customer:</span>
                <span class="detail-value" id="fileCustomer">John Smith</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Project:</span>
                <span class="detail-value" id="fileProject">Kitchen Renovation</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">File Type:</span>
                <span class="detail-value" id="fileType">PDF Document</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">File Size:</span>
                <span class="detail-value" id="fileSize">2.3 MB</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Uploaded:</span>
                <span class="detail-value" id="fileUploaded">Jan 15, 2024</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Last Modified:</span>
                <span class="detail-value" id="fileModified">Jan 20, 2024</span>
            </div>
        </div>

        <div class="action-buttons">
            <button class="btn primary">Download</button>
            <button class="btn secondary">Share</button>
            <button class="btn secondary">Rename</button>
            <button class="btn secondary" style="color: #dc3545;">Delete</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // File upload functionality
    function triggerFileUpload() {
        document.getElementById('fileInput').click();
    }

    function handleFileUpload(event) {
        const files = event.target.files;
        for (let file of files) {
            console.log('Uploading:', file.name);
            // Add file upload logic here
            addFileToGrid(file);
        }
    }

    function addFileToGrid(file) {
        const filesGrid = document.getElementById('filesGrid');
        const fileCard = document.createElement('div');
        fileCard.className = 'file-card';
        fileCard.innerHTML = `
                <div class="file-icon document">📄</div>
                <div class="file-name">${file.name}</div>
                <div class="file-meta">New Upload • ${(file.size / 1024 / 1024).toFixed(1)} MB</div>
                <div class="file-actions">
                    <button class="file-action-btn download">Download</button>
                    <button class="file-action-btn share">Share</button>
                </div>
            `;
        filesGrid.appendChild(fileCard);
    }

    // Drag and drop functionality
    const uploadArea = document.getElementById('uploadArea');

    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        const files = e.dataTransfer.files;
        for (let file of files) {
            addFileToGrid(file);
        }
    });

    // Category filtering
    function filterByCategory(category) {
        document.querySelectorAll('.category-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        event.target.classList.add('active');

        // Add category filtering logic here
        console.log('Filtering by category:', category);
    }

    // Customer/Project filtering
    function filterFiles() {
        const customer = document.getElementById('customer-select').value;
        const project = document.getElementById('project-select').value;

        console.log('Filtering files:', {
            customer,
            project
        });
        // Add filtering logic here
    }

    // File details modal
    function showFileDetails(fileName) {
        const modal = document.getElementById('fileModal');
        const modalTitle = document.getElementById('modalTitle');

        // Set file details based on filename
        const fileDetails = {
            'design-specs.pdf': {
                name: 'Design Specifications.pdf',
                customer: 'John Smith',
                project: 'Kitchen Renovation',
                type: 'PDF Document',
                size: '2.3 MB',
                uploaded: 'Jan 15, 2024',
                modified: 'Jan 20, 2024'
            },
            'kitchen-layout.dwg': {
                name: 'Kitchen Layout.dwg',
                customer: 'John Smith',
                project: 'Kitchen Renovation',
                type: 'CAD Drawing',
                size: '1.8 MB',
                uploaded: 'Jan 16, 2024',
                modified: 'Jan 18, 2024'
            },
            'stone-samples.jpg': {
                name: 'Stone Samples.jpg',
                customer: 'Maria Santos',
                project: 'Bathroom Vanity',
                type: 'Image File',
                size: '4.1 MB',
                uploaded: 'Jan 17, 2024',
                modified: 'Jan 17, 2024'
            }
        };

        const details = fileDetails[fileName] || {
            name: fileName,
            customer: 'Unknown',
            project: 'Unknown',
            type: 'Unknown',
            size: 'Unknown',
            uploaded: 'Unknown',
            modified: 'Unknown'
        };

        document.getElementById('fileName').textContent = details.name;
        document.getElementById('fileCustomer').textContent = details.customer;
        document.getElementById('fileProject').textContent = details.project;
        document.getElementById('fileType').textContent = details.type;
        document.getElementById('fileSize').textContent = details.size;
        document.getElementById('fileUploaded').textContent = details.uploaded;
        document.getElementById('fileModified').textContent = details.modified;

        modal.style.display = 'block';
    }

    function closeModal() {
        document.getElementById('fileModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('fileModal');
        if (event.target === modal) {
            closeModal();
        }
    }

    // Navigation functionality
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', () => {
            document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
            item.classList.add('active');
        });
    });

    // Search functionality
    document.querySelector('.search-bar input').addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        // Add search logic here
    });
</script>
@endpush