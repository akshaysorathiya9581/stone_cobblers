@php use Illuminate\Support\Str; @endphp

@extends('layouts.admin')

@section('title', 'Quotes')

@push('css')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .dashboard {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #f1f3f4;
            border-right: 1px solid #e0e0e0;
            padding: 20px 0;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid #e0e0e0;
            margin-bottom: 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background-color: rgb(22, 163, 74);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .nav-section {
            margin-bottom: 30px;
        }

        .nav-section h3 {
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 10px;
            padding: 0 20px;
            font-weight: 600;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 20px;
            color: #333;
            text-decoration: none;
            transition: background-color 0.2s;
            cursor: pointer;
        }

        .nav-item:hover {
            background-color: #e8f5e8;
        }

        .nav-item.active {
            background-color: #e8f5e8;
            color: rgb(22, 163, 74);
            font-weight: 500;
        }

        .nav-item i {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }

        /* Icon styles */
        .icon-dashboard::before {
            content: "🏠";
        }

        .icon-customers::before {
            content: "👥";
        }

        .icon-projects::before {
            content: "📋";
        }

        .icon-quotes::before {
            content: "💰";
        }

        .icon-files::before {
            content: "📁";
        }

        .icon-reports::before {
            content: "📊";
        }

        .icon-settings::before {
            content: "⚙️";
        }

        .icon-starred::before {
            content: "⭐";
        }

        .icon-pinned::before {
            content: "📌";
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            background: white;
            border-bottom: 1px solid #e0e0e0;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .search-bar {
            display: flex;
            align-items: center;
            background: #f1f3f4;
            border-radius: 8px;
            padding: 8px 16px;
            width: 400px;
        }

        .search-bar input {
            border: none;
            background: none;
            outline: none;
            width: 100%;
            font-size: 14px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.2s;
        }

        .header-btn.primary {
            background-color: rgb(22, 163, 74);
            color: white;
        }

        .header-btn.secondary {
            background-color: #f1f3f4;
            color: #333;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: #ff9500;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        /* Content Area */
        .content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .content-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn.primary {
            background-color: #000;
            color: white;
        }

        .btn.secondary {
            background-color: white;
            color: #333;
            border: 1px solid #e0e0e0;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* File Upload Area */
        .upload-section {
            background: white;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 30px;
            margin-bottom: 30px;
        }

        .upload-area {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            transition: border-color 0.3s;
            background-color: #f8f9fa;
        }

        .upload-area.dragover {
            border-color: rgb(22, 163, 74);
            background-color: #e8f5e8;
        }

        .upload-icon {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 20px;
        }

        .upload-text {
            font-size: 18px;
            color: #666;
            margin-bottom: 10px;
        }

        .upload-subtext {
            font-size: 14px;
            color: #999;
            margin-bottom: 20px;
        }

        .upload-btn {
            background-color: rgb(22, 163, 74);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }

        /* Customer/Project Selector */
        .selector-section {
            background: white;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 20px;
            margin-bottom: 30px;
        }

        .selector-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .selector-group {
            display: flex;
            flex-direction: column;
        }

        .selector-group label {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            color: #333;
        }

        .selector-group select {
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
            background-color: white;
        }

        /* File Categories */
        .categories {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .category-tab {
            padding: 8px 16px;
            border: 1px solid #e0e0e0;
            border-radius: 20px;
            background: white;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
        }

        .category-tab.active {
            background-color: rgb(22, 163, 74);
            color: white;
            border-color: rgb(22, 163, 74);
        }

        /* Files Grid */
        .files-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .file-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .file-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .file-icon {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 12px;
        }

        .file-icon.pdf {
            background-color: #ff4444;
            color: white;
        }

        .file-icon.design {
            background-color: #4CAF50;
            color: white;
        }

        .file-icon.image {
            background-color: #2196F3;
            color: white;
        }

        .file-icon.document {
            background-color: #FF9800;
            color: white;
        }

        .file-name {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 4px;
            word-break: break-word;
        }

        .file-meta {
            font-size: 12px;
            color: #666;
            margin-bottom: 8px;
        }

        .file-actions {
            display: flex;
            gap: 8px;
        }

        .file-action-btn {
            padding: 4px 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: background-color 0.2s;
        }

        .file-action-btn.download {
            background-color: #e8f5e8;
            color: rgb(22, 163, 74);
        }

        .file-action-btn.delete {
            background-color: #f8d7da;
            color: #dc3545;
        }

        .file-action-btn.share {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        /* File Details Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 8px;
            padding: 30px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .modal-title {
            font-size: 20px;
            font-weight: bold;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .file-details {
            margin-bottom: 20px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .detail-label {
            font-weight: 500;
            color: #666;
        }

        .detail-value {
            color: #333;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .search-bar {
                width: 250px;
            }

            .selector-grid {
                grid-template-columns: 1fr;
            }

            .files-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
        }
    </style>
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
                <div class="user-avatar">BM</div>
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
                        <select id="customer-select" onchange="filterFiles()">
                            <option value="">All Customers</option>
                            <option value="john-smith">John Smith</option>
                            <option value="maria-santos">Maria Santos</option>
                            <option value="robert-wilson">Robert Wilson</option>
                            <option value="lisa-davis">Lisa Davis</option>
                            <option value="mike-johnson">Mike Johnson</option>
                        </select>
                    </div>
                    <div class="selector-group">
                        <label for="project-select">Project</label>
                        <select id="project-select" onchange="filterFiles()">
                            <option value="">All Projects</option>
                            <option value="kitchen-renovation">Kitchen Renovation</option>
                            <option value="bathroom-vanity">Bathroom Vanity</option>
                            <option value="outdoor-patio">Outdoor Patio</option>
                            <option value="kitchen-island">Kitchen Island</option>
                            <option value="fireplace-surround">Fireplace Surround</option>
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
