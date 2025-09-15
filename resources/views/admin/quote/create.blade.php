@extends('layouts.admin')

@section('title','Quote — Multi Step')

@push('css')
<!-- Combined CSS (kept your styles, simplified duplicates) -->
<style>
  .app-wrapper {
    max-width: 1200px;
    margin: 0 auto
  }

  .card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, .06);
    overflow: hidden
  }

  .centered {
    max-width: 700px;
    margin: 32px auto;
    padding: 40px;
    text-align: center
  }

  /* Welcome */
  .welcome-container {
    max-width: 700px;
    margin: 24px auto
  }

  .icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgb(22, 163, 74);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px
  }

  .icon::before {
    content: "🔨";
    font-size: 40px;
    color: #fff
  }

  .title {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 8px
  }

  .brand {
    color: rgb(22, 163, 74)
  }

  .description {
    color: #666;
    margin-bottom: 20px
  }

  .quote-options {
    padding: 16px;
    border-radius: 8px;
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    margin-bottom: 20px
  }

  .checkbox-group {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap
  }

  .checkbox-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    background: #fff;
    cursor: pointer
  }

  .checkbox-item.selected {
    border-color: rgb(22, 163, 74);
    background: #e8f5e8
  }

  .btn {
    background: rgb(22, 163, 74);
    color: #fff;
    padding: 12px 20px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-weight: 600
  }

  .btn.secondary {
    background: #fff;
    color: #333;
    border: 1px solid #ccc
  }

  .time-estimate {
    color: #999;
    margin-top: 8px
  }

  .error-message {
    color: #d32f2f;
    display: none;
    margin-top: 12px
  }

  /* Steps layout */
  .step-container {
    max-width: 1200px;
    margin: 20px auto
  }

  .container {
    background: #fff;
    border-radius: 8px;
    overflow: hidden
  }

  .header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #e0e0e0
  }

  .table-container {
    padding: 20px;
    overflow: auto
  }

  table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px
  }

  thead th {
    padding: 12px 8px;
    border-bottom: 1px solid #e0e0e0;
    text-align: left
  }

  td {
    padding: 8px;
    border-bottom: 1px solid #e0e0e0;
    vertical-align: top
  }

  .qty-input {
    width: 100%;
    padding: 6px;
    border: 1px solid #ccc;
    border-radius: 4px;
    text-align: center;
    background: #e6f3ff
  }

  .qty-input.num-fill {
    background: #e8f5e8
  }

  .qty-input.yes-no {
    background: #fff3cd
  }

  .alpha-fill {
    background: #ffebee
  }

  .cost-value {
    color: #333;
    font-weight: 500;
    text-align: right
  }

  .empty-value {
    color: #999;
    text-align: right
  }

  .taxed-t {
    color: #2e7d32;
    font-weight: 700;
    text-align: center
  }

  .project-col {
    width: 25%
  }

  .scope-col {
    width: 25%
  }

  .qty-col {
    width: 12%
  }

  .cost-col {
    width: 12%
  }

  .total-col {
    width: 12%
  }

  .taxed-col {
    width: 6%
  }

  .nav-footer {
    padding: 20px;
    border-top: 1px solid #e0e0e0;
    background: #f8f9fa;
    display: flex;
    justify-content: space-between;
    align-items: center
  }

  .steps-indicator {
    font-size: 14px;
    color: #666
  }

  .hidden {
    display: none
  }

  .summary-box {
    padding: 20px
  }

  .summary-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px dashed #eee
  }

  .summary-row.total {
    font-weight: 700
  }

  @media (max-width:768px) {
    .header-row {
      flex-direction: column;
      align-items: flex-start
    }

    .project-col,
    .scope-col {
      width: 35%
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
      <input type="text" placeholder="Search projects, customers, status...">
    </div>

    <div class="header-actions">
      <a href="#export" class="header-btn secondary" role="button"><i>📤</i> Export</a>
      <a href="{{ route('admin.projects.create') }}" class="header-btn primary" role="button"><i>➕</i> New Project</a>
      <div class="user-avatar">BM</div>
    </div>
  </div>

  <!-- Content -->
  <div class="content bg-content">
    <div class="welcome-container card" id="welcome-panel">
      <div class="quote-type">
        <div class="icon"></div>
        <h1 class="title">Welcome to <br><span class="brand">The Stone Cobblers</span></h1>
        <p class="description">Transform your outdoor space with our premium stone cobbling services. Let's gather some details to provide you with a personalized quote.</p>
        <div class="quote-options">
          <h3 style="margin:0 0 12px 0">Select Quote Type</h3>
          <div class="checkbox-group" id="quote-types">
            <div class="checkbox-item" id="chk-kitchen" data-value="kitchen" onclick="toggleCheckbox('kitchen')">
              <input type="checkbox" id="kitchen-quote" name="quote-type" value="kitchen">
              <label for="kitchen-quote">Kitchen Quote</label>
            </div>
            <div class="checkbox-item" id="chk-vanity" data-value="vanity" onclick="toggleCheckbox('vanity')">
              <input type="checkbox" id="vanity-quote" name="quote-type" value="vanity" disabled>
              <label for="vanity-quote">Vanity Quote</label>
            </div>
          </div>
        </div>

        <button class="btn" id="begin-btn" onclick="beginQuote()">Let's Begin <span style="margin-left:8px">→</span></button>
        <div class="time-estimate">Takes about 3 minutes to complete</div>
        <div class="error-message" id="welcome-error">Please select at least one quote type to continue.</div>
      </div>
    </div>
    <!-- Multi-step form (hidden initially) -->
    <form id="multi-step-form" class="hidden" method="POST" action="{{ route('admin.kitchen-quotes.store') }}" style="width: 100%;">
      @csrf
      <!-- STEP 1: Kitchen Top -->
      <div class="container step" data-step="1" id="step-1">
        <div class="header-row">
          <h2 style="margin:0;color:#333">Kitchen Top</h2>
          <div style="text-align:right">
            <div style="font-size:14px;color:#666;margin-bottom:5px">Accumulative Cost Total:</div>
            <div id="header-total-1" style="font-size:24px;font-weight:700;color:#2e7d32">$ -</div>
          </div>
        </div>

        <div class="table-container">
          <table>
            <thead>
              <tr>
                <th class="project-col">Project</th>
                <th class="scope-col">Scope/Material</th>
                <th class="qty-col">QTY</th>
                <th class="cost-col">COST</th>
                <th class="total-col">TOTAL</th>
                <th class="taxed-col">TAXED 'T'</th>
              </tr>
            </thead>

            <tbody id="kitchen-rows">
              {{-- each row uses data-cost attribute filled from DB --}}
              @php
              $kitchenLabels = [
              'Kitchen - Sq Ft','Labor Charge','Edge - Lin Ft','4" BS - Lin Ft','UM Sink Cutout',
              'Undermount Sink','small oval sink','Extra Sink Cutout','Cooktop Cutout','Electrical Cutouts',
              'Arc Charges','Radius 6" - 12"','Bump-Outs','water fall','removal','Extra Labor'
              ];
              @endphp

              @foreach($kitchenLabels as $label)
              <tr data-name="{{ $label }}">
                <td>{{ $label }}</td>
                <td class="alpha-fill">{{ $label === 'Kitchen - Sq Ft' ? 'alpha fill' : '' }}</td>
                <td><input type="number" class="qty-input num-fill" placeholder="0" min="0" step="0.01" value="0"></td>
                <td class="cost-value" data-cost="{{ number_format($kitchen_tops[$label] ?? 0, 4, '.', '') }}">
                  ${{ number_format($kitchen_tops[$label] ?? 0, 2) }}
                  <input type="hidden" name="kitchen[name][]" value="{{ $label }}">
                  <input type="hidden" name="kitchen[unit_price][]" value="{{ number_format($kitchen_tops[$label] ?? 0, 4, '.', '') }}">
                </td>
                <td class="line-total empty-value">$ -</td>
                <td class="taxed-t">{{ in_array($label, ['Kitchen - Sq Ft','Undermount Sink','small oval sink']) ? 'T' : '' }}</td>
              </tr>
              @endforeach
            </tbody>

            <tfoot>
              <tr>
                <td colspan="4" style="text-align:right;font-weight:700">Total:</td>
                <td id="grand-total-1" class="empty-value">$ -</td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>

        <div class="nav-footer">
          <button type="button" id="prev-tab-1" class="btn secondary" onclick="prevStep(1)" disabled><span>←</span> Previous</button>
          <div class="steps-indicator">Step 1 of 3</div>
          <button type="button" id="next-tab-1" class="btn" onclick="nextStep(1)">Next <span>→</span></button>
        </div>
      </div>

      <!-- STEP 2: Cabinet Manufacturer (updated: all inputs named for form submit) -->
      <div class="container step hidden" data-step="2" id="step-2">
        <div class="header-row">
          <h2 style="margin:0;color:#333">Kitchen Cabinet</h2>
          <div style="text-align:right">
            <div style="font-size:14px;color:#666;margin-bottom:5px">Accumulative Cost Total:</div>
            <div id="header-total-2" style="font-size:24px;font-weight:700;color:#2e7d32">$ -</div>
          </div>
        </div>

        <div class="table-container">
          <!-- Cabinet Manufacturer Section -->
          <div style="margin-bottom: 30px;">
            <h3 style="background-color: #fff3cd; padding: 10px; margin: 0; border-bottom: 1px solid #e0e0e0;">CABINET MANUFACTURER</h3>

            <table style="margin-bottom: 20px; width:100%;">
              <thead>
                <tr>
                  <th style="text-align:left;width:40%;">Manufacturer</th>
                  <th style="text-align:right;width:20%;">Unit Price</th>
                  <th style="text-align:center;width:20%;">Qty</th>
                  <th style="text-align:right;width:20%;">Line Total</th>
                </tr>
              </thead>

              <tbody id="manufacturer-rows">
                {{-- iterate manufacturers passed from controller --}}
                @php
                // fallback default list if $manufacturers not passed
                $defaultManufacturers = [
                'bertch'=>0.4310,'mantra'=>0.4450,'CB'=>0.4520,'802/USCD FROM 2020'=>0.4680,
                'KCD/USCD'=>0.4680,'dura'=>0.4680,'OMEGA'=>0.4850
                ];
                $manufacturers = $manufacturers ?? $defaultManufacturers;
                @endphp

                @foreach($manufacturers as $name => $unitCost)
                <tr data-name="{{ $name }}">
                  <td style="padding:8px;border:1px solid #e0e0e0;">
                    {{ $name }}
                    <input type="hidden" name="manufacturer[name][]" value="{{ $name }}">
                  </td>

                  <td style="padding:8px;border:1px solid #e0e0e0;text-align:right;">
                    {{-- show formatted unit price and add hidden input for unit price (4 decimals kept) --}}
                    <span class="manufacturer-unit" data-unit="{{ number_format((float)$unitCost, 4, '.', '') }}">
                      {{ number_format((float)$unitCost, 4, '.', '') }}
                    </span>
                    <input type="hidden" name="manufacturer[unit_price][]" value="{{ number_format((float)$unitCost, 4, '.', '') }}">
                  </td>

                  <td style="padding:8px;border:1px solid #e0e0e0;text-align:center;">
                    {{-- qty input (user editable) --}}
                    <input
                      type="number"
                      name="manufacturer[qty][]"
                      class="qty-input manufacturer-qty"
                      placeholder="0"
                      min="0"
                      step="0.01"
                      value="0"
                      style="width:100%;" />
                  </td>

                  <td style="padding:8px;border:1px solid #e0e0e0;text-align:right;" class="manufacturer-line empty-value">
                    $ -
                  </td>
                </tr>
                @endforeach

                {{-- manufacturer subtotal row --}}
                <tr>
                  <td colspan="3" style="padding:8px;border:1px solid #e0e0e0;text-align:center;font-weight:700">=</td>
                  <td id="manufacturer-total" style="padding:8px;border:1px solid #e0e0e0;text-align:right;background:#e8f5e8">$ -</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Cost Calculation Section (named inputs) -->
          <div style="margin-bottom: 30px;">
            <table style="margin-bottom:20px;width:100%;">
              <tr>
                <td style="width:30%;padding:8px;border:1px solid #e0e0e0;">20/20 LIST PRICE</td>
                <td style="width:20%;padding:8px;border:1px solid #e0e0e0;background-color:#e8f5e8;">
                  <input type="number" id="list-price" name="list_price" class="qty-input" placeholder="num fill" min="0" step="0.01" value="0">
                </td>
                <td id="multiplier-result" style="width:25%;padding:8px;border:1px solid #e0e0e0;text-align:right;">0.000</td>
                <td id="cost-total" style="width:25%;padding:8px;border:1px solid #e0e0e0;text-align:right;">0.00</td>
              </tr>
            </table>
          </div>

          <!-- Margin Markup Section (named inputs) -->
          <div style="margin-bottom: 30px;">
            <h3 style="background-color:#f5f5f5;padding:10px;margin:0;border-bottom:1px solid #e0e0e0;">MARGIN MARKUP</h3>
            <table style="margin-bottom:20px;width:100%;">
              <tr>
                <td style="width:30%;padding:8px;border:1px solid #e0e0e0;">1.425-1.5 CONTRACTOR 1ST TIME</td>
                <td style="width:20%;padding:8px;border:1px solid #e0e0e0;">
                  <input type="number" name="margin_markup" id="margin-markup" class="qty-input" placeholder="1.60" min="0" step="0.01" value="1.60">
                </td>
                <td id="markup-result" style="width:50%;padding:8px;border:1px solid #e0e0e0;text-align:right;">0.00</td>
              </tr>
              <!-- additional margin rows left static (no inputs) -->
              <tr>
                <td style="padding:8px;border:1px solid #e0e0e0;">1.5-1.55 CONTRACTOR</td>
                <td style="padding:8px;border:1px solid #e0e0e0;"></td>
                <td style="padding:8px;border:1px solid #e0e0e0;"></td>
              </tr>
              <tr>
                <td style="padding:8px;border:1px solid #e0e0e0;">1.55-1.6, 1.66-1.7 RETAIL</td>
                <td style="padding:8px;border:1px solid #e0e0e0;"></td>
                <td style="padding:8px;border:1px solid #e0e0e0;"></td>
              </tr>
            </table>
          </div>

          <!-- Miscellaneous Items (named inputs) -->
          <div style="margin-bottom: 30px;">
            <table style="margin-bottom:20px;width:100%;">
              <tr>
                <td style="width:30%;padding:8px;border:1px solid #e0e0e0;">TSC BUFFER</td>
                <td style="padding:8px;border:1px solid #e0e0e0;">(500+)</td>
                <td style="padding:8px;border:1px solid #e0e0e0;text-align:right;" id="tsc-buffer">0.00</td>
              </tr>

              <tr>
                <td style="padding:8px;border:1px solid #e0e0e0;">HARDWARE QUANTITY</td>
                <td style="padding:8px;border:1px solid #e0e0e0;background:#ffb74d;">
                  <input type="number" id="hardware-qty" name="hardware_qty" class="qty-input" placeholder="0.00" min="0" step="0.01" value="0.00">
                </td>
                <td id="hardware-total" style="padding:8px;border:1px solid #e0e0e0;text-align:right;">0.00</td>
              </tr>

              <tr>
                <td style="padding:8px;border:1px solid #e0e0e0;">MISC ITEMS</td>
                <td style="padding:8px;border:1px solid #e0e0e0;"></td>
                <td style="padding:8px;border:1px solid #e0e0e0;"></td>
              </tr>

              <tr>
                <td style="padding:8px;border:1px solid #e0e0e0;">802 FREIGHT SURCHARGE</td>
                <td style="padding:8px;border:1px solid #e0e0e0;"></td>
                <td style="padding:8px;border:1px solid #e0e0e0;text-align:right;">0.00</td>
              </tr>

              <tr>
                <td style="padding:8px;border:1px solid #e0e0e0;">PRICE CHANGE BUFFER</td>
                <td style="padding:8px;border:1px solid #e0e0e0;background:#ffcdd2;">
                  <input type="number" id="price-buffer" name="price_buffer" class="qty-input" placeholder="0.00" min="0" step="0.01" value="0.00">
                </td>
                <td id="price-buffer-result" style="padding:8px;border:1px solid #e0e0e0;text-align:right;">0.00</td>
              </tr>

              <tr>
                <td style="padding:8px;border:1px solid #e0e0e0;">MORE THAN 1 PHONE CALL/DAY</td>
                <td style="padding:8px;border:1px solid #e0e0e0;background:#ffcdd2;">
                  <input type="number" id="phone-call-buffer" name="phone_call_buffer" class="qty-input" placeholder="0.00" min="0" step="0.01" value="0.00">
                </td>
                <td id="phone-call-result" style="padding:8px;border:1px solid #e0e0e0;text-align:right;">0.00</td>
              </tr>

              <tr>
                <td style="padding:8px;border:1px solid #e0e0e0;background:#e8f5e8;font-weight:bold;">TOTAL RETAIL</td>
                <td style="padding:8px;border:1px solid #e0e0e0;"></td>
                <td id="total-retail" style="padding:8px;border:1px solid #e0e0e0;text-align:right;background:#e8f5e8;">0.00</td>
              </tr>

              <tr>
                <td style="padding:8px;border:1px solid #e0e0e0;">TAX</td>
                <td style="padding:8px;border:1px solid #e0e0e0;"></td>
                <td id="tax-amount" style="padding:8px;border:1px solid #e0e0e0;text-align:right;">0.00</td>
              </tr>
            </table>
          </div>

          <!-- Delivery Section (named inputs) -->
          <div style="margin-bottom: 30px;">
            <h3 style="background-color:#fff3cd;padding:10px;margin:0;border-bottom:1px solid #e0e0e0;">DELIVERY</h3>
            <table style="margin-bottom:20px;width:100%;">
              <tr>
                <td style="width:30%;padding:8px;border:1px solid #e0e0e0;">FULL KIT TAILGATE</td>
                <td style="width:20%;padding:8px;border:1px solid #e0e0e0;text-align:right;">300.00</td>
                <td style="width:20%;padding:8px;border:1px solid #e0e0e0;background:#f5f5f5;">
                  <input type="number" id="delivery-full-kit" name="delivery_full_kit" class="qty-input" placeholder="" min="0" step="0.01" value="0">
                </td>
                <td id="delivery-full-kit-total" style="width:20%;padding:8px;border:1px solid #e0e0e0;text-align:right;">0.00</td>
              </tr>

              <tr>
                <td style="padding:8px;border:1px solid #e0e0e0;">UP TO TEN ITEMS</td>
                <td style="padding:8px;border:1px solid #e0e0e0;text-align:right;">175.00</td>
                <td style="padding:8px;border:1px solid #e0e0e0;background:#f5f5f5;">
                  <input type="number" id="delivery-ten-items" name="delivery_ten_items" class="qty-input" placeholder="" min="0" step="0.01" value="0">
                </td>
                <td id="delivery-ten-items-total" style="padding:8px;border:1px solid #e0e0e0;text-align:right;">0.00</td>
              </tr>

              <tr>
                <td style="padding:8px;border:1px solid #e0e0e0;">SINGLE ITEM VAN</td>
                <td style="padding:8px;border:1px solid #e0e0e0;text-align:right;">100.00</td>
                <td style="padding:8px;border:1px solid #e0e0e0;background:#f5f5f5;">
                  <input type="number" id="delivery-single-van" name="delivery_single_van" class="qty-input" placeholder="" min="0" step="0.01" value="0">
                </td>
                <td id="delivery-single-van-total" style="padding:8px;border:1px solid #e0e0e0;text-align:right;">0.00</td>
              </tr>

              <tr>
                <td style="padding:8px;border:1px solid #e0e0e0;">CUSTOMER PICKUP</td>
                <td style="padding:8px;border:1px solid #e0e0e0;text-align:right;"></td>
                <td style="padding:8px;border:1px solid #e0e0e0;background:#f5f5f5;">
                  <input type="number" id="delivery-pickup" name="delivery_pickup" class="qty-input" placeholder="" min="0" step="0.01" value="0">
                </td>
                <td id="delivery-pickup-total" style="padding:8px;border:1px solid #e0e0e0;text-align:right;">0.00</td>
              </tr>

              <tr>
                <td colspan="3" style="padding:8px;border:1px solid #e0e0e0;text-align:right;font-weight:bold;background:#e8f5e8;">TOTAL</td>
                <td id="delivery-total" style="padding:8px;border:1px solid #e0e0e0;text-align:right;background:#e8f5e8;">0.00</td>
              </tr>
            </table>
          </div>

          <!-- Final Surcharge -->
          <div style="margin-bottom:30px;">
            <table style="width:100%;margin-bottom:20px;">
              <tr>
                <td style="width:30%;padding:8px;border:1px solid #e0e0e0;">DBA fee/fuel surcharge</td>
                <td style="width:20%;padding:8px;border:1px solid #e0e0e0;">
                  <input type="number" id="dba-surcharge" name="dba_surcharge" class="qty-input" placeholder="0.03" min="0" step="0.01" value="0.03">
                </td>
                <td id="dba-result" style="width:50%;padding:8px;border:1px solid #e0e0e0;text-align:right;">0.00</td>
              </tr>
              <tr>
                <td style="padding:8px;border:1px solid #e0e0e0;font-weight:bold;">final total DBA</td>
                <td style="padding:8px;border:1px solid #e0e0e0;"></td>
                <td id="final-total" style="padding:8px;border:1px solid #e0e0e0;text-align:right;font-weight:bold;">0.00</td>
              </tr>
            </table>
          </div>
        </div>

        <!-- Navigation Tabs -->
        <div style="padding:20px;border-top:1px solid #e0e0e0;background:#f8f9fa">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <button type="button" id="prev-tab-2" class="btn secondary" onclick="prevStep(2)"><span>←</span> Previous</button>
            <div style="font-size:14px;color:#666">Step 2 of 3</div>
            <button type="button" id="next-tab-2" class="btn" onclick="nextStep(2)">Next <span>→</span></button>
          </div>
        </div>
      </div>

      <!-- STEP 3: Summary & Submit -->
      <div class="container step hidden" data-step="3" id="step-3">
        <div class="header-row">
          <h2 style="margin:0;color:#333">Summary</h2>
          <div style="text-align:right">
            <div style="font-size:14px;color:#666;margin-bottom:5px">Final Total</div>
            <div id="header-total-3" style="font-size:24px;font-weight:700;color:#2e7d32">$ -</div>
          </div>
        </div>

        <div class="table-container summary-box">
          <div id="summary-list"></div>

          <div class="summary-row total" style="margin-top:12px">
            <div>Total</div>
            <div id="final-total" style="font-weight:700">$ -</div>
          </div>
        </div>

        <div class="nav-footer">
          <button type="button" class="btn secondary" onclick="prevStep(3)"><span>←</span> Previous</button>
          <div class="steps-indicator">Step 3 of 3</div>
          <button type="submit" class="btn" id="save-quote">Save & Finish</button>
        </div>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
  /* Multi-step form JS: no external deps */
  document.addEventListener('DOMContentLoaded', function() {
    const selected = {
      kitchen: false,
      vanity: false
    };

    function el(id) {
      return document.getElementById(id);
    }

    window.toggleCheckbox = function(key) {
      selected[key] = !selected[key];
      const chk = document.getElementById(key + '-quote');
      chk.checked = selected[key];
      const box = document.getElementById('chk-' + key);
      if (selected[key]) box.classList.add('selected');
      else box.classList.remove('selected');
    }

    window.beginQuote = function() {
      // require at least kitchen selected (vanity disabled)
      if (!selected.kitchen) {
        el('welcome-error').style.display = 'block';
        return;
      }
      el('welcome-panel').classList.add('hidden');
      el('multi-step-form').classList.remove('hidden');
      showStep(1);
    }

    // Step navigation
    function showStep(step) {
      document.querySelectorAll('.step').forEach(s => s.classList.add('hidden'));
      const node = document.querySelector('.step[data-step="' + step + '"]');
      if (node) node.classList.remove('hidden');

      // initial recalc
      recalcAll();
    }
    window.nextStep = function(current) {
      const next = current + 1;
      showStep(next);
    }
    window.prevStep = function(current) {
      const prev = current - 1;
      if (prev < 1) {
        return;
      }
      showStep(prev);
    }

    // Calculations for kitchen rows
    function recalcKitchen() {
      let grand = 0;
      document.querySelectorAll('#kitchen-rows tr[data-name]').forEach(tr => {
        const qty = parseFloat(tr.querySelector('.qty-input').value) || 0;
        const cost = parseFloat(tr.querySelector('.cost-value').dataset.cost) || 0;
        const line = qty * cost;
        grand += line;
        const lineCell = tr.querySelector('.line-total');
        lineCell.textContent = line > 0 ? '$' + line.toFixed(2) : '$ -';
        lineCell.classList.toggle('empty-value', line === 0);
      });
      el('grand-total-1').textContent = grand > 0 ? '$' + grand.toFixed(2) : '$ -';
      el('header-total-1').textContent = grand > 0 ? '$' + grand.toFixed(2) : '$ -';
      return grand;
    }

    // Calculations for manufacturer rows
    function recalcManufacturer() {
      let total = 0;
      document.querySelectorAll('#manufacturer-rows tr[data-name]').forEach(tr => {
        const qty = parseFloat(tr.querySelector('.manufacturer-qty').value) || 0;
        const unit = parseFloat(tr.querySelector('.manufacturer-unit').textContent) || 0;
        const line = qty * unit;
        total += line;
        const lineCell = tr.querySelector('.manufacturer-line');
        lineCell.textContent = line > 0 ? '$' + line.toFixed(4) : '$ -';
        lineCell.classList.toggle('empty-value', line === 0);
      });
      el('manufacturer-total').textContent = total > 0 ? '$' + total.toFixed(4) : '$ -';
      el('header-total-2').textContent = total > 0 ? '$' + total.toFixed(4) : '$ -';
      return total;
    }

    // multiplier and final aggregation
    function recalcAll() {
      const kitchenSum = recalcKitchen();
      const manufacturerSum = recalcManufacturer();
      const listPrice = parseFloat(el('list-price')?.value || 0) || 0;
      const multiplierResult = listPrice * manufacturerSum;
      el('multiplier-result').textContent = multiplierResult ? multiplierResult.toFixed(4) : '';
      el('cost-total').textContent = multiplierResult ? '$' + multiplierResult.toFixed(2) : '';

      const final = kitchenSum + multiplierResult + (manufacturerSum || 0);
      el('final-total').textContent = final ? '$' + final.toFixed(2) : '$ -';
      el('header-total-3').textContent = final ? '$' + final.toFixed(2) : '$ -';
    }

    // event listeners: delegate
    document.addEventListener('input', function(e) {
      if (e.target.matches('#kitchen-rows .qty-input') || e.target.matches('#manufacturer-rows .manufacturer-qty') || e.target.matches('#list-price')) {
        recalcAll();
      }
    });

    // Summary building on navigate to step 3
    document.querySelectorAll('[onclick^="nextStep"]').forEach(btn => {
      btn.addEventListener('click', function() {
        // If moving to step 3, build summary
        const current = parseInt(this.getAttribute('onclick').match(/\d+/)?.[0] || 1);
        if (current + 1 === 3) {
          buildSummary();
        }
      });
    });

    function buildSummary() {
      const list = el('summary-list');
      list.innerHTML = '';
      // kitchen non zero lines
      document.querySelectorAll('#kitchen-rows tr[data-name]').forEach(tr => {
        const name = tr.getAttribute('data-name');
        const qty = parseFloat(tr.querySelector('.qty-input').value) || 0;
        const unit = parseFloat(tr.querySelector('.cost-value').dataset.cost) || 0;
        const line = qty * unit;
        if (line > 0) {
          const div = document.createElement('div');
          div.className = 'summary-row';
          div.innerHTML = `<div>${name} × ${qty}</div><div>$${line.toFixed(2)}</div>`;
          list.appendChild(div);
        }
      });
      // manufacturers non zero lines
      document.querySelectorAll('#manufacturer-rows tr[data-name]').forEach(tr => {
        const name = tr.getAttribute('data-name');
        const qty = parseFloat(tr.querySelector('.manufacturer-qty').value) || 0;
        const unit = parseFloat(tr.querySelector('.manufacturer-unit').textContent) || 0;
        const line = qty * unit;
        if (line > 0) {
          const div = document.createElement('div');
          div.className = 'summary-row';
          div.innerHTML = `<div>${name} × ${qty}</div><div>$${line.toFixed(4)}</div>`;
          list.appendChild(div);
        }
      });

      // footer totals
      const finalText = el('final-total').textContent || '$ -';
      el('final-total').textContent = finalText;
    }

    // Form submit via AJAX
    document.getElementById('multi-step-form').addEventListener('submit', function(e) {
      e.preventDefault();
      // gather data: kitchen names, qtys, unit_prices; manufacturer names, qtys, unit_prices
      const fd = new FormData();
      // kitchen
      document.querySelectorAll('#kitchen-rows tr[data-name]').forEach((tr, idx) => {
        const name = tr.getAttribute('data-name');
        const qty = tr.querySelector('.qty-input').value || 0;
        const unit = tr.querySelector('.cost-value').dataset.cost || 0;
        fd.append('kitchen[name][]', name);
        fd.append('kitchen[qty][]', qty);
        fd.append('kitchen[unit_price][]', unit);
      });
      // manufacturers
      document.querySelectorAll('#manufacturer-rows tr[data-name]').forEach((tr, idx) => {
        const name = tr.getAttribute('data-name');
        const qty = tr.querySelector('.manufacturer-qty').value || 0;
        const unit = tr.querySelector('.manufacturer-unit').textContent || 0;
        fd.append('manufacturer[name][]', name);
        fd.append('manufacturer[qty][]', qty);
        fd.append('manufacturer[unit_price][]', unit);
      });

      // add totals
      fd.append('final_total', (el('final-total').textContent || '').replace('$', ''));

      const btn = document.getElementById('save-quote');
      btn.disabled = true;
      btn.textContent = 'Saving...';

      fetch(this.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: fd
      }).then(r => r.json()).then(json => {
        btn.disabled = false;
        btn.textContent = 'Save & Finish';
        if (json && json.success) {
          alert(json.message || 'Saved successfully');
          location.reload();
        } else {
          alert((json && json.message) ? json.message : 'Save failed');
        }
      }).catch(err => {
        btn.disabled = false;
        btn.textContent = 'Save & Finish';
        alert('Save failed');
        console.error(err);
      });
    });

    // initial recalc so header shows DB values properly
    recalcAll();
  });
</script>
@endpush