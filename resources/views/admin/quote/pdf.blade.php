<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Quote</title>
        <style>
            @page { margin: 20mm; }
            body { font-family: DejaVu Sans, sans-serif; font-size: 12px; line-height: 1.4; }
            .header { display: flex; align-items: center; margin-bottom: 20px; }
            .logo { width: 120px; }
            .company-info { margin-left: 15px; font-size: 13px; }
            .company-info h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
            .quote-meta { margin: 15px 0; font-size: 12px; }
            .quote-meta td { padding: 4px 8px; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; }
            th, td { border: 1px solid #333; padding: 6px 8px; font-size: 12px; }
            th { background: #f2f2f2; text-align: left; }
            .section-title { background: #333; color: #fff; font-weight: bold; padding: 5px; }
            .totals td { font-weight: bold; }
            .signature { margin-top: 30px; }
            .signature div { margin-top: 40px; border-top: 1px solid #333; width: 200px; }
            .note { margin-top: 20px; font-size: 11px; }
            .highlight { background: #e6f0ff; padding: 10px; margin-top: 15px; border: 1px solid #99c2ff; }
            .center { text-align: center; }
        </style>
    </head>
    <body>
        <!-- Header with logo and company info -->
        <div class="header">
            <img src="logo.png" class="logo" alt="Logo">
            <div class="company-info">
                <h2>The Stone Cobblers</h2>
                <div>
                    317 West Boylston St
                    <br>
                    West Boylston, MA 01583
                    <br>
                    774-261-4445
                </div>
            </div>
        </div>
        <!-- Quote meta -->
        <table class="quote-meta">
            <tr>
                <td>
                    <strong>Quote Number:</strong>
                    #001
                </td>
                <td>
                    <strong>Quote Date:</strong>
                    09/15/2025
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <strong>Customer:</strong>
                    John Doe
                    <br>
                    <strong>Address:</strong>
                    123 Main Street, City, State
                </td>
            </tr>
        </table>
        <p>
            <strong>Project:</strong>
            KITCHEN CAB + KITCHEN TOP | VANITY CAB + VANITY TOP
        </p>
        <!-- Items table -->
        <table>
            <thead>
                <tr>
                    <th>Scope / Material</th>
                    <th>Qty</th>
                    <th>Cost</th>
                    <th>Total</th>
                    <th>Taxed 'T'</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Granite Countertop</td>
                    <td>20 sq.ft</td>
                    <td>$100</td>
                    <td>$2000</td>
                    <td>Yes</td>
                </tr>
                <tr>
                    <td>Edge Profile</td>
                    <td>20 ft</td>
                    <td>$10</td>
                    <td>$200</td>
                    <td>No</td>
                </tr>
                <tr>
                    <td>Backsplash</td>
                    <td>10 ft</td>
                    <td>$20</td>
                    <td>$200</td>
                    <td>No</td>
                </tr>
                <tr>
                    <td>Misc Items</td>
                    <td>—</td>
                    <td>$50</td>
                    <td>$50</td>
                    <td>No</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="totals">
                    <td colspan="4">Subtotal</td>
                    <td>$2450</td>
                </tr>
                <tr class="totals">
                    <td colspan="4">Tax</td>
                    <td>$175</td>
                </tr>
                <tr class="totals">
                    <td colspan="4">Grand Total</td>
                    <td>$2625</td>
                </tr>
            </tfoot>
        </table>
        <!-- Cabinet Manufacturer -->
        <div class="section-title">CABINET MANUFACTURER</div>
        <table>
            <tr>
                <td>bertch</td>
                <td></td>
            </tr>
            <tr>
                <td>mantra</td>
                <td></td>
            </tr>
            <tr>
                <td>CB</td>
                <td></td>
            </tr>
            <tr>
                <td>802/USCD FROM 2020</td>
                <td></td>
            </tr>
            <tr>
                <td>KCD/USCD</td>
                <td></td>
            </tr>
            <tr>
                <td>dura</td>
                <td></td>
            </tr>
            <tr>
                <td>OMEGA</td>
                <td></td>
            </tr>
            <tr>
                <td>20/20 LIST PRICE</td>
                <td></td>
            </tr>
        </table>
        <!-- Margin / Markup -->
        <div class="section-title">MARGIN / MARKUP</div>
        <table>
            <tr>
                <td>1.425 - 1.5 CONTRACTOR 1ST TIME</td>
                <td></td>
            </tr>
            <tr>
                <td>1.5 - 1.55 CONTRACTOR</td>
                <td></td>
            </tr>
            <tr>
                <td>1.55 - 1.6, 1.66 - 1.7 RETAIL</td>
                <td></td>
            </tr>
            <tr>
                <td>TSC BUFFER</td>
                <td></td>
            </tr>
            <tr>
                <td>HARDWARE QUANTITY</td>
                <td></td>
            </tr>
            <tr>
                <td>MISC ITEMS</td>
                <td></td>
            </tr>
            <tr>
                <td>802 FREIGHT SURCHARGE</td>
                <td></td>
            </tr>
            <tr>
                <td>PRICE CHANGE BUFFER</td>
                <td></td>
            </tr>
            <tr>
                <td>MORE THAN 1 PHONE CALL/DAY</td>
                <td></td>
            </tr>
            <tr>
                <td>TOTAL RETAIL</td>
                <td></td>
            </tr>
            <tr>
                <td>TAX</td>
                <td></td>
            </tr>
        </table>
        <!-- Delivery -->
        <div class="section-title">DELIVERY</div>
        <table>
            <tr>
                <td>FULL KIT TAILGATE</td>
                <td></td>
            </tr>
            <tr>
                <td>UP TO TEN ITEMS</td>
                <td></td>
            </tr>
            <tr>
                <td>SINGLE ITEM VAN</td>
                <td></td>
            </tr>
            <tr>
                <td>CUSTOMER PICKUP</td>
                <td></td>
            </tr>
            <tr>
                <td>DBA fee / fuel surcharge</td>
                <td></td>
            </tr>
            <tr>
                <td>Final total DBA</td>
                <td></td>
            </tr>
        </table>
        <!-- Signature -->
        <div class="signature">
            <p>Accepted by (Signature):</p>
            <div></div>
        </div>
        <div class="signature">
            <p style="margin-bottom: 0;">Date:</p>
            <div></div>
        </div>
        <!-- Quote validity -->
        <div class="highlight center">
            THIS QUOTE IS VALID FOR 30 DAYS
        </div>
        <!-- Notes -->
        <div class="note">
            <strong>Sales Rep: Phoebe@thestonecobblers.com</strong><br>
            <strong>Payment Schedule:</strong>
            50% deposit required, balance due upon completion.
            <br>
            <strong>Terms:</strong>
            Countertops include field templates, materials, fabrication, and installation. 
    Additional services may incur extra charges.
            <br>
            <em>Please review carefully before signing.</em>
        </div>
    </body>
</html>
