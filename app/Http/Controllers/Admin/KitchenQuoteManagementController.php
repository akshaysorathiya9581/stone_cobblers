<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\KitchenQuote;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Kitchen Quote Management Controller
 * Handles kitchen-specific quote operations
 * Uses the same Quote model but filters by quote_type = 'kitchen'
 */
class KitchenQuoteManagementController extends Controller
{
    protected $quoteType = 'kitchen';

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of kitchen quotes
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status');

        $query = Quote::with(['project.customer', 'creator'])
            ->where('quote_type', $this->quoteType)
            ->orderBy('created_at', 'desc');

        if ($user && $user->role === 'admin') {
            // Admin: see all quotes
        } else {
            // Non-admin: show only their quotes
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                ->orWhereHas('project', function ($qp) use ($user) {
                    $qp->where('user_id', $user->id);
                });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $quotes = $query->get();
        $type = $this->quoteType;

        return view('admin.quote.kitchen.index', compact('quotes', 'type'));
    }

    /**
     * Show the form for creating a new kitchen quote
     */
    public function create()
    {
        $quoteType = $this->quoteType;
        $typePrefix = strtoupper($quoteType);
        
        // Fetch kitchen-specific configuration data
        $QUOTE_TOP = KitchenQuote::where('type', $typePrefix . '_TOP')
            ->get()
            ->keyBy('project');
            
        $QUOTE_MANUFACTURER = KitchenQuote::where('type', $typePrefix . '_MANUFACTURER')
            ->get()
            ->keyBy('project');
            
        $QUOTE_MARGIN_MARKUP = KitchenQuote::where('type', $typePrefix . '_MARGIN_MARKUP')
            ->get()
            ->keyBy('project');
            
        $QUOTE_DELIVERY = KitchenQuote::where('type', $typePrefix . '_DELIVERY')
            ->get()
            ->keyBy('project');
            
        $QUOTE_BUFFER = KitchenQuote::where('type', $typePrefix . '_BUFFER')
            ->get()
            ->keyBy('project');

        // Get projects list based on user role
        $user = auth()->user();

        if ($user && $user->role === 'admin') {
            $projects = Project::with('customer')->get();
        } else {
            $projects = $user ? Project::with('customer')->where('user_id', $user->id)->get() : collect();
        }

        return view('admin.quote.kitchen.create', compact(
            'quoteType',
            'projects',
            'QUOTE_TOP',
            'QUOTE_MANUFACTURER',
            'QUOTE_MARGIN_MARKUP',
            'QUOTE_DELIVERY',
            'QUOTE_BUFFER'
        ));
    }

    /**
     * Display the specified kitchen quote
     */
    public function show(Quote $kitchen_quote)
    {
        // Ensure it's a kitchen quote
        if ($kitchen_quote->quote_type !== $this->quoteType) {
            abort(404, 'Kitchen quote not found');
        }

        $kitchen_quote->load('items', 'project.customer', 'creator');
        return view('admin.quote.kitchen.show', compact('kitchen_quote'));
    }
}
