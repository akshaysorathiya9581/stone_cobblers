@extends('layouts.admin')

@section('title', 'Add Quote')

@push('css')
 <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0fdf4;
            padding: 20px;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th {
            background-color: white;
            color: black;
            font-weight: bold;
            padding: 12px 8px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
            border-right: 1px solid #e0e0e0;
        }

        th:last-child {
            border-right: none;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #e0e0e0;
            border-right: 1px solid #e0e0e0;
            vertical-align: top;
        }

        td:last-child {
            border-right: none;
        }

        /* Column alignments */
        .project-col {
            text-align: left;
            width: 25%;
        }

        .scope-col {
            text-align: left;
            width: 25%;
        }

        .qty-col {
            text-align: center;
            width: 15%;
        }

        .cost-col {
            text-align: right;
            width: 15%;
        }

        .total-col {
            text-align: right;
            width: 15%;
        }

        .taxed-col {
            text-align: center;
            width: 5%;
        }

        /* Quantity input styling */
        .qty-input {
            width: 100%;
            padding: 6px 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 12px;
            text-align: center;
            background-color: #e6f3ff; /* Light blue background */
        }

        .qty-input.num-fill {
            background-color: #e8f5e8; /* Light green background */
        }

        .qty-input.yes-no {
            background-color: #fff3cd; /* Light yellow background */
        }

        .qty-input::placeholder {
            color: #999;
            font-style: italic;
        }

        /* Special styling for specific rows */
        .alpha-fill {
            background-color: #ffebee; /* Light red background */
        }

        .cost-value {
            color: #333;
            font-weight: 500;
        }

        .error-value {
            color: #d32f2f;
            font-style: italic;
        }

        .empty-value {
            color: #999;
        }

        .taxed-t {
            color: #2e7d32;
            font-weight: bold;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .table-container {
                font-size: 12px;
            }
            
            th, td {
                padding: 6px 4px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div
            style="display: flex; justify-content: space-between; align-items: center; padding: 20px; margin: 0; border-bottom: 1px solid #e0e0e0;">
            <h2 style="margin: 0; color: #333;">Kitchen Top</h2>
            <div style="text-align: right;">
                <div style="font-size: 14px; color: #666; margin-bottom: 5px;">Accumulative Cost Total:</div>
                <div id="header-total" style="font-size: 24px; font-weight: bold; color: #2e7d32;">$ -</div>
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
                <tbody>
                    <tr>
                        <td>Kitchen - Sq Ft</td>
                        <td class="alpha-fill">alpha fill</td>
                        <td><input type="number" class="qty-input num-fill" placeholder="num fill" min="0"
                                step="0.01"></td>
                        <td class="cost-value">$25.00</td>
                        <td class="empty-value">$ -</td>
                        <td class="taxed-t">T</td>
                    </tr>
                    <tr>
                        <td>Labor Charge</td>
                        <td></td>
                        <td><input type="number" class="qty-input" placeholder="" min="0" step="0.01"></td>
                        <td class="cost-value">$30.00</td>
                        <td class="empty-value">$ -</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Edge - Lin Ft</td>
                        <td>pencil</td>
                        <td><input type="number" class="qty-input num-fill" placeholder="num fill" min="0"
                                step="0.01"></td>
                        <td class="cost-value">$15.00</td>
                        <td class="empty-value">$ -</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>4" BS - Lin Ft</td>
                        <td>included in Sq Ft</td>
                        <td><input type="number" class="qty-input" placeholder="" min="0" step="0.01"></td>
                        <td class="cost-value">$0.00</td>
                        <td class="empty-value">$ -</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>UM Sink Cutout</td>
                        <td></td>
                        <td><input type="number" class="qty-input" placeholder="" min="0" step="0.01"></td>
                        <td class="cost-value">$50.00</td>
                        <td class="empty-value">$ -</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Undermount Sink</td>
                        <td>choice</td>
                        <td><input type="text" class="qty-input yes-no" placeholder="y/n" maxlength="3"></td>
                        <td class="cost-value">$399.00</td>
                        <td class="empty-value">$ -</td>
                        <td class="taxed-t">T</td>
                    </tr>
                    <tr>
                        <td>small oval sink</td>
                        <td></td>
                        <td><input type="number" class="qty-input" placeholder="num fill" min="0" step="0.01">
                        </td>
                        <td class="cost-value">$150.00</td>
                        <td class="empty-value">$ -</td>
                        <td class="taxed-t">T</td>
                    </tr>
                    <tr>
                        <td>Extra Sink Cutout</td>
                        <td></td>
                        <td><input type="text" class="qty-input" placeholder="or check?" maxlength="10"></td>
                        <td class="cost-value">$25.00</td>
                        <td class="empty-value">$ -</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Cooktop Cutout</td>
                        <td></td>
                        <td><input type="number" class="qty-input" placeholder="" min="0" step="0.01"></td>
                        <td class="cost-value">$35.00</td>
                        <td class="empty-value">$ -</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Electrical Cutouts</td>
                        <td></td>
                        <td><input type="number" class="qty-input" placeholder="" min="0" step="0.01"></td>
                        <td class="cost-value">$15.00</td>
                        <td class="empty-value">$ -</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Arc Charges</td>
                        <td>per Linear Foot</td>
                        <td><input type="number" class="qty-input" placeholder="" min="0" step="0.01"></td>
                        <td class="cost-value">$45.00</td>
                        <td class="empty-value">$ -</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Radius 6" - 12"</td>
                        <td></td>
                        <td><input type="number" class="qty-input" placeholder="" min="0" step="0.01"></td>
                        <td class="cost-value">$75.00</td>
                        <td class="empty-value">$ -</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Bump-Outs</td>
                        <td></td>
                        <td><input type="number" class="qty-input" placeholder="" min="0" step="0.01"></td>
                        <td class="cost-value">$60.00</td>
                        <td class="empty-value">$ -</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>water fall</td>
                        <td></td>
                        <td><input type="number" class="qty-input" placeholder="" min="0" step="0.01"></td>
                        <td class="cost-value">$40.00</td>
                        <td class="empty-value">$ -</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>removal</td>
                        <td></td>
                        <td><input type="number" class="qty-input" placeholder="" min="0" step="0.01"></td>
                        <td class="cost-value">$20.00</td>
                        <td class="empty-value">$ -</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Extra Labor</td>
                        <td></td>
                        <td><input type="number" class="qty-input" placeholder="" min="0" step="0.01"></td>
                        <td class="cost-value">$45.00</td>
                        <td class="empty-value">$ -</td>
                        <td></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align: right; font-weight: bold;">Total:</td>
                        <td id="grand-total" class="empty-value">$ -</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div style="padding: 20px; border-top: 1px solid #e0e0e0; background-color: #f8f9fa;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <button id="prev-tab"
                style="padding: 10px 20px; background-color: white; border: 1px solid #ccc; color: #333; cursor: pointer; border-radius: 4px; display: flex; align-items: center; gap: 5px;">
                <span>←</span> Previous
            </button>
            <div style="font-size: 14px; color: #666;">Step 1 of 3</div>
            <button id="next-tab"
                style="padding: 10px 20px; background-color: #2e7d32; color: white; border: none; cursor: pointer; border-radius: 4px; display: flex; align-items: center; gap: 5px;">
                Next <span>→</span>
            </button>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Add functionality to calculate totals when quantities change
        document.addEventListener('DOMContentLoaded', function() {
            const qtyInputs = document.querySelectorAll('.qty-input');

            // Function to calculate grand total
            function calculateGrandTotal() {
                const totalCells = document.querySelectorAll('tbody tr td:nth-child(5)');
                let grandTotal = 0;

                totalCells.forEach(cell => {
                    const cellText = cell.textContent.trim();
                    if (cellText !== '$ -' && cellText !== '#VALUE!') {
                        const value = parseFloat(cellText.replace('$', '').replace(',', ''));
                        if (!isNaN(value)) {
                            grandTotal += value;
                        }
                    }
                });

                const grandTotalElement = document.getElementById('grand-total');
                const headerTotalElement = document.getElementById('header-total');

                if (grandTotal > 0) {
                    const formattedTotal = `$${grandTotal.toFixed(2)}`;
                    grandTotalElement.textContent = formattedTotal;
                    grandTotalElement.className = 'cost-value';
                    headerTotalElement.textContent = formattedTotal;
                    headerTotalElement.style.color = '#2e7d32';
                } else {
                    grandTotalElement.textContent = '$ -';
                    grandTotalElement.className = 'empty-value';
                    headerTotalElement.textContent = '$ -';
                    headerTotalElement.style.color = '#666';
                }
            }

            qtyInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const row = this.closest('tr');
                    const costCell = row.querySelector('td:nth-child(4)'); // COST column
                    const totalCell = row.querySelector('td:nth-child(5)'); // TOTAL column

                    if (costCell && totalCell) {
                        const costText = costCell.textContent.trim();
                        const cost = parseFloat(costText.replace('$', '').replace(',', ''));
                        const qty = parseFloat(this.value) || 0;
                        const total = cost * qty;

                        console.log('Cost:', cost, 'Qty:', qty, 'Total:', total); // Debug log

                        if (!isNaN(total) && total > 0) {
                            totalCell.textContent = `$${total.toFixed(2)}`;
                            totalCell.className = 'cost-value';
                        } else {
                            totalCell.textContent = '$ -';
                            totalCell.className = 'empty-value';
                        }

                        // Recalculate grand total after each change
                        calculateGrandTotal();
                    }
                });
            });

            // Test the calculation on page load
            console.log('JavaScript loaded successfully');

            // Navigation functionality
            document.getElementById('next-tab').addEventListener('click', function() {
                // Save current form data to localStorage
                const formData = {};
                qtyInputs.forEach((input, index) => {
                    formData[`qty_${index}`] = input.value;
                });
                formData.grandTotal = document.getElementById('grand-total').textContent;
                localStorage.setItem('kitchenTopData', JSON.stringify(formData));

                // Navigate to Kitchen Cab page
                window.location.href = 'kitchen-cab.html';
            });

            document.getElementById('prev-tab').addEventListener('click', function() {
                // Navigate back to previous page
                window.location.href = 'index.html';
            });
        });
    </script>
@endpush
