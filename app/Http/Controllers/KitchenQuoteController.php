<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\KitchenQuote;

class KitchenQuoteController extends Controller
{
    /**
     * Display kitchen or vanity price configuration
     * This handles both Kitchen and Vanity quote pricing
     */
    public function index(Request $request)
    {
        // Determine type from URL path or query parameter
        $path = $request->path();
        $type = 'kitchen'; // default
        
        // Check if URL contains 'vanity'
        if (str_contains($path, 'vanity')) {
            $type = 'vanity';
        }
        
        // Allow query parameter to override
        if ($request->has('type')) {
            $type = $request->query('type');
        }
        
        // Ensure valid type
        if (!in_array($type, ['kitchen', 'vanity'])) {
            $type = 'kitchen';
        }

        $typePrefix = strtoupper($type);
        
        // Filter by type prefix
        $kitchen_tops = KitchenQuote::where('type', 'LIKE', $typePrefix . '%')
            ->orderBy('created_at')
            ->get();

        return view('admin.kitchen_quote.index', compact('kitchen_tops', 'type'));
    }

   /**
     * Store via AJAX
     */
    public function store(Request $request)
    {
        // Validate. We expect single quote create from modal: item, scope_material, unit_price, category, is_taxable
        $rules = [
            'project' => 'required|string|max:255',
            'scope_material' => 'nullable|string|max:255',
            'cost' => 'required|numeric|min:0',
            'type' => 'required|string',
            'is_taxable' => 'nullable|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // Return JSON 422 with errors and old input (so frontend can re-populate)
            return response()->json([
                'ok' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'old' => $request->only(['project','scope_material','cost','type','is_taxable'])
            ], 422);
        }

        $quote = KitchenQuote::create([
            'project' => $request->post('project'),
            'scope_material' => $request->post('scope_material'),
            'cost' => $request->post('cost'),
            'type' => $request->post('type'),
            'is_taxable' => $request->post('is_taxable', false),
        ]);

        // Return the newly created resource (client can append it)
        return response()->json([
            'ok' => true,
            'message' => 'created successfully',
            'data' => array_merge($quote->toArray(), [
                'type_label' => get_kitchen_type_list($quote->type)
            ])
        ], 201);
    }

     /**
     * Return single quote JSON (for edit prefill)
     */
    public function show(KitchenQuote $quote)
    {
        return response()->json([
            'ok' => true,
            'data' => $quote
        ]);
    }

    /**
     * Update via AJAX
     */
    public function update(Request $request, KitchenQuote $quote)
    {
        $rules = [
            'project' => 'required|string|max:255',
            'scope_material' => 'nullable|string|max:255',
            'cost' => 'required|numeric|min:0',
            'type' => 'required|string',
            'is_taxable' => 'nullable|boolean',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'old' => $request->only(['project','scope_material','cost','type','is_taxable'])
            ], 422);
        }

        $quote->project = $request->post('project');
        $quote->scope_material = $request->post('scope_material');
        $quote->cost = $request->post('cost');
        $quote->type = $request->post('type');
        $quote->is_taxable = $request->post('is_taxable', false);
        $quote->save();

        return response()->json([
            'ok' => true,
            'message' => 'updated successfully',
            'data' => array_merge($quote->toArray(), [
                'type_label' => get_kitchen_type_list($quote->type)
            ])
        ]);
    }

    /**
     * Destroy via AJAX (optional)
     */
    public function destroy(KitchenQuote $quote)
    {
        $quote->delete();

        return response()->json([
            'ok' => true,
            'message' => 'deleted successfully',
        ]);
    }
}
