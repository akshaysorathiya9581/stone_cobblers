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
 * Vanity Quote Management Controller
 * Handles vanity-specific quote operations
 * Uses the same Quote model but filters by quote_type = 'vanity'
 */
class VanityQuoteManagementController extends Controller
{
    protected $quoteType = 'vanity';

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of vanity quotes
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

        return view('admin.quote.vanity.index', compact('quotes', 'type'));
    }

    /**
     * Show the form for creating a new vanity quote
     */
    public function create()
    {
        $quoteType = $this->quoteType;
        $typePrefix = strtoupper($quoteType);
        
        // Fetch vanity-specific configuration data
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

        return view('admin.quote.vanity.create', compact(
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
     * Display the specified vanity quote
     */
    public function show(Quote $vanity_quote)
    {
        // Ensure it's a vanity quote
        if ($vanity_quote->quote_type !== $this->quoteType) {
            abort(404, 'Vanity quote not found');
        }

        $vanity_quote->load('items', 'project.customer', 'creator');
        return view('admin.quote.vanity.show', compact('vanity_quote'));
    }
}
