<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Combined Quote {{ $kitchenQuote->quote_number ?? '' }} / {{ $vanityQuote->quote_number ?? '' }}</title>

    <style>
        /* --- Page margins --- */
        @page {
            margin-top: 45mm;
            margin-right: 15mm;
            margin-bottom: 20mm;
            margin-left: 15mm;
            /* Header sits in top margin (30mm height), footer in bottom margin (12mm height) */
        }

        /* Basic typography */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* --- Fixed header for all pages --- */
        .pdf-header {
            position: fixed;
            top: -40mm;
            left: 0;
            right: 0;
            height: 35mm;
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            border-bottom: 1px solid #000;
            background: #fff;
            z-index: 1000;
        }

        .header-wrapper {
            width: 100%;
            height: 100%;
            padding: 5mm 0;
            box-sizing: border-box;
        }

        .header-wrapper::after {
            content: "";
            display: table;
            clear: both;
        }

        .header-left {
            float: left;
            width: 40%;
            padding-right: 15px;
            box-sizing: border-box;
        }

        .header-left .logo-box {
            border: 2px solid #000;
            padding: 6px 8px;
            display: inline-block;
            max-width: 200px;
        }

        .header-left .logo-text {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: #000;
            line-height: 1;
            margin-bottom: 2px;
        }

        .header-left .company-name {
            font-size: 13px;
            font-weight: 700;
            color: #000;
            line-height: 1.3;
            text-transform: uppercase;
        }

        .header-right {
            float: right;
            width: 55%;
            text-align: right;
            padding-left: 15px;
            margin-top: -10px;
            box-sizing: border-box;
        }

        .header-right .company-title {
            font-size: 14px;
            font-weight: 700;
            color: #000;
            margin-bottom: 5px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .header-right .address-block {
            font-size: 10px;
            color: #000;
            line-height: 1.4;
            margin-bottom: 4px;
        }

        .header-right .address-block strong {
            font-weight: 600;
        }

        .header-right .contact-block {
            font-size: 10px;
            color: #000;
            line-height: 1.4;
        }

        .header-right .contact-block strong {
            font-weight: 600;
        }

        .header-right .link {
            color: #0066cc;
            text-decoration: none;
        }

        .pdf-footer {
            position: fixed;
            bottom: -15mm;
            left: 0;
            right: 0;
            height: 12mm;
            text-align: center;
            font-size: 10px;
            color: #666;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            background: #fff;
            z-index: 1000;
        }
        
        .pdf-footer .footer-text {
            font-size: 9px;
            color: #999;
            margin-top: 3px;
        }

        .content {
            position: relative;
            margin: 0;
            padding-top: 2mm;
            padding-bottom: 2mm;
            padding-left: 0;
            padding-right: 0;
            box-sizing: border-box;
        }

        .quote-meta {
            margin: 0 0 6px 0;
            padding: 0;
            font-size: 12px;
            width: 100%;
            border-collapse: collapse;
        }

        .quote-meta td {
            padding: 4px 0;
            vertical-align: top;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        table.items th,
        table.items td {
            border: 1px solid #333;
            padding: 6px 8px;
            font-size: 12px;
            vertical-align: middle;
        }

        table.items th {
            background: #f2f2f2;
            text-align: left;
        }

        .section-title {
            background: #333;
            color: #fff;
            font-weight: bold;
            padding: 4px;
            margin-top: 14px;
            font-size: 13px;
        }

        .quote-type-title {
            background: #555;
            color: #fff;
            font-weight: bold;
            padding: 6px 8px;
            margin-top: 20px;
            font-size: 14px;
            text-transform: uppercase;
        }

        .item-thumb {
            max-width: 80px;
            max-height: 60px;
            display: block;
            margin: 0 auto 6px;
        }

        .col-center {
            text-align: center;
        }

        .col-right {
            text-align: right;
        }

        .totals td {
            font-weight: bold;
        }

        .signature {
            margin-top: 18px;
        }

        .signature div {
            border-top: 1px solid #333;
            width: 200px;
            margin-top: 20px;
        }

        .highlight {
            background: #e6f0ff;
            padding: 10px;
            margin-top: 12px;
            border: 1px solid #99c2ff;
            text-align: center;
            font-weight: 600;
        }

        tr {
            page-break-inside: avoid;
        }
        
        .no-data-row {
            background: #f9f9f9;
            color: #999;
            font-style: italic;
        }

        .section-title {
            page-break-after: avoid;
        }

        .quote-type-title {
            page-break-after: avoid;
        }

        table.items {
            page-break-inside: auto;
        }

        table.items thead {
            display: table-header-group;
        }

        table.items tfoot {
            display: table-footer-group;
        }

        .signature {
            page-break-inside: avoid;
        }

        .highlight {
            page-break-inside: avoid;
        }

        .combined-totals {
            background: #f0f0f0;
            border: 2px solid #333;
        }
    </style>
</head>

<body>

    <div class="pdf-header" aria-hidden="true">
        <div class="header-wrapper">
            <div class="header-left">
                @if (!empty($companyLogo))
                    <img src="{{ $companyLogo }}" alt="{{ $companyName }}" style="max-width: 180px; max-height: 70px; display: block;">
                @else
                    <div class="logo-box">
                        <div class="logo-text">TSC</div>
                        <div class="company-name">THE<br>STONE<br>COBBLERS</div>
                    </div>
                @endif
            </div>

            <div class="header-right">
                <div class="company-title">{{ strtoupper($companyName) }}</div>
                <div class="address-block">
                    {{ $companyAddress }}, {{ $companyCity }}, {{ $companyState }} {{ $companyZipcode }}
                </div>         
                <div class="contact-block">
                    <div>
                        <strong>Phone:</strong> {{ $companyPhone }}
                    </div>
                    @if($companyEmail)
                        <div><strong>Email:</strong> <span class="link">{{ $companyEmail }}</span></div>
                    @endif
                    @if($companyWebsite)
                        <div><strong>Web:</strong> <span class="link">{{ $companyWebsite }}</span></div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- FOOTER (page numbers added via PHP script at bottom) --}}
    <div class="pdf-footer">
        <div class="footer-text" style="margin-top: 15px;">
            {{ $companyName }} | {{ $companyPhone }} | {{ $companyEmail ?? '' }}
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="content">
        {{-- Date line with underline matching image design --}}
        <div>
            <strong style="font-size: 12px;">Date:</strong> 
            <span style="margin-left: 5px;">{{ optional($kitchenQuote->created_at)->format('m/d/Y') ?? '_______________' }}</span>
        </div>

        <table class="quote-meta">
            <tr>
                <td style="width:50%;">
                    <strong>Kitchen Quote #:</strong> {{ $kitchenQuote->quote_number ?? '—' }}<br>
                    <strong>Vanity Quote #:</strong> {{ $vanityQuote->quote_number ?? '—' }}<br>
                    <strong>Customer:</strong>
                    {{ $kitchenQuote->customer_name ?? $vanityQuote->customer_name ?? (optional($project->customer)->name ?? 'N/A') }}
                </td>
                <td style="width:50%; text-align:right;">
                    <strong>Expires:</strong> {{ optional($kitchenQuote->expires_at ?? $vanityQuote->expires_at)->format('m/d/Y') ?? '—' }}
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <strong>Project:</strong> {{ $kitchenQuote->project_name ?? $vanityQuote->project_name ?? (optional($project)->name ?? 'N/A') }}<br>
                    <strong>Address:</strong>
                    {{ optional(optional($project)->customer)->address ?? 'N/A' }}
                </td>
            </tr>
        </table>

        <p style="font-size: 12px;"><strong>Project Type:</strong> KITCHEN + VANITY (COMBINED QUOTE)</p>

        {{-- ============================================ --}}
        {{-- KITCHEN QUOTE SECTION --}}
        {{-- ============================================ --}}
        <div class="quote-type-title">KITCHEN QUOTE - {{ $kitchenQuote->quote_number ?? 'N/A' }}</div>

        {{-- KITCHEN QUOTE ITEMS --}}
        <div class="section-title">QUOTE ITEMS</div>
        <table class="items">
            <thead>
                <tr>
                    <th style="width:35%;">Item / Description</th>
                    <th style="width:15%;" class="col-center">Qty</th>
                    <th style="width:20%;" class="col-right">Unit Price</th>
                    <th style="width:20%;" class="col-right">Line Total</th>
                    <th style="width:10%;" class="col-center">Taxed</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $kitchenQuoteItems = collect($kitchenItems)->filter(function($item) {
                        return (str_ends_with($item->type, '_TOP') || $item->type === 'KITCHEN_TOP')
                            && (float)$item->qty > 0 
                            && (float)$item->line_total > 0;
                    });
                @endphp
                
                @forelse($kitchenQuoteItems as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td class="col-center">
                            {{ $item->qty > 0 ? rtrim(rtrim(number_format($item->qty, 2, '.', ''), '0'), '.') : '—' }}
                        </td>
                        <td class="col-right">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="col-right">${{ number_format($item->line_total, 2) }}</td>
                        <td class="col-center">{{ !empty($item->is_taxable) ? 'T' : '' }}</td>
                    </tr>
                @empty
                    <tr class="no-data-row">
                        <td colspan="5" class="col-center">No kitchen quote items</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- KITCHEN BOX MANUFACTURERS --}}
        <div class="section-title">BOX MANUFACTURERS</div>
        <table class="items">
            <thead>
                <tr>
                    <th style="width:40%;">Manufacturer Name</th>
                    <th style="width:15%;" class="col-center">Qty</th>
                    <th style="width:20%;" class="col-right">Unit Price</th>
                    <th style="width:25%;" class="col-right">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $kitchenManufacturers = collect($kitchenItems)->filter(function($item) {
                        return (str_ends_with($item->type, '_MANUFACTURER') || $item->type === 'KITCHEN_MANUFACTURER')
                            && (float)$item->qty > 0 
                            && (float)$item->line_total > 0;
                    });
                @endphp
                
                @forelse($kitchenManufacturers as $mfg)
                    <tr>
                        <td>{{ $mfg->name }}</td>
                        <td class="col-center">
                            {{ $mfg->qty > 0 ? rtrim(rtrim(number_format($mfg->qty, 2, '.', ''), '0'), '.') : '—' }}
                        </td>
                        <td class="col-right">${{ number_format($mfg->unit_price, 2) }}</td>
                        <td class="col-right">${{ number_format($mfg->line_total, 2) }}</td>
                    </tr>
                @empty
                    @foreach (['Bertch', 'Mantra', 'CB', '802/USCD FROM 2020', 'KCD/USCD', 'Dura Supreme', 'OMEGA', '20/20 LIST PRICE'] as $mfg)
                        <tr>
                            <td>{{ $mfg }}</td>
                            <td class="col-center">—</td>
                            <td class="col-right">—</td>
                            <td class="col-right">—</td>
                        </tr>
                    @endforeach
                @endforelse
            </tbody>
        </table>

        {{-- KITCHEN MARGIN / MARKUP --}}
        <div class="section-title">MARGIN / MARKUP</div>
        <table class="items">
            <thead>
                <tr>
                    <th style="width:50%;">Description</th>
                    <th style="width:20%;" class="col-center">Multiplier</th>
                    <th style="width:30%;" class="col-right">Result</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $kitchenMargins = collect($kitchenItems)->filter(function($item) {
                        return (str_ends_with($item->type, '_MARGIN_MARKUP') || $item->type === 'KITCHEN_MARGIN_MARKUP')
                            && (float)$item->line_total > 0;
                    });
                @endphp
                
                @forelse($kitchenMargins as $margin)
                    <tr>
                        <td>{{ $margin->name }}</td>
                        <td class="col-center">{{ number_format($margin->unit_price, 2) }}x</td>
                        <td class="col-right">${{ number_format($margin->line_total, 2) }}</td>
                    </tr>
                @empty
                    @foreach ([
                        '1.425 - 1.5 CONTRACTOR 1ST TIME',
                        '1.5 - 1.55 CONTRACTOR',
                        '1.55 - 1.6, 1.66 - 1.7 RETAIL',
                        'TSC BUFFER',
                        'HARDWARE QUANTITY',
                        'MISC ITEMS',
                        '802 FREIGHT SURCHARGE',
                        'PRICE CHANGE BUFFER',
                        'MORE THAN 1 PHONE CALL/DAY'
                    ] as $marginOption)
                        <tr>
                            <td>{{ $marginOption }}</td>
                            <td class="col-center">—</td>
                            <td class="col-right">—</td>
                        </tr>
                    @endforeach
                @endforelse
            </tbody>
        </table>

        {{-- KITCHEN TOTALS --}}
        <table class="items" style="margin-top: 10px;">
            <tfoot>
                <tr class="totals">
                    <td colspan="3" style="text-align: right; font-size: 13px;"><strong>Kitchen Subtotal</strong></td>
                    <td class="col-right" style="font-size: 13px;">${{ number_format($kitchenQuote->subtotal ?? 0, 2) }}</td>
                </tr>
                <tr class="totals">
                    <td colspan="3" style="text-align: right; font-size: 13px;"><strong>Kitchen Tax ({{ ($taxRate * 100) }}%)</strong></td>
                    <td class="col-right" style="font-size: 13px;">${{ number_format($kitchenQuote->tax ?? 0, 2) }}</td>
                </tr>
                @if(($kitchenQuote->discount ?? 0) > 0)
                <tr class="totals">
                    <td colspan="3" style="text-align: right; font-size: 13px;"><strong>Kitchen Discount</strong></td>
                    <td class="col-right" style="font-size: 13px; color: #c00;">-${{ number_format($kitchenQuote->discount ?? 0, 2) }}</td>
                </tr>
                @endif
                <tr class="totals" style="background: #e0e0e0;">
                    <td colspan="3" style="text-align: right; font-size: 14px; font-weight: bold;">KITCHEN TOTAL</td>
                    <td class="col-right" style="font-size: 14px; font-weight: bold;">${{ number_format($kitchenQuote->total ?? 0, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- ============================================ --}}
        {{-- VANITY QUOTE SECTION --}}
        {{-- ============================================ --}}
        <div class="quote-type-title" style="margin-top: 30px;">VANITY QUOTE - {{ $vanityQuote->quote_number ?? 'N/A' }}</div>

        {{-- VANITY QUOTE ITEMS --}}
        <div class="section-title">QUOTE ITEMS</div>
        <table class="items">
            <thead>
                <tr>
                    <th style="width:35%;">Item / Description</th>
                    <th style="width:15%;" class="col-center">Qty</th>
                    <th style="width:20%;" class="col-right">Unit Price</th>
                    <th style="width:20%;" class="col-right">Line Total</th>
                    <th style="width:10%;" class="col-center">Taxed</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $vanityQuoteItems = collect($vanityItems)->filter(function($item) {
                        return (str_ends_with($item->type, '_TOP') || $item->type === 'VANITY_TOP')
                            && (float)$item->qty > 0 
                            && (float)$item->line_total > 0;
                    });
                @endphp
                
                @forelse($vanityQuoteItems as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td class="col-center">
                            {{ $item->qty > 0 ? rtrim(rtrim(number_format($item->qty, 2, '.', ''), '0'), '.') : '—' }}
                        </td>
                        <td class="col-right">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="col-right">${{ number_format($item->line_total, 2) }}</td>
                        <td class="col-center">{{ !empty($item->is_taxable) ? 'T' : '' }}</td>
                    </tr>
                @empty
                    <tr class="no-data-row">
                        <td colspan="5" class="col-center">No vanity quote items</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- VANITY BOX MANUFACTURERS --}}
        <div class="section-title">BOX MANUFACTURERS</div>
        <table class="items">
            <thead>
                <tr>
                    <th style="width:40%;">Manufacturer Name</th>
                    <th style="width:15%;" class="col-center">Qty</th>
                    <th style="width:20%;" class="col-right">Unit Price</th>
                    <th style="width:25%;" class="col-right">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $vanityManufacturers = collect($vanityItems)->filter(function($item) {
                        return (str_ends_with($item->type, '_MANUFACTURER') || $item->type === 'VANITY_MANUFACTURER')
                            && (float)$item->qty > 0 
                            && (float)$item->line_total > 0;
                    });
                @endphp
                
                @forelse($vanityManufacturers as $mfg)
                    <tr>
                        <td>{{ $mfg->name }}</td>
                        <td class="col-center">
                            {{ $mfg->qty > 0 ? rtrim(rtrim(number_format($mfg->qty, 2, '.', ''), '0'), '.') : '—' }}
                        </td>
                        <td class="col-right">${{ number_format($mfg->unit_price, 2) }}</td>
                        <td class="col-right">${{ number_format($mfg->line_total, 2) }}</td>
                    </tr>
                @empty
                    @foreach (['Bertch', 'Mantra', 'CB', '802/USCD FROM 2020', 'KCD/USCD', 'Dura Supreme', 'OMEGA', '20/20 LIST PRICE'] as $mfg)
                        <tr>
                            <td>{{ $mfg }}</td>
                            <td class="col-center">—</td>
                            <td class="col-right">—</td>
                            <td class="col-right">—</td>
                        </tr>
                    @endforeach
                @endforelse
            </tbody>
        </table>

        {{-- VANITY MARGIN / MARKUP --}}
        <div class="section-title">MARGIN / MARKUP</div>
        <table class="items">
            <thead>
                <tr>
                    <th style="width:50%;">Description</th>
                    <th style="width:20%;" class="col-center">Multiplier</th>
                    <th style="width:30%;" class="col-right">Result</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $vanityMargins = collect($vanityItems)->filter(function($item) {
                        return (str_ends_with($item->type, '_MARGIN_MARKUP') || $item->type === 'VANITY_MARGIN_MARKUP')
                            && (float)$item->line_total > 0;
                    });
                @endphp
                
                @forelse($vanityMargins as $margin)
                    <tr>
                        <td>{{ $margin->name }}</td>
                        <td class="col-center">{{ number_format($margin->unit_price, 2) }}x</td>
                        <td class="col-right">${{ number_format($margin->line_total, 2) }}</td>
                    </tr>
                @empty
                    @foreach ([
                        '1.425 - 1.5 CONTRACTOR 1ST TIME',
                        '1.5 - 1.55 CONTRACTOR',
                        '1.55 - 1.6, 1.66 - 1.7 RETAIL',
                        'TSC BUFFER',
                        'HARDWARE QUANTITY',
                        'MISC ITEMS',
                        '802 FREIGHT SURCHARGE',
                        'PRICE CHANGE BUFFER',
                        'MORE THAN 1 PHONE CALL/DAY'
                    ] as $marginOption)
                        <tr>
                            <td>{{ $marginOption }}</td>
                            <td class="col-center">—</td>
                            <td class="col-right">—</td>
                        </tr>
                    @endforeach
                @endforelse
            </tbody>
        </table>

        {{-- VANITY TOTALS --}}
        <table class="items" style="margin-top: 10px;">
            <tfoot>
                <tr class="totals">
                    <td colspan="3" style="text-align: right; font-size: 13px;"><strong>Vanity Subtotal</strong></td>
                    <td class="col-right" style="font-size: 13px;">${{ number_format($vanityQuote->subtotal ?? 0, 2) }}</td>
                </tr>
                <tr class="totals">
                    <td colspan="3" style="text-align: right; font-size: 13px;"><strong>Vanity Tax ({{ ($taxRate * 100) }}%)</strong></td>
                    <td class="col-right" style="font-size: 13px;">${{ number_format($vanityQuote->tax ?? 0, 2) }}</td>
                </tr>
                @if(($vanityQuote->discount ?? 0) > 0)
                <tr class="totals">
                    <td colspan="3" style="text-align: right; font-size: 13px;"><strong>Vanity Discount</strong></td>
                    <td class="col-right" style="font-size: 13px; color: #c00;">-${{ number_format($vanityQuote->discount ?? 0, 2) }}</td>
                </tr>
                @endif
                <tr class="totals" style="background: #e0e0e0;">
                    <td colspan="3" style="text-align: right; font-size: 14px; font-weight: bold;">VANITY TOTAL</td>
                    <td class="col-right" style="font-size: 14px; font-weight: bold;">${{ number_format($vanityQuote->total ?? 0, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- ============================================ --}}
        {{-- COMBINED TOTALS SUMMARY --}}
        {{-- ============================================ --}}
        <table class="items combined-totals" style="margin-top: 30px;">
            <tfoot>
                <tr class="totals">
                    <td colspan="3" style="text-align: right; font-size: 15px; padding: 10px;"><strong>Combined Subtotal</strong></td>
                    <td class="col-right" style="font-size: 15px; padding: 10px;">${{ number_format($combinedSubtotal ?? 0, 2) }}</td>
                </tr>

                <tr class="totals">
                    <td colspan="3" style="text-align: right; font-size: 15px; padding: 10px;"><strong>Combined Tax ({{ ($taxRate * 100) }}%)</strong></td>
                    <td class="col-right" style="font-size: 15px; padding: 10px;">${{ number_format($combinedTax ?? 0, 2) }}</td>
                </tr>

                @if(($combinedDiscount ?? 0) > 0)
                <tr class="totals">
                    <td colspan="3" style="text-align: right; font-size: 15px; padding: 10px;"><strong>Combined Discount</strong></td>
                    <td class="col-right" style="font-size: 15px; padding: 10px; color: #c00;">-${{ number_format($combinedDiscount ?? 0, 2) }}</td>
                </tr>
                @endif

                <tr class="totals" style="background: #333; color: #fff;">
                    <td colspan="3" style="text-align: right; font-size: 18px; font-weight: bold; padding: 12px;">COMBINED GRAND TOTAL</td>
                    <td class="col-right" style="font-size: 18px; font-weight: bold; padding: 12px; color: #fff;">${{ number_format($combinedTotal ?? 0, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="signature">
            <p>Accepted by (Signature):</p>
            <div></div>
        </div>

        <div class="signature" style="margin-bottom:8px;">
            <p style="margin-bottom:0;">Date:</p>
            <div></div>
        </div>
        <br>
        <div class="highlight">THIS COMBINED QUOTE IS VALID FOR {{ setting('quote_expiry_days', 30) }} DAYS</div>

        <div class="note" style="margin-top:12px;">
            @if($quoteTerms)
                <strong>Terms & Conditions:</strong> {!! nl2br(e($quoteTerms)) !!}<br>
            @endif
            <strong>Sales Rep:</strong> {{ optional($kitchenQuote->creator ?? $vanityQuote->creator)->email ?? setting('company_email', 'info@thestonecobblers.com') }}<br>
            @if($quoteFooter)
                <br><em>{{ $quoteFooter }}</em>
            @endif
        </div>

    </div> {{-- end .content --}}

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $font = $fontMetrics->get_font("DejaVu Sans", "normal");
            $size = 10;
            $pageWidth = $pdf->get_width();
            $pageHeight = $pdf->get_height();
            $textWidth = $fontMetrics->get_text_width($text, $font, $size);
            
            // Calculate center position for page number
            $x = ($pageWidth - $textWidth) / 2;
            $y = $pageHeight - 25; // Position from bottom
            
            // Draw the page number
            $pdf->page_text($x, $y, $text, $font, $size, array(0.4, 0.4, 0.4));
        }
    </script>

</body>

</html>

