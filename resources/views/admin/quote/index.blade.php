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
        .icon-dashboard::before { content: "🏠"; }
        .icon-customers::before { content: "👥"; }
        .icon-projects::before { content: "📋"; }
        .icon-quotes::before { content: "💰"; }
        .icon-files::before { content: "📁"; }
        .icon-reports::before { content: "📊"; }
        .icon-settings::before { content: "⚙️"; }
        .icon-starred::before { content: "⭐"; }
        .icon-pinned::before { content: "📌"; }

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
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* Breadcrumb */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #666;
        }

        .breadcrumb-item {
            cursor: pointer;
            transition: color 0.2s;
        }

        .breadcrumb-item:hover {
            color: rgb(22, 163, 74);
        }

        .breadcrumb-separator {
            color: #ccc;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .stat-card h3 {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .stat-card .value {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin-bottom: 4px;
        }

        .stat-card .change {
            font-size: 12px;
            color: rgb(22, 163, 74);
        }

        /* Tabs */
        .tabs {
            display: flex;
            border-bottom: 1px solid #e0e0e0;
            margin-bottom: 30px;
        }

        .tab {
            padding: 12px 24px;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: #666;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
        }

        .tab.active {
            color: rgb(22, 163, 74);
            border-bottom-color: rgb(22, 163, 74);
        }

        /* Quotes Table */
        .quotes-table {
            background: white;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            overflow: hidden;
        }

        .table-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr 120px;
            padding: 15px 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
            font-weight: 600;
            font-size: 14px;
        }

        .table-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr 120px;
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
            align-items: center;
            transition: background-color 0.2s;
        }

        .table-row:hover {
            background-color: #f8f9fa;
        }

        .customer-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .customer-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: rgb(22, 163, 74);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 12px;
        }

        .customer-details h4 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .customer-details p {
            font-size: 12px;
            color: #666;
        }

        .quote-number {
            font-weight: 600;
            color: rgb(22, 163, 74);
            font-size: 14px;
        }

        .amount {
            font-weight: 600;
            font-size: 14px;
        }

        .amount.pending {
            color: #ffc107;
        }

        .amount.approved {
            color: #28a745;
        }

        .amount.rejected {
            color: #dc3545;
        }

        .status-tag {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            text-align: center;
        }

        .status-draft {
            background-color: #f8f9fa;
            color: #666;
        }

        .status-sent {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-expired {
            background-color: #fff3cd;
            color: #856404;
        }

        .date {
            font-size: 12px;
            color: #666;
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

        .action-btn.edit {
            background-color: #e8f5e8;
            color: rgb(22, 163, 74);
        }

        .action-btn.view {
            background-color: #f8f9fa;
            color: #666;
        }

        .action-btn.send {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        /* Customer Details Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
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
            max-width: 800px;
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

        .customer-quotes {
            margin-top: 20px;
        }

        .quote-item {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr 80px;
            gap: 15px;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            margin-bottom: 10px;
            align-items: center;
        }

        .quote-item:hover {
            background-color: #f8f9fa;
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
     <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="search-bar">
                <i>🔍</i>
                <input type="text" placeholder="Search quotes, customers...">
            </div>

            <div class="header-actions">
                <button class="header-btn secondary">
                    <i>📤</i> Export
                </button>
                <button class="header-btn primary">
                    <i>➕</i> New Quote
                </button>
                <div class="user-avatar">BM</div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                <span class="breadcrumb-item" onclick="goToDashboard()">Dashboard</span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-item">Quotes</span>
            </div>

            <div class="content-header">
                <h1 class="content-title">Quotes Management</h1>
                <div class="action-buttons">
                    <button class="btn secondary">
                        <i>📊</i> Reports
                    </button>
                    <button class="btn primary">
                        <i>➕</i> Create Quote
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Quotes</h3>
                    <div class="value">342</div>
                    <div class="change">+8% this month</div>
                </div>
                <div class="stat-card">
                    <h3>Pending Approval</h3>
                    <div class="value">23</div>
                    <div class="change">-5% this week</div>
                </div>
                <div class="stat-card">
                    <h3>Approved Quotes</h3>
                    <div class="value">156</div>
                    <div class="change">+12% this month</div>
                </div>
                <div class="stat-card">
                    <h3>Total Value</h3>
                    <div class="value">$847,230</div>
                    <div class="change">+15% vs last month</div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tabs">
                <button class="tab active">All Quotes</button>
                <button class="tab">Draft</button>
                <button class="tab">Sent</button>
                <button class="tab">Approved</button>
                <button class="tab">Rejected</button>
                <button class="tab">Expired</button>
            </div>

            <!-- Quotes Table -->
            <div class="quotes-table">
                <div class="table-header">
                    <div>Customer</div>
                    <div>Quote #</div>
                    <div>Amount</div>
                    <div>Status</div>
                    <div>Created</div>
                    <div>Expires</div>
                    <div>Actions</div>
                </div>

                <div class="table-row" onclick="showCustomerQuotes('John Smith')">
                    <div class="customer-info">
                        <div class="customer-avatar">JS</div>
                        <div class="customer-details">
                            <h4>John Smith</h4>
                            <p>Kitchen Renovation</p>
                        </div>
                    </div>
                    <div class="quote-number">QT-2024-001</div>
                    <div class="amount approved">$12,450.00</div>
                    <div><span class="status-tag status-approved">Approved</span></div>
                    <div class="date">Jan 15, 2024</div>
                    <div class="date">Feb 15, 2024</div>
                    <div class="actions">
                        <button class="action-btn view">View</button>
                    </div>
                </div>

                <div class="table-row" onclick="showCustomerQuotes('Maria Santos')">
                    <div class="customer-info">
                        <div class="customer-avatar">MS</div>
                        <div class="customer-details">
                            <h4>Maria Santos</h4>
                            <p>Bathroom Vanity</p>
                        </div>
                    </div>
                    <div class="quote-number">QT-2024-002</div>
                    <div class="amount pending">$8,750.00</div>
                    <div><span class="status-tag status-sent">Sent</span></div>
                    <div class="date">Jan 18, 2024</div>
                    <div class="date">Feb 18, 2024</div>
                    <div class="actions">
                        <button class="action-btn send">Send</button>
                        <button class="action-btn view">View</button>
                    </div>
                </div>

                <div class="table-row" onclick="showCustomerQuotes('Robert Wilson')">
                    <div class="customer-info">
                        <div class="customer-avatar">RW</div>
                        <div class="customer-details">
                            <h4>Robert Wilson</h4>
                            <p>Outdoor Patio</p>
                        </div>
                    </div>
                    <div class="quote-number">QT-2024-003</div>
                    <div class="amount rejected">$15,200.00</div>
                    <div><span class="status-tag status-rejected">Rejected</span></div>
                    <div class="date">Jan 20, 2024</div>
                    <div class="date">Feb 20, 2024</div>
                    <div class="actions">
                        <button class="action-btn edit">Edit</button>
                        <button class="action-btn view">View</button>
                    </div>
                </div>

                <div class="table-row" onclick="showCustomerQuotes('Lisa Davis')">
                    <div class="customer-info">
                        <div class="customer-avatar">LD</div>
                        <div class="customer-details">
                            <h4>Lisa Davis</h4>
                            <p>Kitchen Island</p>
                        </div>
                    </div>
                    <div class="quote-number">QT-2024-004</div>
                    <div class="amount pending">$9,800.00</div>
                    <div><span class="status-tag status-draft">Draft</span></div>
                    <div class="date">Jan 22, 2024</div>
                    <div class="date">Feb 22, 2024</div>
                    <div class="actions">
                        <button class="action-btn edit">Edit</button>
                        <button class="action-btn send">Send</button>
                    </div>
                </div>

                <div class="table-row" onclick="showCustomerQuotes('Mike Johnson')">
                    <div class="customer-info">
                        <div class="customer-avatar">MJ</div>
                        <div class="customer-details">
                            <h4>Mike Johnson</h4>
                            <p>Fireplace Surround</p>
                        </div>
                    </div>
                    <div class="quote-number">QT-2024-005</div>
                    <div class="amount approved">$6,500.00</div>
                    <div><span class="status-tag status-approved">Approved</span></div>
                    <div class="date">Jan 25, 2024</div>
                    <div class="date">Feb 25, 2024</div>
                    <div class="actions">
                        <button class="action-btn view">View</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <!-- Customer Quotes Modal -->
<div class="modal" id="customerModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title" id="modalTitle">Customer Quotes</h2>
            <button class="close-btn" onclick="closeModal()">&times;</button>
        </div>
        
        <div class="customer-info">
            <div class="customer-avatar" id="modalAvatar">JS</div>
            <div class="customer-details">
                <h4 id="modalCustomerName">John Smith</h4>
                <p id="modalCustomerProject">Kitchen Renovation</p>
            </div>
        </div>

        <div class="customer-quotes">
            <h3>All Quotes for this Customer</h3>
            <div class="quote-item">
                <div><strong>QT-2024-001</strong></div>
                <div>$12,450.00</div>
                <div><span class="status-tag status-approved">Approved</span></div>
                <div>Jan 15, 2024</div>
                <div class="actions">
                    <button class="action-btn view">View</button>
                </div>
            </div>
            <div class="quote-item">
                <div><strong>QT-2023-089</strong></div>
                <div>$8,200.00</div>
                <div><span class="status-tag status-approved">Approved</span></div>
                <div>Dec 10, 2023</div>
                <div class="actions">
                    <button class="action-btn view">View</button>
                </div>
            </div>
            <div class="quote-item">
                <div><strong>QT-2023-076</strong></div>
                <div>$5,800.00</div>
                <div><span class="status-tag status-rejected">Rejected</span></div>
                <div>Nov 28, 2023</div>
                <div class="actions">
                    <button class="action-btn view">View</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



@push('scripts')
       <script>
        // Tab functionality
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
            });
        });

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

        // Action buttons
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const action = btn.textContent.toLowerCase();
                const row = btn.closest('.table-row');
                const customerName = row.querySelector('h4').textContent;
                
                if (action === 'edit') {
                    alert(`Edit quote for ${customerName}`);
                } else if (action === 'view') {
                    alert(`View quote for ${customerName}`);
                } else if (action === 'send') {
                    alert(`Send quote for ${customerName}`);
                }
            });
        });

        // Customer quotes modal functionality
        function showCustomerQuotes(customerName) {
            const modal = document.getElementById('customerModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalCustomerName = document.getElementById('modalCustomerName');
            const modalCustomerProject = document.getElementById('modalCustomerProject');
            const modalAvatar = document.getElementById('modalAvatar');
            
            // Set modal content based on customer
            modalTitle.textContent = `${customerName} - Quotes`;
            modalCustomerName.textContent = customerName;
            modalAvatar.textContent = customerName.split(' ').map(n => n[0]).join('');
            
            // Set project based on customer (this would come from data)
            const projects = {
                'John Smith': 'Kitchen Renovation',
                'Maria Santos': 'Bathroom Vanity',
                'Robert Wilson': 'Outdoor Patio',
                'Lisa Davis': 'Kitchen Island',
                'Mike Johnson': 'Fireplace Surround'
            };
            modalCustomerProject.textContent = projects[customerName] || 'Project';
            
            modal.style.display = 'block';
        }

        function closeModal() {
            document.getElementById('customerModal').style.display = 'none';
        }

        function goToDashboard() {
            window.location.href = 'dashboard.html';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('customerModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
@endpush
