@extends('layouts.admin')

@section('title', 'Quote — Multi Step')

@push('css')
@endpush

@section('content')

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <x-header :export-url="null" :create-url="route('admin.quotes.create')" export-label="Export Quote" create-label="New Quote" />

        <!-- Content -->
        <div class="content bg-content">
            <div class="welcome-container card" id="welcome-panel">
                <div class="quote-type">
                    <div class="icon"></div>
                    <h1 class="title">Welcome to <br><span class="brand">The Stone Cobblers</span></h1>
                    <p class="description">Transform your outdoor space with our premium stone cobbling services. Let's gather some details to provide you with a personalized quote.</p>

                    <div class="quote-options">
                        <div>
                            <h3 style="margin:0 0 12px 0">Select Project</h3>
                            <div class="selector-group">
                                <select id="project-select" name="project_id" class="custom-select" data-placeholder="Select Project">
                                    <option></option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <br>
                        <h3 style="margin:0 0 12px 0">Select Quote Type</h3>
                        <div class="checkbox-group" id="quote-types">
                            <div class="checkbox-item" id="chk-kitchen" data-value="kitchen" onclick="toggleCheckbox('kitchen')">
                                <input type="checkbox" id="kitchen-quote" name="quote-type-kitchen" value="kitchen" />
                                <label for="kitchen-quote">Kitchen Quote</label>
                            </div>
                            <div class="checkbox-item" id="chk-vanity" data-value="vanity" onclick="toggleCheckbox('vanity')">
                                <input type="checkbox" id="vanity-quote" name="quote-type-vanity" value="vanity" />
                                <label for="vanity-quote">Vanity Quote</label>
                            </div>
                        </div>
                    </div>

                    <button class="btn theme" id="begin-btn" onclick="beginQuote()">Let's Begin <span style="margin-left:8px">→</span></button>
                    <div class="time-estimate">Takes about 3 minutes to complete</div>
                    <div class="error-message" id="welcome-error" style="display:none;color:#c62828;margin-top:8px">
                        Please select at least one quote type and a project to continue.
                    </div>
                </div>
            </div>

            <!-- Multi-step form -->
            <form id="multi-step-form" class="hidden quote-details" method="POST" action="{{ route('admin.quotes.store') }}">
                @csrf

                <!-- STEP 1: Kitchen Top -->
                <div class="container step" data-step="1" id="step-1">
                    <div class="header-row">
                        <h2 class="title">Kitchen Top</h2>
                        <div class="header-row__right">
                            <div class="title-label">Accumulative Cost Total:</div>
                            <div class="title-span" id="header-total-1">$ -</div>
                        </div>
                    </div>

                    <table class="table">
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
                            @forelse ($KITCHEN_TOP as $project => $cost)
                                @php
                                    $formattedCost = number_format($cost, 4, '.', '');
                                    $displayCost = number_format($cost, 2);
                                    $isTaxed = in_array($project, ['Kitchen - Sq Ft','Undermount Sink','small oval sink']);
                                @endphp

                                <tr data-name="{{ $project }}">
                                    <td>{{ $project }}</td>
                                    <td class="alpha-fill">{{ $project === 'Kitchen - Sq Ft' ? 'alpha fill' : '' }}</td>
                                    <td>
                                        <input type="number" name="kitchen[qty][]" class="qty-input num-fill kitchen-qty" placeholder="0" min="0" step="0.01" value="{{ old('kitchen.qty.' . $loop->index, 0) }}">
                                    </td>
                                    <td class="cost-value" data-cost="{{ $formattedCost }}">
                                        ${{ $displayCost }}
                                        <input type="hidden" name="kitchen[name][]" value="{{ $project }}">
                                        <input type="hidden" name="kitchen[unit_price][]" value="{{ $formattedCost }}">
                                    </td>
                                    <td class="line-total empty-value">$ -</td>
                                    <td class="taxed-t">{{ $isTaxed ? 'T' : '' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted">No Kitchen Top items found.</td></tr>
                            @endforelse
                        </tbody>

                        <tfoot>
                            <tr>
                                <td colspan="4" style="text-align:right;font-weight:700">Total:</td>
                                <td id="grand-total-1" class="empty-value">$ -</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="nav-footer">
                        <button type="button" id="prev-tab-1" class="btn secondary" onclick="prevStep(1)" disabled><span>←</span> Previous</button>
                        <div class="steps-indicator">Step 1 of 3</div>
                        <button type="button" id="next-tab-1" class="btn theme" data-current="1">Next <span>→</span></button>
                    </div>
                </div>

                <!-- STEP 2: Cabinet Manufacturer -->
                <div class="container step hidden" data-step="2" id="step-2">
                    <div class="header-row">
                        <h2 class="title">Kitchen Cabinet</h2>
                        <div class="header-row__right">
                            <div class="title-label">Accumulative Cost Total:</div>
                            <div class="title-span" id="header-total-2">$ -</div>
                        </div>
                    </div>

                    <div class="table-container">
                        <!-- CABINET MANUFACTURER -->
                        <div style="margin-bottom: 30px;">
                            <h3 style="background-color: #fff3cd; padding:10px; margin:0; border-bottom:1px solid #e0e0e0;">CABINET MANUFACTURER</h3>

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th style="text-align:left;width:40%;">Manufacturer</th>
                                        <th style="text-align:right;width:20%;">Unit Price</th>
                                        <th style="text-align:center;width:20%;">Qty</th>
                                        <th style="text-align:right;width:20%;">Line Total</th>
                                    </tr>
                                </thead>

                                <tbody id="manufacturer-rows">
                                    @forelse ($KITCHEN_MANUFACTURER as $mfg => $cost)
                                        @php $formattedUnit = (float)$cost; @endphp
                                        <tr data-name="{{ $mfg }}">
                                            <td style="padding:8px;border:1px solid #e0e0e0;">
                                                {{ $mfg }}
                                                <input type="hidden" name="manufacturer[name][]" value="{{ $mfg }}">
                                            </td>

                                            <td style="padding:8px;border:1px solid #e0e0e0;text-align:right;">
                                                {{ number_format($formattedUnit, (round($formattedUnit,2) != $formattedUnit ? 4 : 2), '.', '') }}
                                                <input type="hidden" name="manufacturer[unit_price][]" value="{{ number_format($formattedUnit, (round($formattedUnit,2) != $formattedUnit ? 4 : 2), '.', '') }}">
                                            </td>

                                            <td style="padding:8px;border:1px solid #e0e0e0;text-align:center;">
                                                <input type="number" name="manufacturer[qty][]" class="qty-input manufacturer-qty" min="0" step="0.01" value="{{ old('manufacturer.qty.' . $loop->index, 0) }}" style="width:100%;">
                                            </td>

                                            <td style="padding:8px;border:1px solid #e0e0e0;text-align:right;" class="manufacturer-line empty-value">
                                                $ -
                                                <input type="hidden" name="manufacturer[line_total][]" class="manufacturer-line-hidden" value="0">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" style="text-align:center;color:#999;">No manufacturer data found.</td></tr>
                                    @endforelse

                                    <!-- subtotal -->
                                    <tr>
                                        <td colspan="3" style="padding:8px;border:1px solid #e0e0e0;text-align:center;font-weight:700">=</td>
                                        <td id="manufacturer-total" style="padding:8px;border:1px solid #e0e0e0;text-align:right;background:#e8f5e8">$ -</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Margin Markup -->
                        <div style="margin-bottom:30px;">
                            <h3 style="background-color:#f5f5f5;padding:10px;margin:0;border-bottom:1px solid #e0e0e0;">MARGIN MARKUP</h3>

                            <table class="table" style="margin-bottom:20px;width:100%;">
                                <thead>
                                    <tr>
                                        <th style="width:55%;padding:8px;border:1px solid #e0e0e0;text-align:left;">DESCRIPTION</th>
                                        <th style="width:20%;padding:8px;border:1px solid #e0e0e0;text-align:center;">MULTIPLIER</th>
                                        <th style="width:25%;padding:8px;border:1px solid #e0e0e0;text-align:right;">RESULT</th>
                                    </tr>
                                </thead>

                                <tbody id="margin-markup-rows">
                                    @forelse($KITCHEN_MARGIN_MARKUP as $project => $cost)
                                        @php
                                            $slug = Str::slug($project, '_');
                                            $mult = (float) $cost;
                                        @endphp

                                        <tr data-key="{{ $slug }}">
                                            <td style="padding:8px;border:1px solid #e0e0e0;">
                                                {{ $project }}
                                                <input type="hidden" name="margin[{{ $slug }}][name]" value="{{ $project }}">
                                            </td>

                                            <td style="padding:8px;border:1px solid #e0e0e0;text-align:center;">
                                                <input type="number" name="margin[{{ $slug }}][value]" class="qty-input margin-input" step="0.01" min="0" id="margin-{{ $slug }}" value="{{ old('margin.' . $slug . '.value', number_format($mult,2,'.','')) }}" style="width:100%;text-align:center;">
                                            </td>

                                            <td class="markup-result" id="markup-{{ $slug }}" style="padding:8px;border:1px solid #e0e0e0;text-align:right;">
                                                {{ number_format($mult * 100, 2, '.', '') }}%
                                                <input type="hidden" name="margin[{{ $slug }}][result]" value="{{ number_format($mult * 100, 2, '.', '') }}">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" style="padding:8px;border:1px solid #e0e0e0;text-align:center;color:#999;">No margin markup configured.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @php
                            $taxRate = isset($KITCHEN_MARGIN_MARKUP['TAX_RATE']) ? (float) $KITCHEN_MARGIN_MARKUP['TAX_RATE'] : 0;
                        @endphp

                        <!-- ===== BUFFER SECTION ===== -->
                        @php
                            $KITCHEN_BUFFER = $KITCHEN_BUFFER ?? [];
                            $preferredOrder = ['TSC BUFFER','HARDWARE QUANTITY','PRICE CHANGE BUFFER','MORE THAN 1 PHONE CALL/DAY'];
                            $rows = [];
                            foreach($preferredOrder as $k) if(array_key_exists($k, $KITCHEN_BUFFER)) $rows[$k] = (float)$KITCHEN_BUFFER[$k];
                            foreach($KITCHEN_BUFFER as $proj => $val) if($proj !== 'TAX_RATE' && !isset($rows[$proj])) $rows[$proj] = (float)$val;
                        @endphp

                        <div style="margin-bottom:30px;">
                            <h3 style="background:#fff3cd;padding:10px;margin:0;border-bottom:1px solid #e0e0e0;">BUFFER & TOTALS</h3>

                            <table class="table" style="width:100%;margin-top:8px;">
                                <thead>
                                    <tr>
                                        <th style="width:50%;padding:8px;border:1px solid #e0e0e0;text-align:left;">DESCRIPTION</th>
                                        <th style="width:20%;padding:8px;border:1px solid #e0e0e0;text-align:right;">UNIT</th>
                                        <th style="width:15%;padding:8px;border:1px solid #e0e0e0;text-align:center;background:#fffbf0;">QTY</th>
                                        <th style="width:15%;padding:8px;border:1px solid #e0e0e0;text-align:right;">LINE TOTAL</th>
                                    </tr>
                                </thead>

                                <tbody id="buffer-rows">
                                    @if(empty($rows))
                                        <tr><td colspan="4" style="padding:12px;border:1px solid #e0e0e0;text-align:center;color:#999;">No buffer items configured.</td></tr>
                                    @else
                                        @foreach($rows as $project => $unitVal)
                                            @php
                                                $slug = Str::slug($project, '_');
                                                if(strtoupper($project) === 'TSC BUFFER') $mode = 'fixed';
                                                elseif(strtoupper($project) === 'HARDWARE QUANTITY') $mode = 'qty';
                                                elseif(stripos($project,'PRICE') !== false) $mode = 'input';
                                                elseif(stripos($project,'PHONE') !== false) $mode = 'input';
                                                else $mode = 'input';
                                                $defaultQty = $mode === 'fixed' ? 1 : 0;
                                            @endphp

                                            <tr data-key="{{ $slug }}" data-type="buffer" data-mode="{{ $mode }}" data-unit="{{ number_format($unitVal,2,'.','') }}">
                                                <td style="padding:8px;border:1px solid #e0e0e0;">
                                                    {{ $project }}
                                                    <input type="hidden" name="buffer[{{ $slug }}][name]" value="{{ $project }}">
                                                </td>

                                                <td class="unit-cell" style="padding:8px;border:1px solid #e0e0e0;text-align:right;">
                                                    {{ $mode === 'input' ? '-' : number_format($unitVal, 2, '.', '') }}
                                                    <input type="hidden" name="buffer[{{ $slug }}][unit]" value="{{ $mode === 'input' ? '0.00' : number_format($unitVal,2,'.','') }}">
                                                </td>

                                                <td style="padding:8px;border:1px solid #e0e0e0;background:#fffbf0;text-align:center;">
                                                    <input type="number" name="buffer[{{ $slug }}][qty]" class="qty-input buffer-qty" step="0.01" min="0" value="{{ old('buffer.' . $slug . '.qty', $defaultQty) }}" style="width:100%;" />
                                                </td>

                                                <td class="line-total" style="padding:8px;border:1px solid #e0e0e0;text-align:right;">
                                                    0.00
                                                    <input type="hidden" name="buffer[{{ $slug }}][line_total]" class="line-hidden" value="0.00">
                                                </td>
                                            </tr>
                                        @endforeach

                                        <tr>
                                            <td colspan="3" style="padding:8px;border:1px solid #e0e0e0;background:#e8f5e8;font-weight:700;text-align:right;">TOTAL RETAIL</td>
                                            <td id="total-retail" style="padding:8px;border:1px solid #e0e0e0;text-align:right;background:#e8f5e8;">0.00</td>
                                        </tr>

                                        <tr>
                                            <td colspan="3" style="padding:8px;border:1px solid #e0e0e0;text-align:right;">TAX</td>
                                            <td id="tax-amount" style="padding:8px;border:1px solid #e0e0e0;text-align:right;">0.00</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- DELIVERY -->
                        @php $KITCHEN_DELIVERY = $KITCHEN_DELIVERY ?? []; @endphp
                        <div style="margin-bottom: 30px;">
                            <h3 style="background-color:#fff3cd;padding:10px;margin:0;border-bottom:1px solid #e0e0e0;">DELIVERY</h3>

                            <table class="table" style="margin-bottom:20px;width:100%;">
                                <thead>
                                    <tr>
                                        <th style="width:30%;padding:8px;border:1px solid #e0e0e0;text-align:left;">Description</th>
                                        <th style="width:20%;padding:8px;border:1px solid #e0e0e0;text-align:right;">Unit Price</th>
                                        <th style="width:20%;padding:8px;border:1px solid #e0e0e0;background:#f5f5f5;text-align:center;">Qty</th>
                                        <th style="width:30%;padding:8px;border:1px solid #e0e0e0;text-align:right;">Line Total</th>
                                    </tr>
                                </thead>

                                <tbody id="delivery-rows">
                                    @forelse ($KITCHEN_DELIVERY as $project => $cost)
                                        @php
                                            $unit = number_format((float)$cost, (round((float)$cost,2) != (float)$cost ? 4 : 2), '.', '');
                                            $id = 'delivery_' . Str::slug($project, '_');
                                        @endphp

                                        <tr data-key="{{ $id }}" data-project="{{ $project }}">
                                            <td style="padding:8px;border:1px solid #e0e0e0;">
                                                {{ $project }}
                                                <input type="hidden" name="delivery[{{ $id }}][name]" value="{{ $project }}">
                                            </td>

                                            <td class="delivery-unit" style="padding:8px;border:1px solid #e0e0e0;text-align:right;" data-unit="{{ $unit }}">
                                                {{ $unit }}
                                                <input type="hidden" name="delivery[{{ $id }}][unit_price]" value="{{ $unit }}">
                                            </td>

                                            <td style="padding:8px;border:1px solid #e0e0e0;background:#f5f5f5;text-align:center;">
                                                <input type="number" id="{{ $id }}_qty" name="delivery[{{ $id }}][qty]" class="qty-input delivery-qty" placeholder="" min="0" step="0.01" value="{{ old('delivery.' . $id . '.qty', 0) }}" style="width:100%;">
                                            </td>

                                            <td id="{{ $id }}_total" class="delivery-line" style="padding:8px;border:1px solid #e0e0e0;text-align:right;">
                                                0.00
                                                <input type="hidden" name="delivery[{{ $id }}][line_total]" value="0" class="delivery-line-hidden">
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" style="text-align:center;color:#999;padding:12px;border:1px solid #e0e0e0;">No delivery items configured.</td></tr>
                                    @endforelse

                                    <!-- TOTAL -->
                                    <tr>
                                        <td colspan="3" style="padding:8px;border:1px solid #e0e0e0;text-align:right;font-weight:bold;background:#e8f5e8;">TOTAL</td>
                                        <td id="delivery-total" style="padding:8px;border:1px solid #e0e0e0;text-align:right;background:#e8f5e8;">0.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Final Surcharge -->
                        <div>
                            <table class="table" style="width:100%;margin-bottom:20px;">
                                <tr>
                                    <td style="width:30%;padding:8px;border:1px solid #e0e0e0;">DBA fee/fuel surcharge</td>
                                    <td style="width:20%;padding:8px;border:1px solid #e0e0e0;">
                                        <input type="number" id="dba-surcharge" name="dba_surcharge" class="qty-input" placeholder="0.03" min="0" step="0.01" value="{{ old('dba_surcharge', 0.03) }}">
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

                    <div class="nav-footer">
                        <button type="button" id="prev-tab-2" class="btn secondary" onclick="prevStep(2)"><span>←</span> Previous</button>
                        <div class="steps-indicator">Step 2 of 3</div>
                        <button type="button" id="next-tab-2" class="btn theme" data-current="2">Next <span>→</span></button>
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

                        <div class="summary-row total">
                            <div>Total</div>
                            <div id="final-total" style="font-weight:700">$ -</div>
                        </div>
                    </div>

                    <div class="nav-footer">
                        <button type="button" class="btn secondary" onclick="prevStep(3)"><span>←</span> Previous</button>
                        <div class="steps-indicator">Step 3 of 3</div>
                        <button type="submit" class="btn theme" id="save-quote">Save & Finish</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        jQuery(function($) {
            // Utilities
            function parseNum(v) {
                if (v === null || v === undefined) return 0;
                v = String(v).replace(/\$/g, '').replace(/,/g, '').trim();
                if (v === '') return 0;
                if (/^\(.*\)$/.test(v)) v = '-' + v.replace(/[()]/g, '');
                var n = parseFloat(v);
                return isNaN(n) ? 0 : n;
            }
            function fmt2(n) { return (isNaN(n) ? 0 : n).toFixed(2); }
            function fmt4(n) { return (isNaN(n) ? 0 : n).toFixed(4); }

            // Welcome toggles
            var selected = { kitchen: false, vanity: false };
            $('#kitchen-quote').prop('checked', selected.kitchen);
            $('#vanity-quote').prop('checked', selected.vanity);

            window.toggleCheckbox = function(key) {
                selected[key] = !selected[key];
                var chk = $('#' + key + '-quote');
                if (chk.length) chk.prop('checked', selected[key]);
                var box = $('#chk-' + key);
                if (box.length) {
                    if (selected[key]) box.addClass('selected'); else box.removeClass('selected');
                }
            };

            window.beginQuote = function() {
                var projectId = $('#project-select').val();
                if (!selected.kitchen && !selected.vanity) { $('#welcome-error').text('Please select at least one quote type to continue.').show(); return; }
                if (!projectId) { $('#welcome-error').text('Please select a project to continue.').show(); return; }
                $('#welcome-error').hide();
                $('#welcome-panel').addClass('hidden');
                $('#multi-step-form').removeClass('hidden');
                showStep(1);
            };

            // Navigation
            function showStep(step) {
                $('.step').addClass('hidden');
                $('.step[data-step="' + step + '"]').removeClass('hidden');
                recalcAll();
            }
            window.nextStep = function(current) {
                if (!validateStep(current)) return;
                var next = current + 1;
                showStep(next);
                if (next === 3) buildSummary();
            };
            window.prevStep = function(current) {
                var prev = current - 1;
                if (prev < 1) return;
                showStep(prev);
            };

            function validateStep(step) {
                if (step === 1) {
                    var ok = true;
                    $('#kitchen-rows input.kitchen-qty').each(function() {
                        var v = parseNum($(this).val());
                        if (v < 0) { $(this).addClass('invalid'); ok = false; } else $(this).removeClass('invalid');
                    });
                    if (!ok) alert('Please correct kitchen quantities (numbers >= 0).');
                    return ok;
                }
                if (step === 2) {
                    var ok = true;
                    $('#manufacturer-rows input.manufacturer-qty').each(function() {
                        var v = parseNum($(this).val());
                        if (v < 0) { $(this).addClass('invalid'); ok = false; } else $(this).removeClass('invalid');
                    });
                    if (!ok) alert('Please correct manufacturer quantities (numbers >= 0).');
                    return ok;
                }
                return true;
            }

            // Recalculations
            function recalcKitchen() {
                var grand = 0;
                $('#kitchen-rows tr[data-name]').each(function() {
                    var $tr = $(this);
                    var qty = parseNum($tr.find('input[name="kitchen[qty][]"]').val() || 0);
                    var unit = parseNum($tr.find('input[name="kitchen[unit_price][]"]').val() || $tr.find('.cost-value').data('cost') || 0);
                    var line = qty * unit;
                    grand += line;
                    var $lineCell = $tr.find('.line-total');
                    if (line > 0) $lineCell.text('$' + fmt2(line)).removeClass('empty-value'); else $lineCell.text('$ -').addClass('empty-value');
                });
                $('#grand-total-1').text(grand > 0 ? '$' + fmt2(grand) : '$ -');
                $('#header-total-1').text(grand > 0 ? '$' + fmt2(grand) : '$ -');
                return grand;
            }

            function recalcManufacturer() {
                var total = 0;
                $('#manufacturer-rows tr[data-name]').each(function() {
                    var $tr = $(this);
                    var qty = parseNum($tr.find('input[name="manufacturer[qty][]"]').val() || 0);
                    var unit = parseNum($tr.find('input[name="manufacturer[unit_price][]"]').val() || $tr.find('td').eq(1).text() || 0);
                    var line = qty * unit;
                    total += line;
                    var $display = $tr.find('.manufacturer-line');
                    var $hiddenLine = $tr.find('input.manufacturer-line-hidden[name="manufacturer[line_total][]"]');
                    if (line > 0) {
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

            function recalcDelivery() {
                var deliveryTotal = 0;
                $('#delivery-rows tr[data-key]').each(function() {
                    var $tr = $(this);
                    var qty = parseNum($tr.find('.delivery-qty').val() || 0);
                    var unit = parseNum($tr.find('.delivery-unit').data('unit') || $tr.find('input[name$="[unit_price]"]').val() || 0);
                    var line = qty * unit;
                    deliveryTotal += line;
                    var idTotal = $tr.attr('id') || ($tr.data('key') + '_total');
                    // Update visible cell
                    $tr.find('.delivery-line').first().contents().filter(function(){ return this.nodeType===3; }).first().replaceWith(fmt2(line));
                    $tr.find('.delivery-line-hidden').val(fmt2(line));
                });

                $('#delivery-total').text(deliveryTotal > 0 ? '$' + fmt2(deliveryTotal) : '0.00');
                return deliveryTotal;
            }

            function recalcBufferRows() {
                var total = 0;
                $('#buffer-rows tr[data-type="buffer"]').each(function(){
                    var $tr = $(this);
                    var mode = $tr.data('mode');
                    var unit = parseNum($tr.data('unit')) || 0;
                    var qty = parseNum($tr.find('.buffer-qty').val()) || 0;
                    var line = 0;
                    if(mode === 'fixed'){ if(qty <= 0) qty = 1; line = unit * qty; }
                    else if(mode === 'qty'){ line = unit * qty; }
                    else { line = qty; } // input absolute
                    total += line;
                    $tr.find('.line-total').first().contents().filter(function(){ return this.nodeType===3; }).first().replaceWith(fmt2(line));
                    $tr.find('.line-hidden').val(fmt2(line));
                });
                $('#total-retail').text(total ? ('$' + fmt2(total)) : '0.00');
                var taxAmount = total * (parseFloat('{{ $taxRate ?? 0 }}') || 0);
                $('#tax-amount').text(fmt2(taxAmount));
                return total;
            }

            function recalcMargins() {
                $('#margin-markup-rows tr[data-key]').each(function(){
                    var $tr = $(this);
                    var $input = $tr.find('.margin-input');
                    var mult = parseNum($input.val()) || 0;
                    var pct = mult * 100;
                    $tr.find('.markup-result').first().contents().filter(function(){ return this.nodeType===3; }).first().replaceWith(fmt2(pct) + '%');
                    $tr.find('input[name$="[result]"]').val(fmt2(pct));
                });
            }

            function recalcAll() {
                var kitchenSum = recalcKitchen();
                var manufacturerSum = recalcManufacturer();

                // multiplier logic: if you have a list price input, you can multiply here - we keep it flexible
                var listPriceValue = parseNum($('#list-price').val() || 0);
                var multiplierResult = listPriceValue * manufacturerSum;
                if ($('#multiplier-result').length) $('#multiplier-result').text(multiplierResult ? fmt4(multiplierResult) : '0.000');
                if ($('#cost-total').length) $('#cost-total').text(multiplierResult ? ('$' + fmt2(multiplierResult)) : '$0.00');

                var step2Subtotal = manufacturerSum + multiplierResult;
                if ($('#step2-final-total').length) $('#step2-final-total').text(step2Subtotal ? ('$' + fmt4(step2Subtotal)) : '$0.00');
                if ($('#step2-final-result').length) $('#step2-final-result').text(step2Subtotal ? ('$' + fmt4(step2Subtotal)) : '$0.00');

                var deliverySum = recalcDelivery();
                recalcMargins();
                var bufferSum = recalcBufferRows();

                var priceBuffer = parseNum($('#price-buffer').val() || 0);
                var phoneBuffer = parseNum($('#phone-call-buffer').val() || 0);
                var hardwareQty = parseNum($('#hardware-qty').val() || 0);
                var dba = parseNum($('#dba-surcharge').val() || 0);

                // Note: our bufferSum already includes price/phone/hardware lines if they exist in DB iteration.
                // To avoid double counting, ensure price/phone/hardware are part of buffer rows or use these inputs separately.
                // Here we add explicit inputs as fallback:
                var totalRetail = step2Subtotal + deliverySum + (bufferSum || 0);
                $('#total-retail').text(totalRetail ? ('$' + fmt2(totalRetail)) : '0.00');

                var taxAmount = 0; // if you want tax applied elsewhere, compute it here
                $('#tax-amount').text(fmt2(taxAmount));

                var step2FinalWithDba = step2Subtotal + dba;
                $('#step2-final-result').text(step2FinalWithDba ? ('$' + fmt4(step2FinalWithDba)) : '$0.00');
                $('#step2-final-total').text(step2FinalWithDba ? ('$' + fmt4(step2FinalWithDba)) : '$0.00');

                var final = kitchenSum + step2Subtotal + deliverySum + (bufferSum || 0) + dba + taxAmount;
                $('#final-total').text(final ? ('$' + fmt2(final)) : '$ -');
                $('#header-total-3').text(final ? ('$' + fmt2(final)) : '$ -');

                return {
                    kitchenSum: kitchenSum,
                    manufacturerSum: manufacturerSum,
                    multiplierResult: multiplierResult,
                    step2Subtotal: step2Subtotal,
                    deliverySum: deliverySum,
                    bufferSum: bufferSum,
                    final: final
                };
            }

            // Event delegation for input changes
            $(document).on('input change', [
                '#kitchen-rows input.kitchen-qty',
                '#manufacturer-rows input.manufacturer-qty',
                '#list-price',
                '#price-buffer',
                '#phone-call-buffer',
                '#hardware-qty',
                '#delivery-rows input.delivery-qty',
                '#dba-surcharge',
                '.margin-input',
                '.buffer-qty'
            ].join(','), function() {
                recalcAll();
            });

            // Next buttons
            $('#next-tab-1').on('click', function() { nextStep(1); });
            $('#next-tab-2').on('click', function() { nextStep(2); });

            // Build summary
            function buildSummary() {
                var $list = $('#summary-list');
                if (!$list.length) return;
                $list.empty();

                $('#kitchen-rows tr[data-name]').each(function() {
                    var $tr = $(this);
                    var name = $tr.data('name');
                    var qty = parseNum($tr.find('input[name="kitchen[qty][]"]').val() || 0);
                    var unit = parseNum($tr.find('input[name="kitchen[unit_price][]"]').val() || $tr.find('.cost-value').data('cost') || 0);
                    var line = qty * unit;
                    if (line > 0) $list.append('<div class="summary-row"><div>' + name + ' × ' + qty + '</div><div>$' + fmt2(line) + '</div></div>');
                });

                $('#manufacturer-rows tr[data-name]').each(function(){
                    var $tr = $(this);
                    var name = $tr.data('name');
                    var qty = parseNum($tr.find('input[name="manufacturer[qty][]"]').val() || 0);
                    var unit = parseNum($tr.find('input[name="manufacturer[unit_price][]"]').val() || $tr.find('td').eq(1).text() || 0);
                    var line = qty * unit;
                    if(line > 0) $list.append('<div class="summary-row"><div>' + name + ' × ' + qty + '</div><div>$' + fmt4(line) + '</div></div>');
                });

                $('#delivery-rows tr[data-key]').each(function(){
                    var $tr = $(this);
                    var label = $tr.find('td').first().text().trim();
                    var qty = parseNum($tr.find('.delivery-qty').val() || 0);
                    var unit = parseNum($tr.find('.delivery-unit').data('unit') || 0);
                    var line = qty * unit;
                    if (line > 0) $list.append('<div class="summary-row"><div>' + label + ' × ' + qty + '</div><div>$' + fmt2(line) + '</div></div>');
                });

                var state = recalcAll();
                $('#final-total').text(state.final ? ('$' + fmt2(state.final)) : '$ -');
                $('#header-total-3').text(state.final ? ('$' + fmt2(state.final)) : '$ -');
            }

            // Form submit via AJAX
            var $form = $('#multi-step-form');
            if ($form.length) {
                $form.on('submit', function(e) {
                    e.preventDefault();
                    var state = recalcAll();
                    if (!state.final || state.final <= 0) {
                        if (!confirm('Final total is $0. Do you want to submit anyway?')) return;
                    }

                    var fd = new FormData();
                    fd.append('project_id', $('#project-select').val() || '');
                    // kitchen
                    $('#kitchen-rows tr[data-name]').each(function() {
                        var $tr = $(this);
                        fd.append('kitchen[name][]', $tr.data('name'));
                        fd.append('kitchen[qty][]', parseNum($tr.find('input[name="kitchen[qty][]"]').val() || 0));
                        fd.append('kitchen[unit_price][]', parseNum($tr.find('input[name="kitchen[unit_price][]"]').val() || $tr.find('.cost-value').data('cost') || 0));
                    });
                    // manufacturer
                    $('#manufacturer-rows tr[data-name]').each(function() {
                        var $tr = $(this);
                        fd.append('manufacturer[name][]', $tr.data('name'));
                        fd.append('manufacturer[qty][]', parseNum($tr.find('input[name="manufacturer[qty][]"]').val() || 0));
                        fd.append('manufacturer[unit_price][]', parseNum($tr.find('input[name="manufacturer[unit_price][]"]').val() || $tr.find('td').eq(1).text() || 0));
                        fd.append('manufacturer[line_total][]', parseNum($tr.find('input.manufacturer-line-hidden').val() || 0));
                    });
                    // deliveries
                    $('#delivery-rows tr[data-key]').each(function(){
                        var key = $(this).data('key');
                        var qty = parseNum($(this).find('.delivery-qty').val() || 0);
                        var unit = parseNum($(this).find('.delivery-unit').data('unit') || 0);
                        var line = qty * unit;
                        fd.append('delivery[' + key + '][qty]', qty);
                        fd.append('delivery[' + key + '][unit_price]', unit);
                        fd.append('delivery[' + key + '][line_total]', fmt2(line));
                    });
                    // buffers
                    $('#buffer-rows tr[data-key]').each(function(){
                        var key = $(this).data('key');
                        var qty = parseNum($(this).find('.buffer-qty').val() || 0);
                        var unit = parseNum($(this).data('unit') || 0);
                        var line = parseNum($(this).find('.line-hidden').val() || 0);
                        fd.append('buffer[' + key + '][qty]', qty);
                        fd.append('buffer[' + key + '][unit]', unit);
                        fd.append('buffer[' + key + '][line_total]', fmt2(line));
                    });

                    // simple named inputs (if present)
                    var named = ['list-price','margin-markup','hardware-qty','price-buffer','phone-call-buffer','dba-surcharge'];
                    $.each(named, function(_, id) {
                        var node = $('#' + id);
                        if (!node.length) return;
                        var serverName = node.attr('name') || id.replace(/-/g, '_');
                        fd.append(serverName, node.val());
                    });

                    fd.append('final_total', state.final ? state.final.toString() : '0');
                    fd.append('is_kitchen', selected.kitchen ? '1' : '0');
                    fd.append('is_vanity', selected.vanity ? '1' : '0');

                    var $btn = $('#save-quote');
                    var oldLabel = $btn.text();
                    $btn.prop('disabled', true).text('Saving...');

                    fetch($form.attr('action'), {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val(), 'Accept': 'application/json' },
                        body: fd
                    }).then(function(r){ return r.json().catch(()=>({})); }).then(function(json){
                        $btn.prop('disabled', false).text(oldLabel);
                        if (json && json.success) {
                            if (json.redirect) window.location.href = json.redirect;
                            else window.location.reload();
                        } else {
                            alert((json && json.message) ? json.message : 'Save failed');
                            console.error('Save response:', json);
                        }
                    }).catch(function(err){
                        $btn.prop('disabled', false).text(oldLabel);
                        alert('Save failed (network or server error)');
                        console.error(err);
                    });
                });
            }

            // wire next buttons to functions
            $('#next-tab-1').on('click', function(){ nextStep(1); });
            $('#next-tab-2').on('click', function(){ nextStep(2); });

            // initial calc
            recalcAll();
        });
    </script>
@endpush
