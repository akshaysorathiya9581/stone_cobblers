@extends('layouts.admin')

@section('title', ucfirst($quoteType ?? 'Kitchen') . ' Quotes — Prices')

@push('css')
<style>
    /* Completed state for progress steps */
    .progress-step.completed {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%) !important;
        border-color: #16a34a !important;
        transform: scale(1.1);
        transition: all 0.3s ease;
    }
    
    .progress-step.completed svg {
        display: block;
    }
    
    /* Success step animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes scaleIn {
        from {
            transform: scale(0.8);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }
    
    .success-step {
        animation: fadeInUp 0.6s ease-out;
    }
    
    .success-step__icon {
        animation: scaleIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }
</style>
@endpush

@section('content')
    <!-- New Step Design -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <button class="sidebar-toggle">
                <i class="fas fa-bars toggle-icon"></i>
            </button>
            <!-- <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search quotes, customers...">
            </div> -->

            <div class="header-actions">
                <a href="{{ route('admin.profile.edit') }}"
                    class="user-avatar">{{ auth()->user() ? Str::upper(Str::substr(auth()->user()->name ?? 'U', 0, 2)) : 'U' }}</a>
            </div>
        </div>

        <div class="content bg-white">
            <div class="quote-details">
                <div class="breadcrumb mb-8">
                    <span class="breadcrumb-item">Quote Generation – Step 1 of 4</span>
                </div>
                <div class="content-header d-block">
                    <h2 class="title">Stone by Stone: Your Perfect {{ ucfirst($quoteType ?? 'Kitchen') }} Quote</h2>
                    <h3 class="subtitle">Step 1 of 4 – Select Project</h3>
                    <div class="quote-steps">
                        <div class="progress-container">
                            <div class="progress-step active">1</div>
                            <div class="progress-line"></div>
                            <div class="progress-step inactive">2</div>
                            <div class="progress-line"></div>
                            <div class="progress-step inactive">3</div>
                            <div class="progress-line"></div>
                            <div class="progress-step inactive">4</div>
                        </div>
                        <div class="progress-labels">
                            <div class="progress-label active">Project</div>
                            <div class="progress-label">Quantities</div>
                            <div class="progress-label">Details</div>
                            <div class="progress-label">Review</div>
                        </div>
                    </div>
                </div>

                <!-- Step 0: Project Selection -->
                <div class="quote-stepview project-step">
                    <div class="quote-stepview__full" style="max-width: 600px;">
                        <div class="stepview-title text-align-left">
                            <h3 class="title mb-8">Select a Project</h3>
                            <p>Choose the project for which you want to create a quote</p>
                        </div>
                        <div class="form-fields">
                            <div class="form-field">
                                <label class="form-label required">Project</label>
                                <select class="form-input custom-select" name="project_id" id="project_id"
                                    data-placeholder="Select Project" required>
                                    <option value="">-- Select Project --</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}"
                                            data-customer="{{ $project->customer->first_name ?? '' }} {{ $project->customer->last_name ?? '' }}"
                                            data-customer-id="{{ $project->user_id }}">
                                            {{ $project->name }} - {{ $project->customer->first_name ?? '' }}
                                            {{ $project->customer->last_name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="error-msg" data-for="project_id"></div>
                            </div>

                            <!-- <div class="project-info"
                                    style="display: none; margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                                    <h4 class="section-title">Project Details</h4>
                                    <p><strong>Customer:</strong> <span id="selected-customer">-</span></p>
                                    <p><strong>Project:</strong> <span id="selected-project">-</span></p>
                                    <p><strong>Status:</strong> <span id="selected-status">-</span></p>
                                </div> -->
                        </div>
                    </div>
                </div>

                <!-- Step 1: Quantities -->
                <div class="quote-stepview first-step">
                    <div class="quote-stepview__left">
                        <div class="stepview-title">
                            <h3 class="title mb-8">Quote Items</h3>
                            <p>Adjust quantities for each item</p>
                        </div>
                        <div class="custom-table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 15%;">Project/Item Name</th>
                                        <th style="width: 15%;">Scope/Material</th>
                                        <th style="width: 15%;">QTY</th>
                                        <th style="width: 15%;">Unit Cost</th>
                                        <th style="width: 15%;">Total</th>
                                        <th style="width: 10%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($KITCHEN_TOP ?? [] as $projectName => $item)
                                        <tr data-taxable="{{ $item->is_taxable ? '1' : '0' }}">
                                            <td class="label item-name-td">
                                                {{ $item->project }}
                                                @if($item->is_taxable)
                                                    <span class="t_tag">T</span>
                                                @endif
                                            </td>
                                            <td class="label scope-material-td">{{ $item->scope_material ?? '-' }}</td>
                                            <td class="label">
                                                <div class="quantity-controls">
                                                    <button class="quantity-btn minus">−</button>
                                                    <input type="number" class="quantity-input" value="1" min="0" />
                                                    <button class="quantity-btn plus">+</button>
                                                </div>
                                            </td>
                                            <td class="label">${{ number_format($item->cost, 2) }}</td>
                                            <td class="label">${{ number_format($item->cost, 2) }}</td>
                                            <td class="label actions-td">
                                                <button class="action-btn edit edit-item">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button class="action-btn delete remove-row">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr data-taxable="1">
                                            <td class="label item-name-td">Kitchen - Sq Ft <span class="t_tag">T</span> </td>
                                            <td class="label scope-material-td">Granite</td>
                                            <td class="label">
                                                <div class="quantity-controls">
                                                    <button class="quantity-btn minus">−</button>
                                                    <input type="number" class="quantity-input" value="50" min="0" />
                                                    <button class="quantity-btn plus">+</button>
                                                </div>
                                            </td>
                                            <td class="label">$75.00</td>
                                            <td class="label">$3,750.00</td>
                                            <td class="label actions-td">
                                                <button class="action-btn edit edit-item">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button class="action-btn delete remove-row">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr data-taxable="1">
                                            <td class="label item-name-td">Labor Charge <span class="t_tag">T</span> </td>
                                            <td class="label scope-material-td">Service</td>
                                            <td class="label">
                                                <div class="quantity-controls">
                                                    <button class="quantity-btn minus">−</button>
                                                    <input type="number" class="quantity-input" value="12" min="0" />
                                                    <button class="quantity-btn plus">+</button>
                                                </div>
                                            </td>
                                            <td class="label">$120.00</td>
                                            <td class="label">$1,440.00</td>
                                            <td class="label actions-td">
                                                <button class="action-btn edit edit-item">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button class="action-btn delete remove-row">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr data-taxable="0">
                                            <td class="label item-name-td">Edge - Lin Ft</td>
                                            <td class="label scope-material-td">Stone</td>
                                            <td class="label">
                                                <div class="quantity-controls">
                                                    <button class="quantity-btn minus">−</button>
                                                    <input type="number" class="quantity-input" value="16" min="0" />
                                                    <button class="quantity-btn plus">+</button>
                                                </div>
                                            </td>
                                            <td class="label">$85.00</td>
                                            <td class="label">$1,360.00</td>
                                            <td class="label actions-td">
                                                <button class="action-btn edit edit-item">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button class="action-btn delete remove-row">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr data-taxable="1">
                                            <td class="label item-name-td">Arc Charges <span class="t_tag">T</span> </td>
                                            <td class="label scope-material-td">-</td>
                                            <td class="label">
                                                <div class="quantity-controls">
                                                    <button class="quantity-btn minus">−</button>
                                                    <input type="number" class="quantity-input" value="1" min="0" />
                                                    <button class="quantity-btn plus">+</button>
                                                </div>
                                            </td>
                                            <td class="label">$250.00</td>
                                            <td class="label">$250.00</td>
                                            <td class="label actions-td">
                                                <button class="action-btn edit edit-item">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button class="action-btn delete remove-row">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr data-taxable="0">
                                            <td class="label item-name-td">Bump-Outs</td>
                                            <td class="label scope-material-td">-</td>
                                            <td class="label">
                                                <div class="quantity-controls">
                                                    <button class="quantity-btn minus">−</button>
                                                    <input type="number" class="quantity-input" value="62" min="0" />
                                                    <button class="quantity-btn plus">+</button>
                                                </div>
                                            </td>
                                            <td class="label">$15.00</td>
                                            <td class="label">$930.00</td>
                                            <td class="label actions-td">
                                                <button class="action-btn edit edit-item">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button class="action-btn delete remove-row">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforelse
                                    <!-- Add new item row -->
                                    <tr class="add-row-item">
                                        <td class="label">
                                            <input type="text" class="form-input add-item-name" placeholder="Item Name" />
                                            <div class="validation-msg small text-danger" style="display:none;"></div>
                                        </td>
                                        <td class="label">
                                            <input type="text" class="form-input add-item-scope"
                                                placeholder="Scope/Material" />
                                            <div class="validation-msg small text-danger" style="display:none;"></div>
                                        </td>
                                        <td class="label">
                                            <div class="quantity-controls">
                                                <button class="quantity-btn minus">−</button>
                                                <input type="number" class="quantity-input add-item-qty" value="1"
                                                    min="0" />
                                                <button class="quantity-btn plus">+</button>
                                            </div>
                                            <div class="validation-msg small text-danger" style="display:none;"></div>
                                        </td>
                                        <td class="label">
                                            <input type="number" class="form-input text-align-center add-item-unit"
                                                placeholder="Unit Price" step="0.01" min="0" />
                                            <div class="validation-msg small text-danger" style="display:none;"></div>
                                        </td>
                                        <td class="label add-item-line-total-display">$0.00</td>
                                        <td class="label">
                                            <button type="button" class="btn add-btn add-item-btn">+ Add</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="quote-stepview__right">
                        <div class="stepview-title">
                            <h3 class="title">Quote Summary</h3>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">Subtotal</div>
                            <div class="summary-amount" id="subtotal">$7,730.00</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-label">{{ setting('tax_label', 'Tax') }}
                                ({{ setting('tax_rate', 0.08) * 100 }}%)</div>
                            <div class="summary-amount" id="tax">$618.40</div>
                        </div>
                        <div class="summary-item grand-total">
                            <div class="summary-label">Grand Total</div>
                            <div class="summary-amount" id="grand-total">$8,348.40</div>
                        </div>
                    </div>
                </div>
                <!-- Second Step -->
                <div class="quote-stepview first-step">
                    <div class="quote-stepview__full">
                        <div class="quote-accordion">
                            <div class="quote-accordion__item">
                                <div class="quote-accordion__header">
                                    Box Manufacturer
                                    <i class="fas fa-chevron-down quote-accordion__icon"></i>
                                </div>
                                <div class="quote-accordion__body">
                                    <div class="custom-table">
                                        <table class="table" id="box-manufacturer-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 30%;">Manufacturer</th>
                                                    <th style="width: 20%;">Unit Price</th>
                                                    <th style="width: 20%;">Qty</th>
                                                    <th style="width: 20%;">Line Total</th>
                                                    <th style="width: 10%;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($KITCHEN_MANUFACTURER ?? [] as $manufacturerName => $item)
                                                    <tr data-taxable="{{ $item->is_taxable ? '1' : '0' }}">
                                                        <td class="label manufacturer-name-td">
                                                            {{ $item->project }}
                                                            @if($item->is_taxable)
                                                                <span class="t_tag">T</span>
                                                            @endif
                                                        </td>
                                                        <td class="label unit-price-td">{{ number_format($item->cost, 2) }}</td>
                                                        <td class="label">
                                                            <div class="quantity-controls">
                                                                <button class="quantity-btn minus">−</button>
                                                                <input type="number" class="quantity-input" value="1" min="0" />
                                                                <button class="quantity-btn plus">+</button>
                                                            </div>
                                                        </td>
                                                        <td class="label line-total">${{ number_format($item->cost, 2) }}</td>
                                                        <td class="label actions-td">
                                                            <button class="action-btn edit edit-box">
                                                                <i class="fa-solid fa-pen-to-square"></i>
                                                            </button>
                                                            <button class="action-btn delete remove-row">
                                                                <i class="fa-solid fa-trash"></i>
                                                                </button1>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr data-taxable="0">
                                                        <td class="label manufacturer-name-td">Premium Box Co.</td>
                                                        <td class="label unit-price-td">350.00</td>
                                                        <td class="label">
                                                            <div class="quantity-controls">
                                                                <button class="quantity-btn minus">−</button>
                                                                <input type="number" class="quantity-input" value="10"
                                                                    min="0" />
                                                                <button class="quantity-btn plus">+</button>
                                                            </div>
                                                        </td>
                                                        <td class="label line-total">$3,500.00</td>
                                                        <td class="label actions-td">
                                                            <button class="action-btn edit edit-box">
                                                                <i class="fa-solid fa-pen-to-square"></i>
                                                            </button>
                                                            <button class="action-btn delete remove-row">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr data-taxable="0">
                                                        <td class="label manufacturer-name-td">Eco-Wood Designs</td>
                                                        <td class="label unit-price-td">280.00</td>
                                                        <td class="label">
                                                            <div class="quantity-controls">
                                                                <button class="quantity-btn minus">−</button>
                                                                <input type="number" class="quantity-input" value="15"
                                                                    min="0" />
                                                                <button class="quantity-btn plus">+</button>
                                                            </div>
                                                        </td>
                                                        <td class="label line-total">$4,200.00</td>
                                                        <td class="label actions-td">
                                                            <button class="action-btn edit edit-box">
                                                                <i class="fa-solid fa-pen-to-square"></i>
                                                            </button>
                                                            <button class="action-btn delete remove-row">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                                <tr class="add-row-box">
                                                    <td class="label">
                                                        <input type="text" class="form-input add-box-name"
                                                            placeholder="Manufacturer Name" />
                                                        <div class="validation-msg small text-danger" style="display:none;">
                                                        </div>
                                                    </td>
                                                    <td class="label">
                                                        <input type="number"
                                                            class="form-input text-align-center add-box-unit"
                                                            placeholder="Unit Price" step="0.01" min="0" />
                                                        <div class="validation-msg small text-danger" style="display:none;">
                                                        </div>
                                                    </td>
                                                    <td class="label">
                                                        <div class="quantity-controls">
                                                            <button class="quantity-btn minus">−</button>
                                                            <input type="number" class="quantity-input add-box-qty"
                                                                value="1" min="0" />
                                                            <button class="quantity-btn plus">+</button>
                                                        </div>
                                                        <div class="validation-msg small text-danger" style="display:none;">
                                                        </div>
                                                    </td>
                                                    <td class="label add-box-line-total-display">
                                                        $0.00
                                                    </td>
                                                    <td class="label">
                                                        <button type="button" class="btn add-btn add-box-btn">+ Add</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="quote-accordion__item">
                                <div class="quote-accordion__header">
                                    Margin Markup
                                    <i class="fas fa-chevron-down quote-accordion__icon"></i>
                                </div>
                                <div class="quote-accordion__body">
                                    <div class="custom-table">
                                        <table class="table" id="margin-markup-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 40%;">Description</th>
                                                    <th style="width: 20%;">Multiplier</th>
                                                    <th style="width: 20%;">Result</th>
                                                    <th style="width: 20%;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($KITCHEN_MARGIN_MARKUP ?? [] as $marginDesc => $item)
                                                    <tr data-taxable="{{ $item->is_taxable ? '1' : '0' }}">
                                                        <td class="label margin-desc-td">
                                                            {{ $item->project }}
                                                            @if($item->is_taxable)
                                                                <span class="t_tag">T</span>
                                                            @endif
                                                        </td>
                                                        <td class="label margin-mul-td">{{ number_format($item->cost, 2) }}</td>
                                                        <td class="label margin-result">$0.00</td>
                                                        <td class="label actions-td">
                                                            <button class="action-btn edit edit-margin">
                                                                <i class="fa-solid fa-pen-to-square"></i>
                                                            </button>
                                                            <button class="action-btn delete remove-row">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr data-taxable="0">
                                                        <td class="label margin-desc-td">Standard Profit Margin</td>
                                                        <td class="label margin-mul-td">1.25</td>
                                                        <td class="label margin-result">$2,000.00</td>
                                                        <td class="label actions-td">
                                                            <button class="action-btn edit edit-margin">
                                                                <i class="fa-solid fa-pen-to-square"></i>
                                                            </button>
                                                            <button class="action-btn delete remove-row">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr data-taxable="0">
                                                        <td class="label margin-desc-td">Design Fee</td>
                                                        <td class="label margin-mul-td">1.15</td>
                                                        <td class="label margin-result">$1,500.00</td>
                                                        <td class="label actions-td">
                                                            <button class="action-btn edit edit-margin">
                                                                <i class="fa-solid fa-pen-to-square"></i>
                                                            </button>
                                                            <button class="action-btn delete remove-row">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforelse

                                                <!-- add-row (stays at bottom and is NOT replaced) -->
                                                <tr class="add-row-margin">
                                                    <td class="label">
                                                        <input type="text" class="form-input add-margin-desc"
                                                            placeholder="e.g., Design Fee" />
                                                        <div class="validation-msg small text-danger" style="display:none;">
                                                        </div>
                                                    </td>
                                                    <td class="label">
                                                        <input type="number"
                                                            class="form-input text-align-center add-margin-mul"
                                                            placeholder="e.g., 1.15" step="0.01" min="0" />
                                                        <div class="validation-msg small text-danger" style="display:none;">
                                                        </div>
                                                    </td>
                                                    <td class="label add-margin-result">$0.00</td>
                                                    <td class="label">
                                                        <button type="button" class="btn add-btn add-margin-btn">+
                                                            Add</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- Fourth Step: Review & Submit -->
                <div class="quote-stepview review-step">
                    <div class="quote-stepview__full">
                        <div class="stepview-title">
                            <h3 class="title mb-8">Review Your Quote</h3>
                            <p>Please review all details before submitting</p>
                        </div>

                        <div class="quote-stepview__summary">
                            <div class="summary-card">
                                <div class="summary-header">
                                    <h2 class="summary-title">Quote Summary</h2>
                                    <div class="final-total-section">
                                        <div class="final-total-label">Final Total</div>
                                        <div class="final-total-amount" id="review-grand-total">$0.00</div>
                                    </div>
                                </div>

                                <div class="summary-divider"></div>

                                <div class="summary-section">
                                    <h4 class="summary-title">Project Information</h4>
                                    <p><strong>Project:</strong> <span id="review-project-name">-</span></p>
                                    <p><strong>Customer:</strong> <span id="review-customer-name">-</span></p>
                                </div>

                                <div class="summary-divider"></div>

                                <div class="summary-section">
                                    <h4 class="summary-title">Quote Items</h4>
                                    <div id="review-items-list">
                                        <!-- Items will be populated by JavaScript -->
                                    </div>
                                </div>

                                <div class="summary-divider"></div>

                                <div class="summary-section">
                                    <h4 class="summary-title">Box Manufacturers</h4>
                                    <div id="review-manufacturers-list">
                                        <!-- Manufacturers will be populated by JavaScript -->
                                    </div>
                                </div>

                                <div class="summary-divider"></div>

                                <div class="summary-section">
                                    <h4 class="summary-title">Margin Markups</h4>
                                    <div id="review-margins-list">
                                        <!-- Margins will be populated by JavaScript -->
                                    </div>
                                </div>

                                <div class="summary-divider"></div>

                                <div class="summary-item total-row">
                                    <div class="summary-title mb-0">Subtotal</div>
                                    <div class="final-total-amount" id="review-subtotal">$0.00</div>
                                </div>

                                <div class="summary-item">
                                    <div class="item-description">{{ setting('tax_label', 'Tax') }}
                                        ({{ setting('tax_rate', 0.08) * 100 }}%)</div>
                                    <div class="item-price" id="review-tax">$0.00</div>
                                </div>

                                <div class="summary-item total-row">
                                    <div class="summary-title mb-0">Grand Total</div>
                                    <div class="final-total-amount" id="review-total">$0.00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Success Step -->
                <div class="quote-stepview success-step" style="display: none;">
                    <div class="quote-stepview__full" style="max-width: 700px; margin: 50px auto; text-align: center;">
                        <div class="success-step__icon" style="width: 120px; height: 120px; margin: 0 auto 30px; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 40px rgba(34, 197, 94, 0.3);">
                            <i class="fas fa-check" style="font-size: 60px; color: white;"></i>
                        </div>
                        
                        <h2 class="success-step__title" style="font-size: 32px; font-weight: 700; color: #16a34a; margin-bottom: 15px; letter-spacing: -0.5px;">
                            Quote Created Successfully!
                        </h2>
                        
                        <p class="success-step__description" style="font-size: 16px; color: #64748b; margin-bottom: 30px; line-height: 1.6;">
                            Your quote has been created and the PDF has been generated successfully.
                        </p>
                        
                        <div class="quote-success-card" style="background: #f8fafc; border: 2px solid #e2e8f0; border-radius: 12px; padding: 25px; margin-bottom: 35px;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 15px;">
                                <i class="fas fa-file-invoice" style="font-size: 24px; color: #16a34a;"></i>
                                <h3 style="font-size: 18px; font-weight: 600; color: #1e293b; margin: 0;">Quote Number</h3>
                            </div>
                            <p style="font-size: 28px; font-weight: 700; color: #16a34a; margin: 0; letter-spacing: 1px;">
                                <span id="success-quote-number">-</span>
                            </p>
                        </div>
                        
                        <div class="success-step__flex" style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                            <a href="#" id="view-all-quotes-btn" class="btn theme" style="padding: 14px 30px; font-size: 15px; font-weight: 600; border-radius: 8px; min-width: 180px;">
                                <i class="fas fa-home" style="display: inline-block; vertical-align: middle; margin-right: 8px;"></i>
                                View All Quotes
                            </a>
                            <a href="#" id="create-another-quote-btn" class="btn secondary" style="padding: 14px 30px; font-size: 15px; font-weight: 600; border-radius: 8px; min-width: 180px;">
                                <i class="fas fa-plus" style="display: inline-block; vertical-align: middle; margin-right: 8px;"></i>
                                Create Another Quote
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="step-footer">
            <div class="footer-content">
                <div class="step-indicator">Step 1 of 3</div>
                <div class="footer-actions">
                    <button class="btn secondary">Previous</button>
                    <button class="btn theme">Next</button>
                </div>
            </div>
        </footer>

    </div>
@endsection

@push('scripts')
    <script>
        jQuery(function ($) {
            // --- CONFIG & HELPERS ---
            const TAX_RATE = {{ setting('tax_rate', 0.08) }}; // Dynamic tax rate from settings
            const CURRENCY_SYMBOL = '{{ setting('currency_symbol', '$') }}';
            const CURRENCY_CODE = '{{ setting('currency_code', 'USD') }}';
            const currency = (n) => new Intl.NumberFormat('en-US', { style: 'currency', currency: CURRENCY_CODE }).format(Number(n || 0));
            const parseNumber = (v) => {
                if (v === null || v === undefined) return NaN;
                if (typeof v === 'number') return v;
                v = String(v).replace(/[^0-9\.\-]/g, '').trim();
                return v === '' ? NaN : parseFloat(v);
            };
            function escapeHtml(s) { return String(s || '').replace(/[&<>"'`=\/]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;', '/': '&#x2F;', '`': '&#x60;', '=': '&#x3D;' }[c]; }); }

            // Safe toastr notification
            function showToast(type, message) {
                if (typeof toastr !== 'undefined') {
                    toastr[type](message);
                } else {
                    console.log(`[${type.toUpperCase()}] ${message}`);
                }
            }

            // --- Step navigation ---
            const $steps = $('.quote-stepview');
            const totalSteps = $steps.length - 1; // Exclude success step
            let currentStep = 1;
            let selectedProjectId = null;
            let selectedProjectData = {};

            function showStep(step) {
                if (step < 1) step = 1;
                if (step > totalSteps) step = totalSteps;
                currentStep = step;
                $steps.hide();
                $steps.eq(step - 1).show();
                $('.progress-step').each(function (i) { $(this).toggleClass('active', (i + 1) <= step); });
                $('.progress-label').each(function (i) { $(this).toggleClass('active', (i + 1) === step); });
                $('.step-indicator').text('Step ' + step + ' of ' + totalSteps);
                $('.breadcrumb-item').text('Quote Generation – Step ' + step + ' of ' + totalSteps);

                // Update subtitle based on step
                const subtitles = [
                    'Step 1 of 4 – Select Project',
                    'Step 2 of 4 – Enter Item Quantities',
                    'Step 3 of 4 – Box Manufacturer & Margins',
                    'Step 4 of 4 – Review & Submit'
                ];
                $('.content-header .subtitle').text(subtitles[step - 1] || '');

                // If moving to review step, populate review data
                if (step === 4) {
                    populateReviewStep();
                }
            }

            showStep(1);

            // Project selection handler
            $(document).on('change', '#project_id', function () {
                selectedProjectId = $(this).val();
                const $selected = $(this).find('option:selected');

                if (selectedProjectId) {
                    selectedProjectData = {
                        id: selectedProjectId,
                        name: $selected.text().split(' - ')[0],
                        customer: $selected.data('customer'),
                        customerId: $selected.data('customer-id')
                    };

                    // Show project info
                    $('.project-info').show();
                    $('#selected-customer').text(selectedProjectData.customer);
                    $('#selected-project').text(selectedProjectData.name);
                    $('#selected-status').text('Active');

                    // Clear error
                    $('.error-msg[data-for="project_id"]').hide();
                } else {
                    $('.project-info').hide();
                    selectedProjectData = {};
                }
            });

            $(document).on('click', '.step-footer .btn.theme', function (e) {
                e.preventDefault();

                // Validate current step before proceeding
                if (currentStep === 1) {
                    // Validate project selection
                    if (!selectedProjectId) {
                        $('.error-msg[data-for="project_id"]').text('Please select a project').show();
                        return;
                    }
                }

                if (currentStep === 4) {
                    // Submit form
                    submitQuoteForm();
                    return;
                }

                if (currentStep < totalSteps) {
                    showStep(currentStep + 1);
                    if (currentStep === totalSteps) recalcAll();
                }
            });

            $(document).on('click', '.step-footer .btn.secondary', function (e) {
                e.preventDefault();
                if (currentStep > 1) showStep(currentStep - 1);
            });

            $(document).on('click', '.progress-step', function () {
                const targetStep = $(this).index() + 1;
                // Don't allow skipping to steps beyond current progress
                if (targetStep <= currentStep || (currentStep === 1 && selectedProjectId)) {
                    showStep(targetStep);
                }
            });

            // --- Quantity Controls (delegated) ---
            function bindQuantityControls($container) {
                $container.find('.plus').off('click.plus').on('click.plus', function (e) {
                    e.preventDefault();
                    const $row = $(this).closest('tr, .quantity-controls');
                    const $input = $row.find('.quantity-input').first();
                    let val = parseInt($input.val(), 10) || 0;
                    $input.val(val + 1).trigger('change');
                });

                $container.find('.minus').off('click.minus').on('click.minus', function (e) {
                    e.preventDefault();
                    const $row = $(this).closest('tr, .quantity-controls');
                    const $input = $row.find('.quantity-input').first();
                    let val = parseInt($input.val(), 10) || 0;
                    if (val > 0) $input.val(val - 1).trigger('change');
                });

                $container.find('.quantity-input').off('change.qty input.qty').on('change.qty input.qty', function () {
                    const $row = $(this).closest('tr');
                    recalcRow($row);
                    recalcAll();
                });
            }

            bindQuantityControls($(document));

            // --- Recalculation functions ---
            function recalcRow($row) {
                if (!$row || !$row.length) return 0;
                // skip add-rows
                if ($row.find('.add-box-btn, .add-margin-btn, .add-item-btn').length) return 0;

                // If this row is a manufacturer row (has unit-price-td class)
                if ($row.find('.unit-price-td').length || $row.find('.manufacturer-name-td').length) {
                    const $unitCell = $row.find('td').eq(1);
                    let unit = 0;
                    if ($unitCell.find('input').length) {
                        unit = parseNumber($unitCell.find('input').val());
                    } else {
                        unit = parseNumber($unitCell.text());
                    }
                    let qty = parseNumber($row.find('.quantity-input').first().val());
                    if (isNaN(qty)) qty = 0;
                    const lineTotal = (isNaN(unit) ? 0 : unit) * qty;
                    const $totalCell = $row.find('td').eq(3);
                    if ($totalCell.find('input').length) {
                        $totalCell.find('input').val(currency(lineTotal));
                    } else {
                        $totalCell.text(currency(lineTotal));
                    }
                    return lineTotal;
                }

                // Main left items with new structure: item | scope | qty | unit | total | actions
                // unit is in col 3 (index 3), total is in col 4 (index 4)
                const $unitCell = $row.find('td').eq(3);
                let unit = 0;
                if ($unitCell.find('input').length) {
                    unit = parseNumber($unitCell.find('input').val());
                } else {
                    unit = parseNumber($unitCell.text());
                }
                let qty = parseNumber($row.find('.quantity-input').first().val());
                if (isNaN(qty)) qty = 0;
                const lineTotal = unit * qty;
                const $totalCell = $row.find('td').eq(4);
                if ($totalCell.find('input').length) {
                    $totalCell.find('input').val(currency(lineTotal));
                } else {
                    $totalCell.text(currency(lineTotal));
                }
                return lineTotal;
            }

            function recalcManufacturerRow($row) {
                return recalcRow($row);
            }

            function recalcMarginRow($row) {
                if ($row.find('.add-box-btn, .add-margin-btn').length) return 0;
                // multiplier is column 1, result is column 2 for margin table
                let mul = parseNumber($row.find('td').eq(1).text() || $row.find('td').eq(1).find('input').val());
                const base = subtotalValue();
                const result = base * (isNaN(mul) ? 0 : mul);
                $row.find('td').eq(2).text(currency(result));
                return result;
            }

            function subtotalValue() {
                let subtotal = 0;

                // Left tables (main items) - new structure has 6 columns: item | scope | qty | unit | total | actions
                // Total is at index 4
                $('.quote-stepview__left .custom-table .table').each(function () {
                    $(this).find('tbody tr').each(function () {
                        const $r = $(this);
                        // skip add rows
                        if ($r.find('.add-btn, .add-box-btn, .add-margin-btn, .add-item-btn').length) return;
                        const $cells = $r.find('td');
                        if ($cells.length === 0) return;

                        // For main items table, total is at column index 4 (5th column)
                        if ($cells.length >= 5) {
                            const $totalCell = $cells.eq(4);
                            const text = $totalCell.find('input').length ? $totalCell.find('input').val() : $totalCell.text();
                            subtotal += parseNumber(text) || 0;
                        }
                    });
                });

                // Accordion (manufacturer and others)
                $('.quote-accordion__body .custom-table .table').each(function () {
                    $(this).find('tbody tr').each(function () {
                        const $r = $(this);
                        // skip add rows
                        if ($r.find('.add-btn, .add-box-btn, .add-margin-btn').length) return;
                        const $cells = $r.find('td');
                        if ($cells.length === 0) return;
                        // many accordion tables use column index 3 for totals
                        let text = '';
                        if ($cells.length >= 4) {
                            const $cell = $cells.eq(3);
                            text = $cell.find('input').length ? $cell.find('input').val() : $cell.text();
                        } else {
                            // fallback: last cell
                            const $last = $cells.last();
                            text = $last.find('input').length ? $last.find('input').val() : $last.text();
                        }
                        subtotal += parseNumber(text) || 0;
                    });
                });

                return subtotal;
            }

            function recalcTotals() {
                // recalc main items
                $('.quote-stepview__left .custom-table .table tbody tr').each(function () {
                    const $tr = $(this);
                    if (!$tr.find('.add-btn, .add-box-btn, .add-margin-btn').length) recalcRow($tr);
                });

                // recalc manufacturers
                $('.quote-accordion__body .custom-table .table tbody tr').each(function () {
                    const $tr = $(this);
                    if (!$tr.find('.add-btn, .add-box-btn, .add-margin-btn').length) recalcManufacturerRow($tr);
                });

                // recalc margins
                $('.quote-accordion__item').each(function () {
                    const headerText = $(this).find('.quote-accordion__header').text() || '';
                    if (headerText.toLowerCase().indexOf('margin') !== -1) {
                        $(this).find('.custom-table .table tbody tr').each(function () {
                            const $tr = $(this);
                            // skip add row
                            if ($tr.find('.add-btn, .add-box-btn, .add-margin-btn').length) return;
                            // skip rows that don't look like margin rows
                            if ($tr.find('td').length >= 3) recalcMarginRow($tr);
                        });
                    }
                });

                const subtotal = subtotalValue();
                const taxableSubtotal = calculateTaxableSubtotal();
                const tax = taxableSubtotal * TAX_RATE;
                const grand = subtotal + tax;
                $('#subtotal').text(currency(subtotal));
                $('#tax').text(currency(tax));
                $('#grand-total').text(currency(grand));
                $('.final-total-amount').text(currency(grand));
            }

            // Calculate subtotal only for taxable items
            function calculateTaxableSubtotal() {
                let taxableSubtotal = 0;

                // Left tables (main items) - only taxable ones
                // New structure: item | scope | qty | unit | total | actions
                $('.quote-stepview__left .custom-table .table tbody tr').each(function () {
                    const $r = $(this);
                    // skip add rows
                    if ($r.find('.add-btn, .add-box-btn, .add-margin-btn, .add-item-btn').length) return;

                    // check if row is taxable
                    const isTaxable = $r.attr('data-taxable') === '1';
                    if (!isTaxable) return;

                    const $cells = $r.find('td');
                    if ($cells.length === 0) return;

                    // Total is at column index 4 (5th column)
                    if ($cells.length >= 5) {
                        const $totalCell = $cells.eq(4);
                        const text = $totalCell.find('input').length ? $totalCell.find('input').val() : $totalCell.text();
                        taxableSubtotal += parseNumber(text) || 0;
                    }
                });

                // Accordion tables (manufacturers) - only taxable ones
                $('.quote-accordion__body .custom-table .table tbody tr').each(function () {
                    const $r = $(this);
                    // skip add rows
                    if ($r.find('.add-btn, .add-box-btn, .add-margin-btn').length) return;

                    // check if row is taxable
                    const isTaxable = $r.attr('data-taxable') === '1';
                    if (!isTaxable) return;

                    const $cells = $r.find('td');
                    if ($cells.length === 0) return;
                    let text = '';
                    if ($cells.length >= 4) {
                        const $cell = $cells.eq(3);
                        text = $cell.find('input').length ? $cell.find('input').val() : $cell.text();
                    } else {
                        const $last = $cells.last();
                        text = $last.find('input').length ? $last.find('input').val() : $last.text();
                    }
                    taxableSubtotal += parseNumber(text) || 0;
                });

                return taxableSubtotal;
            }

            function recalcAll() {
                recalcTotals();
            }

            $(document).on('change input', '.quantity-input, .form-input, .unit-price, .margin-mul, .add-box-unit, .add-box-qty, .add-margin-mul', function () {
                recalcAll();
            });

            recalcAll();

            // --- ADD ROWS ---

            // Live calculation for add-row line total (Quote Items)
            $(document).on('input change', '.add-item-unit, .add-item-qty', function () {
                const $addRow = $(this).closest('.add-row-item');
                const unitVal = parseNumber($addRow.find('.add-item-unit').val());
                const qtyVal = parseNumber($addRow.find('.add-item-qty').val());
                const lineTotal = (isNaN(unitVal) ? 0 : unitVal) * (isNaN(qtyVal) ? 0 : qtyVal);
                $addRow.find('.add-item-line-total-display').text(currency(lineTotal));
            });

            // Add new item button handler
            $(document).on('click', '.add-item-btn', function (e) {
                e.preventDefault();
                const $triggerRow = $(this).closest('tr');

                // read values from inputs
                const name = $.trim($triggerRow.find('.add-item-name').val() || '');
                const scope = $.trim($triggerRow.find('.add-item-scope').val() || '');
                const unitRaw = $triggerRow.find('.add-item-unit').val() || '';
                const qtyRaw = $triggerRow.find('.add-item-qty').val() || '1';

                // clear previous validation messages
                $triggerRow.find('.validation-msg').hide().text('');

                // validation
                if (name === '') {
                    $triggerRow.find('.validation-msg').eq(0).text('Please enter item name.').show();
                    return;
                }
                const unit = parseNumber(unitRaw);
                if (isNaN(unit) || unit <= 0) {
                    $triggerRow.find('.validation-msg').eq(3).text('Please enter a valid unit price (greater than 0).').show();
                    return;
                }
                let qty = parseInt(qtyRaw, 10);
                if (isNaN(qty) || qty < 0) {
                    $triggerRow.find('.validation-msg').eq(2).text('Please enter a valid quantity (0 or greater).').show();
                    return;
                }

                // build new row (default to non-taxable for manually added items)
                const lineTotal = unit * qty;
                const newRow = $(`
                    <tr data-taxable="0">
                        <td class="label item-name-td">${escapeHtml(name)}</td>
                        <td class="label scope-material-td">${escapeHtml(scope || '-')}</td>
                        <td class="label">
                            <div class="quantity-controls">
                                <button class="quantity-btn minus">−</button>
                                <input type="number" class="quantity-input" value="${qty}" min="0" />
                                <button class="quantity-btn plus">+</button>
                            </div>
                        </td>
                        <td class="label">${currency(unit)}</td>
                        <td class="label">${currency(lineTotal)}</td>
                        <td class="label actions-td">
                            <button class="action-btn edit edit-item">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="action-btn delete remove-row">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    `);

                // insert BEFORE the trigger row (so add row remains at bottom)
                $triggerRow.before(newRow);

                // clear the add-row inputs for next entry
                $triggerRow.find('.add-item-name').val('');
                $triggerRow.find('.add-item-scope').val('');
                $triggerRow.find('.add-item-unit').val('');
                $triggerRow.find('.add-item-qty').val('1');
                $triggerRow.find('.add-item-line-total-display').text('$0.00');

                // bind quantity handlers
                bindQuantityControls(newRow);
                recalcAll();

                // Show success message
                showToast('success', 'Item added successfully!');
            });

            // Live calculation for add-row line total (Box Manufacturer)
            $(document).on('input change', '.add-box-unit, .add-box-qty', function () {
                const $addRow = $(this).closest('.add-row-box');
                const unitVal = parseNumber($addRow.find('.add-box-unit').val());
                const qtyVal = parseNumber($addRow.find('.add-box-qty').val());
                const lineTotal = (isNaN(unitVal) ? 0 : unitVal) * (isNaN(qtyVal) ? 0 : qtyVal);
                $addRow.find('.add-box-line-total-display').text(currency(lineTotal));
            });

            $(document).on('click', '.add-box-btn', function (e) {
                e.preventDefault();
                const $triggerRow = $(this).closest('tr');

                // read name / unit / qty from inputs in trigger row
                const name = $.trim($triggerRow.find('.add-box-name').val() || '');
                const unitRaw = $triggerRow.find('.add-box-unit').val() || '';
                const qtyRaw = $triggerRow.find('.add-box-qty').val() || '0';

                // clear previous validation messages
                $triggerRow.find('.validation-msg').hide().text('');

                // validation
                if (name === '') {
                    $triggerRow.find('.validation-msg').eq(0).text('Please enter manufacturer name.').show();
                    return;
                }
                const unit = parseNumber(unitRaw);
                if (isNaN(unit) || unit <= 0) {
                    $triggerRow.find('.validation-msg').eq(1).text('Please enter a valid unit price (greater than 0).').show();
                    return;
                }
                let qty = parseInt(qtyRaw, 10);
                if (isNaN(qty) || qty < 0) {
                    $triggerRow.find('.validation-msg').eq(2).text('Please enter a valid quantity (0 or greater).').show();
                    return;
                }

                // build new row (default to non-taxable for manually added items)
                const lineTotal = unit * qty;
                const newRow = $(`
                    <tr data-taxable="0">
                        <td class="label manufacturer-name-td">${escapeHtml(name)}</td>
                        <td class="label unit-price-td">${unit.toFixed(2)}</td>
                        <td class="label">
                            <div class="quantity-controls">
                                <button class="quantity-btn minus">−</button>
                                <input type="number" class="quantity-input" value="${qty}" min="0" />
                                <button class="quantity-btn plus">+</button>
                            </div>
                        </td>
                        <td class="label line-total">${currency(lineTotal)}</td>
                        <td class="label actions-td">
                            <button class="action-btn edit edit-box">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="action-btn delete remove-row">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    `);

                // insert BEFORE the trigger row (so add row remains at bottom)
                $triggerRow.before(newRow);

                // clear the add-row inputs for next entry
                $triggerRow.find('.add-box-name').val('');
                $triggerRow.find('.add-box-unit').val('');
                $triggerRow.find('.add-box-qty').val('1');
                $triggerRow.find('.add-box-line-total-display').text('$0.00');

                // bind quantity handlers
                bindQuantityControls(newRow);
                recalcAll();

                // Show success message
                showToast('success', 'Box manufacturer added successfully!');
            });

            // Live calculation for add-row margin result
            $(document).on('input change', '.add-margin-mul', function () {
                const $addRow = $(this).closest('.add-row-margin');
                const mulVal = parseNumber($addRow.find('.add-margin-mul').val());
                const base = subtotalValue();
                const resultValue = base * (isNaN(mulVal) ? 0 : mulVal);
                $addRow.find('.add-margin-result').text(currency(resultValue));
            });

            $(document).on('click', '.add-margin-btn', function (e) {
                e.preventDefault();
                const $triggerRow = $(this).closest('tr');
                $triggerRow.find('.validation-msg').hide().text('');

                const desc = $.trim($triggerRow.find('.add-margin-desc').val() || '');
                const mulRaw = $triggerRow.find('.add-margin-mul').val() || '';
                const mul = parseNumber(mulRaw);

                // validation
                if (desc === '') {
                    $triggerRow.find('.validation-msg').eq(0).text('Please enter margin description.').show();
                    return;
                }
                if (isNaN(mul) || mul <= 0) {
                    $triggerRow.find('.validation-msg').eq(1).text('Please enter a valid multiplier (e.g. 1.15, must be greater than 0).').show();
                    return;
                }

                // compute immediate result
                const base = subtotalValue();
                const resultValue = base * mul;

                const newRow = $(`
                    <tr data-taxable="0">
                        <td class="label margin-desc-td">${escapeHtml(desc)}</td>
                        <td class="label margin-mul-td">${mul.toFixed(2)}</td>
                        <td class="label margin-result">${currency(resultValue)}</td>
                        <td class="label actions-td">
                            <button class="action-btn edit edit-margin">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="action-btn delete remove-row">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    `);

                // insert before add-row
                $triggerRow.before(newRow);

                // clear the add-row inputs for next entry
                $triggerRow.find('.add-margin-desc').val('');
                $triggerRow.find('.add-margin-mul').val('');
                $triggerRow.find('.add-margin-result').text('$0.00');

                recalcAll();

                // Show success message
                showToast('success', 'Margin markup added successfully!');
            });

            // --- EDIT FUNCTIONALITY ---

            // Enter edit mode for item row
            function enterEditItem($row) {
                if ($row.data('editing')) return;
                $row.data('editing', true);

                const $nameCell = $row.find('td').eq(0);
                const $scopeCell = $row.find('td').eq(1);
                const $qtyCell = $row.find('td').eq(2);
                const $unitCell = $row.find('td').eq(3);

                const orig = {
                    name: $nameCell.text().trim(),
                    scope: $scopeCell.text().trim(),
                    qty: $qtyCell.find('.quantity-input').val() || '0',
                    unit: $unitCell.text().trim()
                };
                $row.data('orig', orig);

                $nameCell.html(`<input type="text" class="form-input inline-name" value="${escapeHtml(orig.name)}"><div class="validation-msg small text-danger" style="display:none;"></div>`);
                $scopeCell.html(`<input type="text" class="form-input inline-scope" value="${escapeHtml(orig.scope === '-' ? '' : orig.scope)}"><div class="validation-msg small text-danger" style="display:none;"></div>`);
                $unitCell.html(`<input type="number" class="form-input inline-unit" value="${escapeHtml(orig.unit.replace('$', '').replace(',', ''))}" step="0.01" min="0"><div class="validation-msg small text-danger" style="display:none;"></div>`);

                // qty input already exists — add inline validation area if missing
                if ($qtyCell.find('.validation-msg').length === 0) {
                    $qtyCell.append('<div class="validation-msg small text-danger" style="display:none;"></div>');
                }

                // actions: hide edit/delete and show save/cancel
                const $actions = $row.find('.actions-td');
                $actions.find('.edit-item').hide();
                $actions.find('.remove-row').hide();
                $actions.prepend('<button class="action-btn edit save-inline-item"><i class="fa fa-save"></i></button><button class="action-btn delete cancel-inline-item"><i class="fa fa-window-close"></i></button>');

                // live update and validation
                $row.find('.inline-unit').on('input', function () {
                    const val = $(this).val();
                    const num = parseNumber(val);
                    if (isNaN(num) || num <= 0) {
                        $unitCell.find('.validation-msg').text('Unit price must be greater than 0').show();
                    } else {
                        $unitCell.find('.validation-msg').hide().text('');
                    }
                    recalcRow($row);
                    recalcAll();
                });

                $row.find('.quantity-input').on('input', function () {
                    const val = $(this).val();
                    const num = parseNumber(val);
                    if (val === '' || isNaN(num) || num < 0) {
                        $qtyCell.find('.validation-msg').text('Quantity must be 0 or greater').show();
                    } else {
                        $qtyCell.find('.validation-msg').hide().text('');
                    }
                    recalcRow($row);
                    recalcAll();
                });
            }

            function saveEditItem($row) {
                const $nameCell = $row.find('td').eq(0);
                const $scopeCell = $row.find('td').eq(1);
                const $qtyCell = $row.find('td').eq(2);
                const $unitCell = $row.find('td').eq(3);

                const name = $nameCell.find('.inline-name').val().trim();
                const scope = $scopeCell.find('.inline-scope').val().trim();
                const unitRaw = $unitCell.find('.inline-unit').val().trim();
                const qtyRaw = $qtyCell.find('.quantity-input').val().trim();

                $nameCell.find('.validation-msg').hide().text('');
                $scopeCell.find('.validation-msg').hide().text('');
                $unitCell.find('.validation-msg').hide().text('');
                $qtyCell.find('.validation-msg').hide().text('');

                let hasError = false;
                if (name === '') {
                    $nameCell.find('.validation-msg').text('Item name is required').show();
                    hasError = true;
                }
                const unit = parseNumber(unitRaw);
                if (isNaN(unit) || unit <= 0) {
                    $unitCell.find('.validation-msg').text('Unit price must be greater than 0').show();
                    hasError = true;
                }
                const qty = parseNumber(qtyRaw);
                if (qtyRaw === '' || isNaN(qty) || qty < 0) {
                    $qtyCell.find('.validation-msg').text('Quantity must be 0 or greater').show();
                    hasError = true;
                }

                if (hasError) return false;

                // persist values
                $nameCell.text(name);
                $scopeCell.text(scope || '-');
                $unitCell.text(currency(unit));
                $qtyCell.html(`<div class="quantity-controls"><button class="quantity-btn minus">−</button><input type="number" class="quantity-input" value="${parseInt(qtyRaw, 10)}" min="0"/><button class="quantity-btn plus">+</button></div>`);

                // restore actions
                $row.find('.save-inline-item, .cancel-inline-item').remove();
                $row.find('.edit-item').show();
                $row.find('.remove-row').show();

                bindQuantityControls($row);
                recalcAll();
                $row.data('editing', false);

                showToast('success', 'Item updated successfully!');
                return true;
            }

            function cancelEditItem($row) {
                const orig = $row.data('orig') || {};
                $row.find('td').eq(0).text(orig.name || '');
                $row.find('td').eq(1).text(orig.scope || '-');
                $row.find('td').eq(2).html(`<div class="quantity-controls"><button class="quantity-btn minus">−</button><input type="number" class="quantity-input" value="${orig.qty || 0}" min="0"/><button class="quantity-btn plus">+</button></div>`);
                $row.find('td').eq(3).text(orig.unit || '');
                $row.find('.save-inline-item, .cancel-inline-item').remove();
                $row.find('.edit-item').show();
                $row.find('.remove-row').show();
                bindQuantityControls($row);
                recalcAll();
                $row.data('editing', false);
            }

            // Enter edit mode for manufacturer row
            function enterEditManufacturer($row) {
                if ($row.data('editing')) return;
                $row.data('editing', true);

                const $nameCell = $row.find('td').eq(0);
                const $unitCell = $row.find('td').eq(1);
                const $qtyCell = $row.find('td').eq(2);
                const orig = {
                    name: $nameCell.text().trim(),
                    unit: $unitCell.text().trim(),
                    qty: $qtyCell.find('.quantity-input').val() || $qtyCell.find('input').val() || '0'
                };
                $row.data('orig', orig);

                $nameCell.html(`<input type="text" class="form-input inline-name" value="${escapeHtml(orig.name)}"><div class="validation-msg small text-danger" style="display:none;"></div>`);
                $unitCell.html(`<input type="number" class="form-input inline-unit" value="${escapeHtml(orig.unit)}" step="0.01" min="0"><div class="validation-msg small text-danger" style="display:none;"></div>`);

                // qty input already exists — add inline validation area if missing
                if ($qtyCell.find('.validation-msg').length === 0) {
                    $qtyCell.append('<div class="validation-msg small text-danger" style="display:none;"></div>');
                }

                // actions: hide edit/delete and show save/cancel
                const $actions = $row.find('.actions-td');
                $actions.find('.edit-box').hide();
                $actions.find('.remove-row').hide();
                $actions.prepend('<button class="action-btn edit save-inline"><i class="fa fa-save"></i></button><button class="action-btn delete cancel-inline"><i class="fa fa-window-close"></i></button>');

                // live update and validation
                $row.find('.inline-unit').on('input', function () {
                    const val = $(this).val();
                    const num = parseNumber(val);
                    if (isNaN(num) || num <= 0) {
                        $unitCell.find('.validation-msg').text('Unit price must be greater than 0').show();
                    } else {
                        $unitCell.find('.validation-msg').hide().text('');
                    }
                    recalcRow($row);
                    recalcAll();
                });

                $row.find('.quantity-input').on('input', function () {
                    const val = $(this).val();
                    const num = parseNumber(val);
                    if (val === '' || isNaN(num) || num < 0) {
                        $qtyCell.find('.validation-msg').text('Quantity must be 0 or greater').show();
                    } else {
                        $qtyCell.find('.validation-msg').hide().text('');
                    }
                    recalcRow($row);
                    recalcAll();
                });
            }

            function saveEditManufacturer($row) {
                const $nameCell = $row.find('td').eq(0);
                const $unitCell = $row.find('td').eq(1);
                const $qtyCell = $row.find('td').eq(2);

                const name = $nameCell.find('.inline-name').val().trim();
                const unitRaw = $unitCell.find('.inline-unit').val().trim();
                const qtyRaw = $qtyCell.find('.quantity-input').val().trim();

                $nameCell.find('.validation-msg').hide().text('');
                $unitCell.find('.validation-msg').hide().text('');
                $qtyCell.find('.validation-msg').hide().text('');

                let hasError = false;
                if (name === '') {
                    $nameCell.find('.validation-msg').text('Manufacturer name is required').show();
                    hasError = true;
                }
                const unit = parseNumber(unitRaw);
                if (isNaN(unit) || unit <= 0) {
                    $unitCell.find('.validation-msg').text('Unit price must be greater than 0').show();
                    hasError = true;
                }
                const qty = parseNumber(qtyRaw);
                if (qtyRaw === '' || isNaN(qty) || qty < 0) {
                    $qtyCell.find('.validation-msg').text('Quantity must be 0 or greater').show();
                    hasError = true;
                }

                if (hasError) return false;

                // persist values
                $nameCell.text(name);
                $unitCell.text(unit.toFixed(2));
                $qtyCell.html(`<div class="quantity-controls"><button class="quantity-btn minus">−</button><input type="number" class="quantity-input" value="${parseInt(qtyRaw, 10)}" min="0"/><button class="quantity-btn plus">+</button></div>`);

                // restore actions
                $row.find('.save-inline, .cancel-inline').remove();
                $row.find('.edit-box').show();
                $row.find('.remove-row').show();

                bindQuantityControls($row);
                recalcAll();
                $row.data('editing', false);

                showToast('success', 'Changes saved successfully!');
                return true;
            }

            function cancelEditManufacturer($row) {
                const orig = $row.data('orig') || {};
                $row.find('td').eq(0).text(orig.name || '');
                $row.find('td').eq(1).text(orig.unit || '');
                $row.find('td').eq(2).html(`<div class="quantity-controls"><button class="quantity-btn minus">−</button><input type="number" class="quantity-input" value="${orig.qty || 0}" min="0"/><button class="quantity-btn plus">+</button></div>`);
                $row.find('.save-inline, .cancel-inline').remove();
                $row.find('.edit-box').show();
                $row.find('.remove-row').show();
                bindQuantityControls($row);
                recalcAll();
                $row.data('editing', false);
            }

            // Margin edit
            function enterEditMargin($row) {
                if ($row.data('editing')) return;
                $row.data('editing', true);
                const $desc = $row.find('td').eq(0);
                const $mul = $row.find('td').eq(1);
                const orig = { desc: $desc.text().trim(), mul: $mul.text().trim() };
                $row.data('orig', orig);
                $desc.html(`<input type="text" class="form-input inline-desc" value="${escapeHtml(orig.desc)}"><div class="validation-msg small text-danger" style="display:none;"></div>`);
                $mul.html(`<input type="number" class="form-input inline-mul" value="${escapeHtml(orig.mul)}" step="0.01" min="0"><div class="validation-msg small text-danger" style="display:none;"></div>`);
                const $actions = $row.find('.actions-td');
                $actions.find('.edit-margin').hide();
                $actions.find('.remove-row').hide();
                $actions.prepend('<button class="action-btn edit save-inline-margin"><i class="fa fa-save"></i></button><button class="action-btn delete cancel-inline-margin"><i class="fa fa-window-close"></i></button>');

                $row.find('.inline-mul').on('input', function () {
                    const mul = parseNumber($(this).val());
                    if (isNaN(mul) || mul <= 0) {
                        $mul.find('.validation-msg').text('Multiplier must be greater than 0').show();
                    } else {
                        $mul.find('.validation-msg').hide().text('');
                    }
                    const base = subtotalValue();
                    $row.find('td').eq(2).text(currency(base * (isNaN(mul) ? 0 : mul)));
                });
            }

            function saveEditMargin($row) {
                const $desc = $row.find('td').eq(0);
                const $mul = $row.find('td').eq(1);
                const desc = $desc.find('.inline-desc').val().trim();
                const mulRaw = $mul.find('.inline-mul').val().trim();
                $desc.find('.validation-msg').hide().text('');
                $mul.find('.validation-msg').hide().text('');
                let hasError = false;
                if (desc === '') {
                    $desc.find('.validation-msg').text('Description is required').show();
                    hasError = true;
                }
                const mul = parseNumber(mulRaw);
                if (isNaN(mul) || mul <= 0) {
                    $mul.find('.validation-msg').text('Multiplier must be greater than 0').show();
                    hasError = true;
                }
                if (hasError) return false;

                $desc.text(desc);
                $mul.text(mul.toFixed(2));
                const base = subtotalValue();
                $row.find('td').eq(2).text(currency(base * mul));

                $row.find('.save-inline-margin, .cancel-inline-margin').remove();
                $row.find('.edit-margin').show();
                $row.find('.remove-row').show();
                recalcAll();
                $row.data('editing', false);

                showToast('success', 'Margin updated successfully!');
                return true;
            }

            function cancelEditMargin($row) {
                const orig = $row.data('orig') || {};
                $row.find('td').eq(0).text(orig.desc || '');
                $row.find('td').eq(1).text(orig.mul || '');
                const mul = parseNumber(orig.mul || 0);
                $row.find('td').eq(2).text(currency(subtotalValue() * (isNaN(mul) ? 0 : mul)));
                $row.find('.save-inline-margin, .cancel-inline-margin').remove();
                $row.find('.edit-margin').show();
                $row.find('.remove-row').show();
                recalcAll();
                $row.data('editing', false);
            }

            // delegated event handlers
            $(document).on('click', '.edit-item', function (e) {
                e.preventDefault();
                enterEditItem($(this).closest('tr'));
            });

            $(document).on('click', '.save-inline-item', function (e) {
                e.preventDefault();
                saveEditItem($(this).closest('tr'));
            });

            $(document).on('click', '.cancel-inline-item', function (e) {
                e.preventDefault();
                cancelEditItem($(this).closest('tr'));
            });

            $(document).on('click', '.edit-box', function (e) {
                e.preventDefault();
                enterEditManufacturer($(this).closest('tr'));
            });

            $(document).on('click', '.save-inline', function (e) {
                e.preventDefault();
                saveEditManufacturer($(this).closest('tr'));
            });

            $(document).on('click', '.cancel-inline', function (e) {
                e.preventDefault();
                cancelEditManufacturer($(this).closest('tr'));
            });

            $(document).on('click', '.edit-margin', function (e) {
                e.preventDefault();
                enterEditMargin($(this).closest('tr'));
            });

            $(document).on('click', '.save-inline-margin', function (e) {
                e.preventDefault();
                saveEditMargin($(this).closest('tr'));
            });

            $(document).on('click', '.cancel-inline-margin', function (e) {
                e.preventDefault();
                cancelEditMargin($(this).closest('tr'));
            });

            // remove row with better confirmation
            $(document).on('click', '.remove-row', function (e) {
                e.preventDefault();
                const $row = $(this).closest('tr');
                const itemName = $row.find('td').first().text().trim() || 'this item';

                if (!confirm(`Are you sure you want to delete "${itemName}"?\n\nThis action cannot be undone.`)) return;

                $row.fadeOut(300, function () {
                    $(this).remove();
                    recalcAll();
                    showToast('info', 'Item deleted successfully.');
                });
            });

            // inline save on Enter, cancel on Esc
            $(document).on('keydown', '.inline-name, .inline-scope, .inline-unit, .inline-desc, .inline-mul', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const $row = $(this).closest('tr');
                    if ($row.find('.save-inline-item').length) {
                        $row.find('.save-inline-item').trigger('click');
                    } else if ($row.find('.save-inline').length) {
                        $row.find('.save-inline').trigger('click');
                    } else if ($row.find('.save-inline-margin').length) {
                        $row.find('.save-inline-margin').trigger('click');
                    }
                } else if (e.key === 'Escape') {
                    e.preventDefault();
                    const $row = $(this).closest('tr');
                    if ($row.find('.cancel-inline-item').length) {
                        $row.find('.cancel-inline-item').trigger('click');
                    }
                    if ($row.find('.cancel-inline').length) {
                        $row.find('.cancel-inline').trigger('click');
                    }
                    if ($row.find('.cancel-inline-margin').length) {
                        $row.find('.cancel-inline-margin').trigger('click');
                    }
                }
            });

            // accordion toggle
            $(document).on('click', '.quote-accordion__header', function () {
                const body = $(this).next('.quote-accordion__body');
                $('.quote-accordion__body').not(body).slideUp();
                $('.quote-accordion__header').not(this).removeClass('active');
                $(this).toggleClass('active');
                body.stop(true, true).slideToggle();
            });

            // final recalc on step change
            $(document).on('click', '.step-footer .btn.theme', function () {
                if (currentStep + 1 === totalSteps) {
                    recalcAll();
                }
            });

            // ensure initial calculation
            recalcAll();

            // --- Populate Review Step ---
            function populateReviewStep() {
                // Project info
                $('#review-project-name').text(selectedProjectData.name || '-');
                $('#review-customer-name').text(selectedProjectData.customer || '-');

                // Quote items from step 2
                let itemsHtml = '';
                $('.quote-stepview__left .custom-table .table tbody tr').each(function () {
                    const $row = $(this);
                    if ($row.find('.add-btn, .add-box-btn, .add-margin-btn, .add-item-btn').length) return;

                    const itemName = $row.find('td').eq(0).text().trim();
                    const scopeMaterial = $row.find('td').eq(1).text().trim();
                    const qty = $row.find('.quantity-input').val() || 0;
                    const unitPrice = $row.find('td').eq(3).text().trim();
                    const total = $row.find('td').eq(4).text().trim(); // ✅ Fixed: index 4 = Total column

                    if (parseInt(qty) > 0) {
                        const scopeText = scopeMaterial && scopeMaterial !== '-' ? ` (${escapeHtml(scopeMaterial)})` : '';
                        itemsHtml += `<div class="summary-item">
                                <div class="item-description">${escapeHtml(itemName)}${scopeText} × ${qty}</div>
                                <div class="item-price">${total}</div>
                            </div>`;
                    }
                });
                $('#review-items-list').html(itemsHtml || '<p style="color: #999;">No items added</p>');

                // Box manufacturers
                let manufacturersHtml = '';
                $('#box-manufacturer-table tbody tr').each(function () {
                    const $row = $(this);
                    if ($row.find('.add-box-btn').length) return;

                    const name = $row.find('.manufacturer-name-td').text().trim();
                    const qty = $row.find('.quantity-input').val() || 0;
                    const unitPrice = $row.find('.unit-price-td').text().trim();
                    const total = $row.find('.line-total').text().trim();

                    if (name) {
                        manufacturersHtml += `<div class="summary-item">
                                <div class="item-description">${escapeHtml(name)} × ${qty} @ $${unitPrice}</div>
                                <div class="item-price">${total}</div>
                            </div>`;
                    }
                });
                $('#review-manufacturers-list').html(manufacturersHtml || '<p style="color: #999;">No manufacturers added</p>');

                // Margins
                let marginsHtml = '';
                $('#margin-markup-table tbody tr').each(function () {
                    const $row = $(this);
                    if ($row.find('.add-margin-btn').length) return;

                    const desc = $row.find('.margin-desc-td').text().trim();
                    const multiplier = $row.find('.margin-mul-td').text().trim();
                    const result = $row.find('.margin-result').text().trim();

                    if (desc) {
                        marginsHtml += `<div class="summary-item">
                                <div class="item-description">${escapeHtml(desc)} (${multiplier}x)</div>
                                <div class="item-price">${result}</div>
                            </div>`;
                    }
                });
                $('#review-margins-list').html(marginsHtml || '<p style="color: #999;">No margins added</p>');

                // Totals
                const subtotal = $('#subtotal').text();
                const tax = $('#tax').text();
                const grandTotal = $('#grand-total').text();

                $('#review-subtotal').text(subtotal);
                $('#review-tax').text(tax);
                $('#review-total').text(grandTotal);
                $('#review-grand-total').text(grandTotal);
            }

            // --- Submit Quote Form via AJAX ---
            function submitQuoteForm() {
                // Collect all form data
                const formData = {
                    _token: '{{ csrf_token() }}',
                    quote_type: '{{ $quoteType ?? "kitchen" }}', // Dynamic quote type
                    project_id: selectedProjectId,
                    customer_name: selectedProjectData.customer,
                    project_name: selectedProjectData.name,
                    subtotal: parseNumber($('#subtotal').text()),
                    tax: parseNumber($('#tax').text()),
                    total: parseNumber($('#grand-total').text()),
                    discount: 0,
                    items: [],
                    manufacturers: [],
                    margins: []
                };

                // Collect quote items
                $('.quote-stepview__left .custom-table .table tbody tr').each(function () {
                    const $row = $(this);
                    if ($row.find('.add-btn, .add-box-btn, .add-margin-btn, .add-item-btn').length) return;

                    const itemName = $row.find('td').eq(0).text().trim();
                    const scopeMaterial = $row.find('td').eq(1).text().trim();
                    const qty = parseNumber($row.find('.quantity-input').val());
                    const unitPrice = parseNumber($row.find('td').eq(3).text());
                    const total = parseNumber($row.find('td').eq(4).text()); // ✅ Fixed: index 4 = Total column
                    const isTaxable = $row.attr('data-taxable') === '1' ? 1 : 0;

                    if (qty > 0) {
                        formData.items.push({
                            name: itemName,
                            scope_material: scopeMaterial && scopeMaterial !== '-' ? scopeMaterial : null,
                            qty: qty,
                            unit_price: unitPrice,
                            line_total: total,
                            is_taxable: isTaxable
                        });
                    }
                });

                // Collect manufacturers
                $('#box-manufacturer-table tbody tr').each(function () {
                    const $row = $(this);
                    if ($row.find('.add-box-btn').length) return;

                    const name = $row.find('.manufacturer-name-td').text().trim();
                    const qty = parseNumber($row.find('.quantity-input').val());
                    const unitPrice = parseNumber($row.find('.unit-price-td').text());
                    const total = parseNumber($row.find('.line-total').text());
                    const isTaxable = $row.attr('data-taxable') === '1' ? 1 : 0;

                    if (name) {
                        formData.manufacturers.push({
                            name: name,
                            qty: qty,
                            unit_price: unitPrice,
                            line_total: total,
                            is_taxable: isTaxable
                        });
                    }
                });

                // Collect margins
                $('#margin-markup-table tbody tr').each(function () {
                    const $row = $(this);
                    if ($row.find('.add-margin-btn').length) return;

                    const desc = $row.find('.margin-desc-td').text().trim();
                    const multiplier = parseNumber($row.find('.margin-mul-td').text());
                    const result = parseNumber($row.find('.margin-result').text());
                    const isTaxable = $row.attr('data-taxable') === '1' ? 1 : 0;

                    if (desc) {
                        formData.margins.push({
                            description: desc,
                            multiplier: multiplier,
                            result: result,
                            is_taxable: isTaxable
                        });
                    }
                });

                // Show loading state
                const $submitBtn = $('.step-footer .btn.theme');
                const originalText = $submitBtn.html();
                $submitBtn.prop('disabled', true).html('<span style="display: inline-block; width: 20px; height: 20px; border: 2px solid #fff; border-top-color: transparent; border-radius: 50%; animation: spin 0.6s linear infinite;"></span> Creating Quote...');

                // Submit via AJAX
                $.ajax({
                    url: '{{ route("admin.quotes.store") }}',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            // Show success step
                            $('#success-quote-number').text(response.quote_number || '-');
                            $('.quote-stepview').hide();
                            $('.success-step').show();
                            $('.step-footer').hide();
                            
                            // Hide all step indicators and headers
                            $('.quote-steps').hide();
                            $('.breadcrumb').hide();
                            $('.content-header .title').hide();
                            $('.content-header .subtitle').hide();
                            
                            // Set button URLs based on quote type
                            const quoteType = '{{ $quoteType ?? "kitchen" }}';
                            if (quoteType === 'kitchen') {
                                $('#view-all-quotes-btn').attr('href', '{{ route("admin.kitchen.quotes.index") }}');
                                $('#create-another-quote-btn').attr('href', '{{ route("admin.kitchen.quotes.create") }}');
                            } else if (quoteType === 'vanity') {
                                $('#view-all-quotes-btn').attr('href', '{{ route("admin.vanity.quotes.index") }}');
                                $('#create-another-quote-btn').attr('href', '{{ route("admin.vanity.quotes.create") }}');
                            }

                            showToast('success', 'Quote created successfully!');
                        } else {
                            showToast('error', response.message || 'Failed to create quote');
                            $submitBtn.prop('disabled', false).html(originalText);
                        }
                    },
                    error: function (xhr) {
                        let errorMsg = 'An error occurred while creating the quote.';

                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON?.errors || {};
                            const errorMessages = [];
                            $.each(errors, function (field, messages) {
                                errorMessages.push(messages.join(' '));
                            });
                            errorMsg = errorMessages.join('<br>') || errorMsg;
                        } else if (xhr.responseJSON?.message) {
                            errorMsg = xhr.responseJSON.message;
                        }

                        showToast('error', errorMsg);
                        $submitBtn.prop('disabled', false).html(originalText);

                        console.error('Quote submission error:', xhr);
                    }
                });
            }

        }); // end jQuery ready
    </script>

    <!-- <style>
            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }

            .summary-section {
                margin-bottom: 20px;
            }

            .summary-section h4 {
                margin-bottom: 10px;
                color: #333;
                font-size: 16px;
                font-weight: 600;
            }

            .summary-section p {
                margin: 5px 0;
                color: #666;
            }

            .summary-item {
                display: flex;
                justify-content: space-between;
                padding: 8px 0;
                border-bottom: 1px solid #f0f0f0;
            }

            .summary-item.total-row {
                font-weight: bold;
                border-top: 2px solid #333;
                border-bottom: 2px solid #333;
                padding: 12px 0;
            }

            .item-description {
                flex: 1;
                color: #333;
            }

            .item-price {
                font-weight: 600;
                color: #333;
            }

            .summary-divider {
                height: 1px;
                background: #e0e0e0;
                margin: 20px 0;
            }
        </style> -->
@endpush