<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\KitchenQuote;

class KitchenQuoteController extends Controller
{
    public function index()
    {
        $kitchen_tops = KitchenQuote::where('type', 'KITCHEN_TOP')
            ->pluck('cost', 'project')
            ->toArray();

        $manufacturers = KitchenQuote::where('type', 'KITCHEN_CABINET')
            ->pluck('cost', 'project')
            ->toArray();

        return view('admin.kitchen_quote.index', compact('kitchen_tops', 'manufacturers'));
    }

   public function store(Request $request)
    {
        // expected arrays:
        // kitchen[name][], kitchen[unit_price][]
        // manufacturer[name][], manufacturer[unit_price][]
        $kNames = $request->input('kitchen.name', []);
        $kPrices = $request->input('kitchen.unit_price', []);
        $mNames = $request->input('manufacturer.name', []);
        $mPrices = $request->input('manufacturer.unit_price', []);

        DB::beginTransaction();
        try {
            // upsert Kitchen Top items
            $countK = max(count($kNames), count($kPrices));
            for ($i = 0; $i < $countK; $i++) {
                $name = isset($kNames[$i]) ? trim($kNames[$i]) : null;
                if (!$name) continue;
                $price = isset($kPrices[$i]) && $kPrices[$i] !== '' ? (float)$kPrices[$i] : 0.0;

                KitchenQuote::updateOrCreate(
                    ['project' => $name, 'type' => 'KITCHEN_TOP'],
                    ['cost' => number_format($price, 4, '.', '')]
                );
            }

            // upsert Manufacturer items
            $countM = max(count($mNames), count($mPrices));
            for ($i = 0; $i < $countM; $i++) {
                $name = isset($mNames[$i]) ? trim($mNames[$i]) : null;
                if (!$name) continue;
                $price = isset($mPrices[$i]) && $mPrices[$i] !== '' ? (float)$mPrices[$i] : 0.0;

                KitchenQuote::updateOrCreate(
                    ['project' => $name, 'type' => 'KITCHEN_CABINET'],
                    ['cost' => number_format($price, 4, '.', '')]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Prices saved.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('KitchenQuote store error: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Save failed: '.$e->getMessage()
            ], 500);
        }
    }
}
