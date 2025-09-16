<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Quote {{ $quote->quote_number ?? '' }}</title>

    <style>
        /* --- Page margins (top margin must allow header height) --- */
        @page {
            margin: 40mm 1mm 15mm 1mm;
            /* top, right, bottom, left */
        }

        /* Basic typography */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* --- Fixed header (appears on every page) --- */
        .pdf-header {
            position: fixed;
            top: -25mm;
            /* header sits in the top margin area */
            left: 0;
            right: 0;
            height: 40mm;
            /* header height - keep in sync with @page top margin */
            display: flex;
            align-items: center;
            padding: 6px 15mm;
            box-sizing: border-box;
        }

        .pdf-header .left {
            width: 35%;
            display: flex;
            align-items: center;
            float: left
        }

        .pdf-header .left img.logo {
            width: auto;
            height: auto;
            max-height: 60px;
            max-width: 190px;
            display: block;
        }

        /* .pdf-header .center {
            width: 44%;
            text-align: center;
            }
            .pdf-header .center h1 {
            margin: 0;
            font-size: 20px;
            letter-spacing: 0.5px;
            }
            .pdf-header .center .tagline {
            margin-top: 2px;
            font-size: 11px;
            color: #555;
            } */

        .pdf-header .right {
            width: 35%;
            text-align: right;
            font-size: 11px;
            color: #222;
            line-height: 1.35;
            white-space: pre-line;
            float: right;
            margin-top: -20px
        }

        /* --- Fixed footer with page numbers --- */
        .pdf-footer {
            position: fixed;
            bottom: -12mm;
            /* sits in bottom margin */
            left: 0;
            right: 0;
            height: 12mm;
            text-align: center;
            font-size: 11px;
            color: #666;
        }

        .pdf-footer .pagenum:before {
            content: "Page " counter(page) " / " counter(pages);
        }

        /* --- Content area (starts below header) --- */
        .content {
            padding: 0 15mm;
            box-sizing: border-box;
        }

        .quote-meta {
            margin: 6px 0 12px 0;
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
            padding: 6px;
            margin-top: 14px;
            font-size: 13px;
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

        /* Prevent page breaks inside table rows for important rows */
        tr {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>

    {{-- HEADER (fixed) --}}
    <div class="pdf-header" aria-hidden="true">
        <div class="left">
            @if (!empty($companyLogo))
                <img src="{{ $companyLogo }}" alt="{{ $companyName }}" class="logo">
            @else
                {{-- fallback: simple text logo --}}
                <div style="font-weight:700;font-size:18px;">{{ $companyName }}</div>
            @endif
        </div>

        {{-- <div class="center">
      <h1 style="margin-bottom:4px;">{{ $companyName }}</h1>
      <div class="tagline">{{ strtoupper(optional($quote->project)->name ?? '') }}</div>
    </div> --}}

        <div class="right">
            {!! nl2br(e($companyAddress)) !!}
        </div>
    </div>

    {{-- FOOTER (fixed page numbers) --}}
    <div class="pdf-footer">
        <span class="pagenum"></span>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="content">
        <table class="quote-meta">
            <tr>
                <td style="width:50%;">
                    <strong>Quote Number:</strong> {{ $quote->quote_number ?? '—' }}<br>
                    <strong>Customer:</strong>
                    {{ $quote->customer_name ?? (optional($quote->project->client)->name ?? 'N/A') }}
                </td>
                <td style="width:50%; text-align:right;">
                    <strong>Quote Date:</strong> {{ optional($quote->created_at)->format('m/d/Y') ?? '—' }}<br>
                    <strong>Expires:</strong> {{ optional($quote->expires_at)->format('m/d/Y') ?? '—' }}
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <strong>Address:</strong>
                    {{ optional($quote->project->client)->address ?? 'N/A' }}
                </td>
            </tr>
        </table>

        <p><strong>Project:</strong>
            {{ trim(($quote->is_kitchen ? 'KITCHEN' : '') . ($quote->is_kitchen && $quote->is_vanity ? ' + ' : '') . ($quote->is_vanity ? 'VANITY' : '')) }}
        </p>

        <table class="items">
            <thead>
                <tr>
                    <th style="width:22%;">Project</th>
                    <th style="width:28%;">Scope / Material</th>
                    <th style="width:10%;" class="col-center">Qty</th>
                    <th style="width:12%;" class="col-right">Cost</th>
                    <th style="width:16%;" class="col-right">Total</th>
                    <th style="width:12%;" class="col-center">Taxed 'T'</th>
                </tr>
            </thead>

            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>

                        <td style="text-align:center;">
                            @if (!empty($item->image_data))
                                <img src="{{ $item->image_data }}" alt="{{ $item->name }}" class="item-thumb">
                                <div style="font-size:11px;">{{ $item->name }}</div>
                            @else
                                <div style="font-size:12px;">{{ $item->name }}</div>
                            @endif
                        </td>

                        <td class="col-center">
                            {{ $item->qty > 0 ? rtrim(rtrim(number_format($item->qty, 2, '.', ''), '0'), '.') : '—' }}
                        </td>
                        <td class="col-right">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="col-right">${{ number_format($item->line_total, 2) }}</td>
                        <td class="col-center">{{ !empty($item->is_taxable) ? 'T' : '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="center">No items</td>
                    </tr>
                @endforelse
            </tbody>

            <tfoot>
                <tr class="totals">
                    <td colspan="4">Subtotal</td>
                    <td class="col-right">${{ number_format($quote->subtotal ?? 0, 2) }}</td>
                    <td></td>
                </tr>

                <tr class="totals">
                    <td colspan="4">Tax</td>
                    <td class="col-right">${{ number_format($quote->tax ?? 0, 2) }}</td>
                    <td></td>
                </tr>

                <tr class="totals">
                    <td colspan="4">Discount</td>
                    <td class="col-right">${{ number_format($quote->discount ?? 0, 2) }}</td>
                    <td></td>
                </tr>

                <tr class="totals">
                    <td colspan="4">Grand Total</td>
                    <td class="col-right">${{ number_format($quote->total ?? 0, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <div class="section-title">CABINET MANUFACTURER</div>
        <table style="width:100%; border-collapse:collapse; margin-top:6px;">
            @foreach (['bertch', 'mantra', 'CB', '802/USCD FROM 2020', 'KCD/USCD', 'dura', 'OMEGA', '20/20 LIST PRICE'] as $mfg)
                <tr>
                    <td style="padding:8px; border:1px solid #333;">{{ $mfg }}</td>
                    <td style="padding:8px; border:1px solid #333;"></td>
                </tr>
            @endforeach
        </table>

        <div class="section-title">MARGIN / MARKUP</div>
        <table style="width:100%; border-collapse:collapse; margin-top:6px;">
            @foreach (['1.425 - 1.5 CONTRACTOR 1ST TIME', '1.5 - 1.55 CONTRACTOR', '1.55 - 1.6, 1.66 - 1.7 RETAIL', 'TSC BUFFER', 'HARDWARE QUANTITY', 'MISC ITEMS', '802 FREIGHT SURCHARGE', 'PRICE CHANGE BUFFER', 'MORE THAN 1 PHONE CALL/DAY', 'TOTAL RETAIL', 'TAX'] as $row)
                <tr>
                    <td style="padding:8px;border:1px solid #333;">{{ $row }}</td>
                    <td style="padding:8px;border:1px solid #333;"></td>
                </tr>
            @endforeach
        </table>

        <div class="section-title">DELIVERY</div>
        <table style="width:100%; border-collapse:collapse; margin-top:6px;">
            @foreach (['FULL KIT TAILGATE', 'UP TO TEN ITEMS', 'SINGLE ITEM VAN', 'CUSTOMER PICKUP', 'DBA fee / fuel surcharge', 'Final total DBA'] as $row)
                <tr>
                    <td style="padding:8px;border:1px solid #333;">{{ $row }}</td>
                    <td style="padding:8px;border:1px solid #333;"></td>
                </tr>
            @endforeach
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
        <br>
        <div class="highlight">THIS QUOTE IS VALID FOR 30 DAYS</div>

        <div class="note" style="margin-top:12px;">
            <strong>Sales Rep:</strong> {{ optional($quote->creator)->email ?? 'info@thestonecobblers.com' }}<br>
            <strong>Payment Schedule:</strong> 50% deposit required, balance due upon completion.<br>
            <strong>Terms:</strong> Countertops include field templates, materials, fabrication, and installation.
            Additional services may incur extra charges.<br>
            <em>Please review carefully before signing.</em>
        </div>

    </div> {{-- end .content --}}

</body>

</html>
