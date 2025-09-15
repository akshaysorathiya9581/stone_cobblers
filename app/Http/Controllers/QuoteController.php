<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\KitchenQuote;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class QuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.quote.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kitchen_tops = KitchenQuote::where('type', 'KITCHEN_TOP')
            ->pluck('cost', 'project')
            ->toArray();

        $manufacturers = KitchenQuote::where('type', 'KITCHEN_CABINET')
            ->pluck('cost', 'project')
            ->toArray();

        return view('admin.quote.create', compact('kitchen_tops', 'manufacturers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Basic validation
        $request->validate([
            'kitchen.name'             => 'nullable|array',
            'kitchen.qty'              => 'nullable|array',
            'kitchen.unit_price'       => 'nullable|array',
            'manufacturer.name'        => 'nullable|array',
            'manufacturer.qty'         => 'nullable|array',
            'manufacturer.unit_price'  => 'nullable|array',
            'delivery'                 => 'nullable|array',
            'final_total'              => 'nullable|numeric',
            'list_price'               => 'nullable|numeric',
            'expires_at'               => 'nullable|date',
            'customer_name'            => 'nullable|string',
            'project_name'             => 'nullable|string',
        ]);

        // format helpers
        $toUnitPrice = function ($v) {
            return number_format((float)$v, 4, '.', '');
        };
        $toQty = function ($v) {
            return number_format((float)$v, 2, '.', '');
        };
        $toLine = function ($v) {
            return number_format((float)$v, 4, '.', '');
        };

        DB::beginTransaction();

        try {
            // Generate quote number like QT-2025-001
            $quoteNumber = $this->generateQuoteNumber();

            // Determine expires_at: client-supplied or default 30 days from now
            $expiresAt = $request->filled('expires_at')
                ? Carbon::parse($request->input('expires_at'))
                : Carbon::now()->addDays(30);

            // Create quote master record
            $quote = Quote::create([
                'user_id'      => $user->id,
                'quote_number' => $quoteNumber,
                'customer_name'=> $request->input('customer_name'),
                'project_name' => $request->input('project_name'),
                'status'       => 'Draft',
                'final_total'  => 0,
                'expires_at'   => $expiresAt,
            ]);

            $computedTotal = 0.0;

            // 1) kitchen items (parallel arrays)
            $kNames = $request->input('kitchen.name', []);
            $kQtys  = $request->input('kitchen.qty', []);
            $kUnits = $request->input('kitchen.unit_price', []);

            for ($i = 0; $i < count($kNames); $i++) {
                $name = (string)($kNames[$i] ?? '');
                if ($name === '') continue;

                $qty  = isset($kQtys[$i]) ? (float)$kQtys[$i] : 0.0;
                $unit = isset($kUnits[$i]) ? (float)$kUnits[$i] : 0.0;
                $line = $qty * $unit;

                // skip true zero lines if desired (still you might want to keep zero-lines)
                if ($qty == 0 && $line == 0) {
                    // optionally continue; here we still save if you prefer to keep
                }

                $item = new QuoteItem([
                    'name'       => $name,
                    'unit_price' => $toUnitPrice($unit),
                    'qty'        => $toQty($qty),
                    'line_total' => $toLine($line),
                ]);
                $quote->items()->save($item);

                $computedTotal += (float)$item->line_total;
            }

            // 2) manufacturer items (parallel arrays)
            $mNames = $request->input('manufacturer.name', []);
            $mQtys  = $request->input('manufacturer.qty', []);
            $mUnits = $request->input('manufacturer.unit_price', []);

            for ($i = 0; $i < count($mNames); $i++) {
                $name = (string)($mNames[$i] ?? '');
                if ($name === '') continue;

                $qty  = isset($mQtys[$i]) ? (float)$mQtys[$i] : 0.0;
                $unit = isset($mUnits[$i]) ? (float)$mUnits[$i] : 0.0;
                $line = $qty * $unit;

                $item = new QuoteItem([
                    'name'       => $name,
                    'unit_price' => $toUnitPrice($unit),
                    'qty'        => $toQty($qty),
                    'line_total' => $toLine($line),
                ]);
                $quote->items()->save($item);

                $computedTotal += (float)$item->line_total;
            }

            // 3) delivery items (accepts nested delivery[...] arrays)
            $delivery = $request->input('delivery', []);
            if (is_array($delivery)) {
                foreach ($delivery as $key => $vals) {
                    if (!is_array($vals)) continue;
                    $qty  = isset($vals['qty']) ? (float)$vals['qty'] : 0.0;
                    $unit = isset($vals['unit_price']) ? (float)$vals['unit_price'] : 0.0;
                    $line = $qty * $unit;
                    if ($qty == 0 && $line == 0) continue;

                    $label = strtoupper(str_replace('_', ' ', $key)); // e.g. full_kit_tailgate -> FULL KIT TAILGATE
                    $item = new QuoteItem([
                        'name'       => $label,
                        'unit_price' => $toUnitPrice($unit),
                        'qty'        => $toQty($qty),
                        'line_total' => $toLine($line),
                    ]);
                    $quote->items()->save($item);
                    $computedTotal += (float)$item->line_total;
                }
            }

            // 4) extras mapped from single fields (price_buffer, phone_call_buffer, dba_surcharge)
            $extrasMap = [
                'price_buffer'      => 'Price Change Buffer',
                'phone_call_buffer' => 'Phone Call Buffer',
                'dba_surcharge'     => 'DBA Surcharge',
            ];
            foreach ($extrasMap as $field => $label) {
                if ($request->filled($field)) {
                    $val = (float)$request->input($field, 0);
                    if ($val != 0) {
                        $item = new QuoteItem([
                            'name'       => $label,
                            'unit_price' => $toUnitPrice($val),
                            'qty'        => $toQty(1),
                            'line_total' => $toLine($val * 1),
                        ]);
                        $quote->items()->save($item);
                        $computedTotal += (float)$item->line_total;
                    }
                }
            }

            // 5) hardware_qty as item if provided
            if ($request->filled('hardware_qty')) {
                $hardwareQty = (float)$request->input('hardware_qty', 0);
                if ($hardwareQty > 0) {
                    // store with unit_price 1.0 (or change if you have per-piece cost)
                    $item = new QuoteItem([
                        'name'       => 'Hardware Quantity',
                        'unit_price' => $toUnitPrice(1),
                        'qty'        => $toQty($hardwareQty),
                        'line_total' => $toLine($hardwareQty * 1),
                    ]);
                    $quote->items()->save($item);
                    $computedTotal += (float)$item->line_total;
                }
            }

            // 6) finalize totals: store server computed final_total (rounded to 2)
            $quote->final_total = number_format((float)$computedTotal, 2, '.', '');
            $quote->save();

            DB::commit();

            return response()->json([
                'success'       => true,
                'message'       => 'Quote saved successfully',
                'quote_id'      => $quote->id,
                'quote_number'  => $quote->quote_number,
                'computed_total'=> (float)$computedTotal,
                'client_total'  => $request->input('final_total') !== null ? (float)$request->input('final_total') : null,
                'expires_at'    => $quote->expires_at ? $quote->expires_at->toDateTimeString() : null,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            // Log::error($e);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save quote: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Generate a sequential quote number QT-YYYY-XXX.
     * Note: naive implementation using count; if you need concurrency guarantees use a sequence table or row lock.
     */
    protected function generateQuoteNumber(): string
    {
        $year = date('Y');
        $prefix = "QT-{$year}-";
        // count existing quotes for the year
        $count = DB::table('quotes')->where('quote_number', 'like', $prefix . '%')->count();
        $seq = $count + 1;
        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quote $quote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quote $quote)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quote $quote)
    {
        //
    }

    public function quote_form_show($type = 'kitchen')
    {
        return view('admin.quote.kitchen_quote', compact('type'));
    }
}
