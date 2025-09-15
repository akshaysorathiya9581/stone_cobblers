@extends('layouts.admin')

@section('title','Quote — Multi Step')

@push('css')
<style>

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
    <form id="multi-step-form" class="hidden" method="POST" action="{{ route('admin.quotes.store') }}" style="width: 100%;">
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
                <td>
                  <input type="number"
                         name="kitchen[qty][]"
                         class="qty-input num-fill"
                         placeholder="0"
                         min="0"
                         step="0.01"
                         value="{{ old('kitchen.qty',0) }}">
                </td>

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
          <button type="button" id="next-tab-1" class="btn" data-current="1">Next <span>→</span></button>
        </div>
      </div>

      <!-- STEP 2: Cabinet Manufacturer -->
      <div class="container step hidden" data-step="2" id="step-2">
        <div class="header-row">
          <h2 style="margin:0;color:#333">Kitchen Cabinet</h2>
          <div style="text-align:right">
            <div style="font-size:14px;color:#666;margin-bottom:5px">Accumulative Cost Total:</div>
            <div id="header-total-2" style="font-size:24px;font-weight:700;color:#2e7d32">$ -</div>
          </div>
        </div>

        <div class="table-container">
          @php
            $defaults = [
              'bertch'=>0.4310,'mantra'=>0.4450,'CB'=>0.4520,'802/USCD FROM 2020'=>0.4680,
              'KCD/USCD'=>0.4680,'dura'=>0.4680,'OMEGA'=>0.4850
            ];
            $manufacturers = $manufacturers ?? $defaults;
            function fmtAuto($v) {
                $v = (float)$v;
                if (round($v, 2) != $v) {
                    return number_format($v, 4, '.', '');
                }
                return number_format($v, 2, '.', '');
            }
          @endphp

          <!-- CABINET MANUFACTURER -->
          <div style="margin-bottom: 30px;">
            <h3 style="background-color: #fff3cd; padding:10px; margin:0; border-bottom:1px solid #e0e0e0;">CABINET MANUFACTURER</h3>

            <table style="width:100%; margin-bottom:20px;">
              <thead>
                <tr>
                  <th style="text-align:left;width:40%;">Manufacturer</th>
                  <th style="text-align:right;width:20%;">Unit Price</th>
                  <th style="text-align:center;width:20%;">Qty</th>
                  <th style="text-align:right;width:20%;">Line Total</th>
                </tr>
              </thead>

              <tbody id="manufacturer-rows">
                @foreach(['bertch','mantra','CB','802/USCD FROM 2020','KCD/USCD','dura','OMEGA'] as $i => $mfg)
                <tr data-name="{{ $mfg }}">
                  <td style="padding:8px;border:1px solid #e0e0e0;">
                    {{ $mfg }}
                    <input type="hidden" name="manufacturer[name][]" value="{{ $mfg }}">
                  </td>

                  <td style="padding:8px;border:1px solid #e0e0e0;text-align:right;">
                    {{ fmtAuto($manufacturers[$mfg] ?? $defaults[$mfg]) }}
                    <input type="hidden" name="manufacturer[unit_price][]" value="{{ fmtAuto($manufacturers[$mfg] ?? $defaults[$mfg]) }}">
                  </td>

                  <td style="padding:8px;border:1px solid #e0e0e0;text-align:center;">
                    <input type="number" name="manufacturer[qty][]" class="qty-input manufacturer-qty" min="0" step="0.01" value="{{ old('manufacturer.qty.'.$i, 0) }}" style="width:100%;">
                  </td>

                  <td style="padding:8px;border:1px solid #e0e0e0;text-align:right;" class="manufacturer-line empty-value">
                    $ -
                    <input type="hidden" name="manufacturer[line_total][]" class="manufacturer-line-hidden" value="0">
                  </td>
                </tr>
                @endforeach

                <!-- subtotal -->
                <tr>
                  <td colspan="3" style="padding:8px;border:1px solid #e0e0e0;text-align:center;font-weight:700">=</td>
                  <td id="manufacturer-total" style="padding:8px;border:1px solid #e0e0e0;text-align:right;background:#e8f5e8">$ -</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Cost Calculation Section -->
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

          <!-- Margin Markup -->
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
            </table>
          </div>

          <!-- Misc / Buffers -->
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

          <!-- Delivery Section -->
          <div style="margin-bottom: 30px;">
            <h3 style="background-color:#fff3cd;padding:10px;margin:0;border-bottom:1px solid #e0e0e0;">DELIVERY</h3>
            <table style="margin-bottom:20px;width:100%;">
              <tr>
                <td style="width:30%;padding:8px;border:1px solid #e0e0e0;">FULL KIT TAILGATE</td>
                <td style="width:20%;padding:8px;border:1px solid #e0e0e0;text-align:right;">{{ fmtAuto($manufacturers['FULL KIT TAILGATE'] ?? '300.00') }}</td>
                <td style="width:20%;padding:8px;border:1px solid #e0e0e0;background:#f5f5f5;">
                  <input type="number" id="delivery-full-kit" name="delivery_full_kit" class="qty-input" placeholder="" min="0" step="0.01" value="0">
                </td>
                <td id="delivery-full-kit-total" style="width:20%;padding:8px;border:1px solid #e0e0e0;text-align:right;">0.00</td>
              </tr>

              <tr>
                <td style="padding:8px;border:1px solid #e0e0e0;">UP TO TEN ITEMS</td>
                <td style="padding:8px;border:1px solid #e0e0e0;text-align:right;">{{ fmtAuto($manufacturers['UP TO TEN ITEMS'] ?? '175.00') }}</td>
                <td style="padding:8px;border:1px solid #e0e0e0;background:#f5f5f5;">
                  <input type="number" id="delivery-ten-items" name="delivery_ten_items" class="qty-input" placeholder="" min="0" step="0.01" value="0">
                </td>
                <td id="delivery-ten-items-total" style="padding:8px;border:1px solid #e0e0e0;text-align:right;">0.00</td>
              </tr>

              <tr>
                <td style="padding:8px;border:1px solid #e0e0e0;">SINGLE ITEM VAN</td>
                <td style="padding:8px;border:1px solid #e0e0e0;text-align:right;">{{ fmtAuto($manufacturers['SINGLE ITEM VAN'] ?? '100.00') }}</td>
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
                <td id="step2-final-result" style="width:50%;padding:8px;border:1px solid #e0e0e0;text-align:right;">0.00</td>
              </tr>
              <tr>
                <td style="padding:8px;border:1px solid #e0e0e0;font-weight:bold;">step 2 subtotal</td>
                <td style="padding:8px;border:1px solid #e0e0e0;"></td>
                <td id="step2-final-total" style="padding:8px;border:1px solid #e0e0e0;text-align:right;font-weight:bold;">0.00</td>
              </tr>
            </table>
          </div>
        </div>

        <!-- Navigation Tabs -->
        <div style="padding:20px;border-top:1px solid #e0e0e0;background:#f8f9fa">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <button type="button" id="prev-tab-2" class="btn secondary" onclick="prevStep(2)"><span>←</span> Previous</button>
            <div style="font-size:14px;color:#666">Step 2 of 3</div>
            <button type="button" id="next-tab-2" class="btn" data-current="2">Next <span>→</span></button>
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
  jQuery(function($){
    // Utilities
    function el(id){ return document.getElementById(id); } // keep for fetch/form usage
    function parseNum(v){
      if (v === null || v === undefined) return 0;
      v = String(v).replace(/\$/g,'').replace(/,/g,'').trim();
      if (v === '') return 0;
      return isNaN(parseFloat(v)) ? 0 : parseFloat(v);
    }
    function fmt2(n){ return (isNaN(n) ? 0 : n).toFixed(2); }
    function fmt4(n){ return (isNaN(n) ? 0 : n).toFixed(4); }

    // Welcome toggles
    var selected = { kitchen: false, vanity: false };
    window.toggleCheckbox = function(key){
      selected[key] = !selected[key];
      var chk = $('#' + key + '-quote');
      if (chk.length) chk.prop('checked', selected[key]);
      var box = $('#chk-' + key);
      if (box.length) box.toggleClass('selected', selected[key]);
    };

    window.beginQuote = function(){
      if (!selected.kitchen) {
        $('#welcome-error').show();
        return;
      }
      $('#welcome-panel').addClass('hidden');
      $('#multi-step-form').removeClass('hidden');
      showStep(1);
    };

    // Navigation
    function showStep(step){
      $('.step').addClass('hidden');
      $('.step[data-step="'+step+'"]').removeClass('hidden');
      recalcAll();
    }
    window.nextStep = function(current){
      if (!validateStep(current)) return;
      var next = current + 1;
      showStep(next);
      if (next === 3) buildSummary();
    };
    window.prevStep = function(current){
      var prev = current - 1;
      if (prev < 1) return;
      showStep(prev);
    };

    function validateStep(step){
      if (step === 1){
        var ok = true;
        $('#kitchen-rows input[name="kitchen[qty][]"]').each(function(){
          var v = parseNum($(this).val());
          if (v < 0){ $(this).addClass('invalid'); ok = false; } else $(this).removeClass('invalid');
        });
        if (!ok) alert('Please correct kitchen quantities (numbers >= 0).');
        return ok;
      }
      if (step === 2){
        var ok = true;
        $('#manufacturer-rows input[name="manufacturer[qty][]"]').each(function(){
          var v = parseNum($(this).val());
          if (v < 0){ $(this).addClass('invalid'); ok = false; } else $(this).removeClass('invalid');
        });
        var lp = $('#list-price');
        if (lp.length){
          var v = parseNum(lp.val());
          if (v < 0){ lp.addClass('invalid'); ok = false; } else lp.removeClass('invalid');
        }
        if (!ok) alert('Please correct manufacturer quantities and list price (numbers >= 0).');
        return ok;
      }
      return true;
    }

    // --- calculations ---

    // Kitchen
    function recalcKitchen(){
      var grand = 0;
      $('#kitchen-rows tr[data-name]').each(function(){
        var $tr = $(this);
        var qty = parseNum($tr.find('input[name="kitchen[qty][]"]').val() || 0);
        var unit = parseNum($tr.find('input[name="kitchen[unit_price][]"]').val() || $tr.find('.cost-value').data('cost') || 0);
        var line = qty * unit;
        grand += line;
        var $lineCell = $tr.find('.line-total');
        if (line > 0){
          $lineCell.text('$' + fmt2(line)).removeClass('empty-value');
        } else {
          $lineCell.text('$ -').addClass('empty-value');
        }
      });
      $('#grand-total-1').text(grand > 0 ? '$' + fmt2(grand) : '$ -');
      $('#header-total-1').text(grand > 0 ? '$' + fmt2(grand) : '$ -');
      return grand;
    }

    // Manufacturer
    function recalcManufacturer(){
      var total = 0;
      $('#manufacturer-rows tr[data-name]').each(function(){
        var $tr = $(this);
        var qty = parseNum($tr.find('input[name="manufacturer[qty][]"]').val() || 0);
        var unit = parseNum($tr.find('input[name="manufacturer[unit_price][]"]').val() || $tr.find('td').eq(1).text() || 0);
        var line = qty * unit;
        total += line;
        var $display = $tr.find('.manufacturer-line');
        var $hiddenLine = $tr.find('input.manufacturer-line-hidden[name="manufacturer[line_total][]"]');
        if (line > 0){
          $display.text('$' + fmt4(line)).removeClass('empty-value');
          if ($hiddenLine.length) $hiddenLine.val(fmt4(line));
        } else {
          $display.text('$ -').addClass('empty-value');
          if ($hiddenLine.length) $hiddenLine.val('0');
        }
      });
      $('#manufacturer-total').text(total > 0 ? '$' + fmt4(total) : '$ -');
      $('#header-total-2').text(total > 0 ? '$' + fmt4(total) : '$ -');
      return total;
    }

    // Delivery
    function recalcDelivery(){
      var deliveryTotal = 0;

      function readUnitByLabel(label, fallbackHiddenName){
        if (fallbackHiddenName){
          var hid = $('input[name="'+fallbackHiddenName+'"]');
          if (hid.length) return parseNum(hid.val());
        }
        var row = $('#step-2 table tr').filter(function(){
          return $(this).children().length && $(this).children().first().text().trim().toUpperCase().indexOf(label.toUpperCase()) === 0;
        }).first();
        if (row.length) return parseNum(row.children().eq(1).text() || row.children().eq(1).html() || 0);
        return 0;
      }

      var fullQty = parseNum($('#delivery-full-kit').val() || 0);
      var fullUnit = readUnitByLabel('FULL KIT TAILGATE', 'delivery_unit_price[full_kit_tailgate]');
      var fullLine = fullQty * fullUnit;
      deliveryTotal += fullLine;
      $('#delivery-full-kit-total').text(fullLine > 0 ? '$' + fmt2(fullLine) : '0.00');

      var tenQty = parseNum($('#delivery-ten-items').val() || 0);
      var tenUnit = readUnitByLabel('UP TO TEN ITEMS', 'delivery_unit_price[ten_items]');
      var tenLine = tenQty * tenUnit;
      deliveryTotal += tenLine;
      $('#delivery-ten-items-total').text(tenLine > 0 ? '$' + fmt2(tenLine) : '0.00');

      var vanQty = parseNum($('#delivery-single-van').val() || 0);
      var vanUnit = readUnitByLabel('SINGLE ITEM VAN', 'delivery_unit_price[single_item_van]');
      var vanLine = vanQty * vanUnit;
      deliveryTotal += vanLine;
      $('#delivery-single-van-total').text(vanLine > 0 ? '$' + fmt2(vanLine) : '0.00');

      var puQty = parseNum($('#delivery-pickup').val() || 0);
      var puUnit = readUnitByLabel('CUSTOMER PICKUP', 'delivery_unit_price[pickup]');
      var puLine = puQty * puUnit;
      deliveryTotal += puLine;
      $('#delivery-pickup-total').text(puLine > 0 ? '$' + fmt2(puLine) : '0.00');

      $('#delivery-total').text(deliveryTotal > 0 ? '$' + fmt2(deliveryTotal) : '0.00');
      return deliveryTotal;
    }

    // Master recalc
    function recalcAll(){
      var kitchenSum = recalcKitchen();
      var manufacturerSum = recalcManufacturer();

      var listPrice = parseNum($('#list-price').val() || 0);
      var multiplierResult = listPrice * manufacturerSum;

      if ($('#multiplier-result').length) $('#multiplier-result').text(multiplierResult ? fmt4(multiplierResult) : '0.000');
      if ($('#cost-total').length) $('#cost-total').text(multiplierResult ? ('$' + fmt2(multiplierResult)) : '$0.00');

      var step2Subtotal = manufacturerSum + multiplierResult;
      if ($('#step2-final-total').length) $('#step2-final-total').text(step2Subtotal ? ('$' + fmt4(step2Subtotal)) : '$0.00');
      if ($('#step2-final-result').length) $('#step2-final-result').text(step2Subtotal ? ('$' + fmt4(step2Subtotal)) : '$0.00');

      var deliverySum = recalcDelivery();

      var priceBuffer = parseNum($('#price-buffer').val() || 0);
      var phoneBuffer = parseNum($('#phone-call-buffer').val() || 0);
      var hardwareQty = parseNum($('#hardware-qty').val() || 0);
      var dba = parseNum($('#dba-surcharge').val() || 0);

      var totalRetail = step2Subtotal + deliverySum + priceBuffer + phoneBuffer + hardwareQty;
      $('#total-retail').text(totalRetail ? ('$' + fmt2(totalRetail)) : '0.00');

      var taxAmount = 0;
      $('#tax-amount').text(fmt2(taxAmount));

      var step2FinalWithDba = step2Subtotal + dba;
      $('#step2-final-result').text(step2FinalWithDba ? ('$' + fmt4(step2FinalWithDba)) : '$0.00');
      $('#step2-final-total').text(step2FinalWithDba ? ('$' + fmt4(step2FinalWithDba)) : '$0.00');

      var final = kitchenSum + step2Subtotal + deliverySum + priceBuffer + phoneBuffer + hardwareQty + dba + taxAmount;
      $('#final-total').text(final ? ('$' + fmt2(final)) : '$ -');
      $('#header-total-3').text(final ? ('$' + fmt2(final)) : '$ -');

      return { kitchenSum: kitchenSum, manufacturerSum: manufacturerSum, multiplierResult: multiplierResult, step2Subtotal: step2Subtotal, deliverySum: deliverySum, final: final };
    }

    // Event delegation: recalc
    $(document).on('input', [
      '#kitchen-rows input[name="kitchen[qty][]"]',
      '#manufacturer-rows input[name="manufacturer[qty][]"]',
      '#list-price',
      '#price-buffer',
      '#phone-call-buffer',
      '#hardware-qty',
      '#delivery-full-kit',
      '#delivery-ten-items',
      '#delivery-single-van',
      '#delivery-pickup',
      '#dba-surcharge'
    ].join(','), function(){
      recalcAll();
    });

    // Next buttons
    $('#next-tab-1').on('click', function(){ nextStep(1); });
    $('#next-tab-2').on('click', function(){ nextStep(2); });

    // Build summary
    function buildSummary(){
      var $list = $('#summary-list');
      if (!$list.length) return;
      $list.empty();

      $('#kitchen-rows tr[data-name]').each(function(){
        var $tr = $(this);
        var name = $tr.data('name');
        var qty = parseNum($tr.find('input[name="kitchen[qty][]"]').val() || 0);
        var unit = parseNum($tr.find('input[name="kitchen[unit_price][]"]').val() || $tr.find('.cost-value').data('cost') || 0);
        var line = qty * unit;
        if (line > 0){
          var $div = $('<div class="summary-row"></div>');
          $div.html('<div>'+name+' × '+qty+'</div><div>$'+fmt2(line)+'</div>');
          $list.append($div);
        }
      });

      $('#manufacturer-rows tr[data-name]').each(function(){
        var $tr = $(this);
        var name = $tr.data('name');
        var qty = parseNum($tr.find('input[name="manufacturer[qty][]"]').val() || 0);
        var unit = parseNum($tr.find('input[name="manufacturer[unit_price][]"]').val() || $tr.find('td').eq(1).text() || 0);
        var line = qty * unit;
        if (line > 0){
          var $div = $('<div class="summary-row"></div>');
          $div.html('<div>'+name+' × '+qty+'</div><div>$'+fmt4(line)+'</div>');
          $list.append($div);
        }
      });

      var deliveries = [
        { id: 'delivery-full-kit', label: 'FULL KIT TAILGATE' },
        { id: 'delivery-ten-items', label: 'UP TO TEN ITEMS' },
        { id: 'delivery-single-van', label: 'SINGLE ITEM VAN' },
        { id: 'delivery-pickup', label: 'CUSTOMER PICKUP' }
      ];
      $.each(deliveries, function(_, d){
        var qty = parseNum($('#'+d.id).val() || 0);
        var row = $('#step-2 table tr').filter(function(){
          return $(this).children().length && $(this).children().first().text().trim().toUpperCase().indexOf(d.label) === 0;
        }).first();
        var unit = row.length ? parseNum(row.children().eq(1).text() || row.children().eq(1).html() || 0) : 0;
        var line = qty * unit;
        if (line > 0){
          var $div = $('<div class="summary-row"></div>');
          $div.html('<div>'+d.label+' × '+qty+'</div><div>$'+fmt2(line)+'</div>');
          $list.append($div);
        }
      });

      var state = recalcAll();
      $('#final-total').text(state.final ? ('$' + fmt2(state.final)) : '$ -');
      $('#header-total-3').text(state.final ? ('$' + fmt2(state.final)) : '$ -');
    }

    // Form submit
    var $form = $('#multi-step-form');
    if ($form.length){
      $form.on('submit', function(e){
        e.preventDefault();
        var state = recalcAll();
        if (!state.final || state.final <= 0){
          if (!confirm('Final total is $0. Do you want to submit anyway?')) return;
        }

        var fd = new FormData();

        // kitchen
        $('#kitchen-rows tr[data-name]').each(function(){
          var $tr = $(this);
          var name = $tr.data('name');
          var qty = parseNum($tr.find('input[name="kitchen[qty][]"]').val() || 0);
          var unit = parseNum($tr.find('input[name="kitchen[unit_price][]"]').val() || $tr.find('.cost-value').data('cost') || 0);
          fd.append('kitchen[name][]', name);
          fd.append('kitchen[qty][]', qty);
          fd.append('kitchen[unit_price][]', unit);
        });

        // manufacturers
        $('#manufacturer-rows tr[data-name]').each(function(){
          var $tr = $(this);
          var name = $tr.data('name');
          var qty = parseNum($tr.find('input[name="manufacturer[qty][]"]').val() || 0);
          var unit = parseNum($tr.find('input[name="manufacturer[unit_price][]"]').val() || $tr.find('td').eq(1).text() || 0);
          var line = qty * unit;
          fd.append('manufacturer[name][]', name);
          fd.append('manufacturer[qty][]', qty);
          fd.append('manufacturer[unit_price][]', unit);
          fd.append('manufacturer[line_total][]', fmt4(line));
        });

        // deliveries
        var deliveryMappings = [
          { id: 'delivery-full-kit', key: 'full_kit_tailgate', label: 'FULL KIT TAILGATE' },
          { id: 'delivery-ten-items', key: 'ten_items', label: 'UP TO TEN ITEMS' },
          { id: 'delivery-single-van', key: 'single_item_van', label: 'SINGLE ITEM VAN' },
          { id: 'delivery-pickup', key: 'pickup', label: 'CUSTOMER PICKUP' }
        ];
        $.each(deliveryMappings, function(_, m){
          var qty = parseNum($('#'+m.id).val() || 0);
          var row = $('#step-2 table tr').filter(function(){
            return $(this).children().length && $(this).children().first().text().trim().toUpperCase().indexOf(m.label) === 0;
          }).first();
          var unit = row.length ? parseNum(row.children().eq(1).text() || row.children().eq(1).html() || 0) : 0;
          var line = qty * unit;
          fd.append('delivery['+m.key+'][qty]', qty);
          fd.append('delivery['+m.key+'][unit_price]', unit);
          fd.append('delivery['+m.key+'][line_total]', fmt2(line));
        });

        // other named inputs
        var named = ['list-price','margin-markup','hardware-qty','price-buffer','phone-call-buffer','dba-surcharge'];
        $.each(named, function(_, id){
          var node = $('#'+id);
          if (!node.length) return;
          fd.append(node.attr('name') || id, node.val());
        });

        fd.append('final_total', state.final ? state.final.toString() : '0');

        var $btn = $('#save-quote');
        if ($btn.length){ $btn.prop('disabled', true); var oldLabel = $btn.text(); $btn.text('Saving...'); }

        fetch($form.attr('action'), {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': $('input[name="_token"]').val(),
            'Accept': 'application/json'
          },
          body: fd
        }).then(function(r){ return r.json().catch(function(){ return {}; }); })
          .then(function(json){
            if ($btn && $btn.length){ $btn.prop('disabled', false); $btn.text(oldLabel); }
            if (json && json.success){
              alert(json.message || 'Saved successfully');
              if (json.redirect) window.location.href = json.redirect;
              else window.location.reload();
            } else {
              alert((json && json.message) ? json.message : 'Save failed');
              console.error('Save response:', json);
            }
          }).catch(function(err){
            if ($btn && $btn.length){ $btn.prop('disabled', false); $btn.text(oldLabel); }
            alert('Save failed (network or server error)');
            console.error(err);
          });
      });
    }

    // initial recalc
    recalcAll();
  });
</script>
@endpush

