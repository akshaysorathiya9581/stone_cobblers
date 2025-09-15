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
                    <a href="" class="btn secondary">
                        <i>📊</i> Reports
                    </a>
                    <a href="{{ route('admin.quotes.create') }}" class="btn primary">
                        <i>➕</i> Create Quote
                    </a>
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

            <!--  -->
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
                        <tr onclick="showCustomerQuotes('John Smith')">
                            <td class="customer-info">
                                <div class="customer-avatar">JS</div>
                                <div class="customer-details">
                                    <h4>John Smith</h4>
                                    <p>Kitchen Renovation</p>
                                </div>
                            </td>
                            <td class="quote-number">QT-2024-001</td>
                            <td class="amount approved">$12,450.00</td>
                            <td><span class="status-tag status-approved">Approved</span></td>
                            <td class="date">Jan 15, 2024</td>
                            <td class="date">Feb 15, 2024</td>
                            <td class="actions">
                                <button class="action-btn view">View</button>
                            </td>
                        </tr>
    
                        <tr onclick="showCustomerQuotes('Maria Santos')">
                            <td class="customer-info">
                                <div class="customer-avatar">MS</div>
                                <div class="customer-details">
                                    <h4>Maria Santos</h4>
                                    <p>Bathroom Vanity</p>
                                </div>
                            </td>
                            <td class="quote-number">QT-2024-002</td>
                            <td class="amount pending">$8,750.00</td>
                            <td><span class="status-tag status-sent">Sent</span></td>
                            <td class="date">Jan 18, 2024</td>
                            <td class="date">Feb 18, 2024</td>
                            <td class="actions">
                                <button class="action-btn send">Send</button>
                                <button class="action-btn view">View</button>
                            </td>
                        </tr>
    
                        <tr onclick="showCustomerQuotes('Robert Wilson')">
                            <td class="customer-info">
                                <div class="customer-avatar">RW</div>
                                <div class="customer-details">
                                    <h4>Robert Wilson</h4>
                                    <p>Outdoor Patio</p>
                                </div>
                            </td>
                            <td class="quote-number">QT-2024-003</td>
                            <td class="amount rejected">$15,200.00</td>
                            <td><span class="status-tag status-rejected">Rejected</span></td>
                            <td class="date">Jan 20, 2024</td>
                            <td class="date">Feb 20, 2024</td>
                            <td class="actions">
                                <button class="action-btn edit">Edit</button>
                                <button class="action-btn view">View</button>
                            </td>
                        </tr>
    
                        <tr onclick="showCustomerQuotes('Lisa Davis')">
                            <td class="customer-info">
                                <div class="customer-avatar">LD</div>
                                <div class="customer-details">
                                    <h4>Lisa Davis</h4>
                                    <p>Kitchen Island</p>
                                </div>
                            </td>
                            <td class="quote-number">QT-2024-004</td>
                            <td class="amount pending">$9,800.00</td>
                            <td><span class="status-tag status-draft">Draft</span></td>
                            <td class="date">Jan 22, 2024</td>
                            <td class="date">Feb 22, 2024</td>
                            <td class="actions">
                                <button class="action-btn edit">Edit</button>
                                <button class="action-btn send">Send</button>
                            </td>
                        </tr>
    
                        <tr onclick="showCustomerQuotes('Mike Johnson')">
                            <td class="customer-info">
                                <div class="customer-avatar">MJ</div>
                                <div class="customer-details">
                                    <h4>Mike Johnson</h4>
                                    <p>Fireplace Surround</p>
                                </div>
                            </td>
                            <td class="quote-number">QT-2024-005</td>
                            <td class="amount approved">$6,500.00</td>
                            <td><span class="status-tag status-approved">Approved</span></td>
                            <td class="date">Jan 25, 2024</td>
                            <td class="date">Feb 25, 2024</td>
                            <td class="actions">
                                <button class="action-btn view">View</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Customer Quotes Modal -->
    <div class="modal modal-medium" id="customerModal">
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
                <h3 class="title">All Quotes for this Customer</h3>
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
        window.onclick = function (event) {
            const modal = document.getElementById('customerModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
@endpush