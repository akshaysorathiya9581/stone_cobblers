<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\KitchenQuote;
use App\Models\Project;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Mail\QuoteSentMail;
use App\Mail\QuoteStatusChangedMail;
use Illuminate\Support\Facades\Mail;

class QuoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of quotes (blade view).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status');

        $query = Quote::with(['project.customer', 'creator'])
            ->orderBy('created_at', 'desc');

        if ($user && $user->role === 'admin') {
            // Admin: see all quotes
        } else {
            // Non-admin: show only quotes related to their projects or created by them
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

        // fetch all (no pagination)
        $quotes = $query->get();

        return view('admin.quote.index', compact('quotes'));
    }


    /**
     * Show creation form.
     */
    public function create()
    {
        // Fetch kitchen quote data from database with full objects (including is_taxable)
        $KITCHEN_TOP = KitchenQuote::where('type', 'KITCHEN_TOP')
            ->get()
            ->keyBy('project');
            
        $KITCHEN_MANUFACTURER = KitchenQuote::where('type', 'KITCHEN_MANUFACTURER')
            ->get()
            ->keyBy('project');
            
        $KITCHEN_MARGIN_MARKUP = KitchenQuote::where('type', 'KITCHEN_MARGIN_MARKUP')
            ->get()
            ->keyBy('project');
            
        $KITCHEN_DELIVERY = KitchenQuote::where('type', 'KITCHEN_DELIVERY')
            ->get()
            ->keyBy('project');
            
        $KITCHEN_BUFFER = KitchenQuote::where('type', 'KITCHEN_BUFFER')
            ->get()
            ->keyBy('project');

        // Get projects list based on user role
        $user = auth()->user();

        if ($user && $user->role === 'admin') {
            $projects = Project::with('customer')->get();
        } else {
            $projects = $user ? Project::with('customer')->where('user_id', $user->id)->get() : collect();
        }

        return view('admin.quote.create', compact(
            'KITCHEN_TOP', 
            'KITCHEN_MANUFACTURER', 
            'KITCHEN_MARGIN_MARKUP', 
            'KITCHEN_DELIVERY', 
            'KITCHEN_BUFFER', 
            'projects'
        ));
    }

   /**
     * Store a new quote (handles full multi-step form payload)
     */
    public function store(Request $request)
    {
        // Basic server-side validation
        $request->validate([
            'project_id'       => 'required|exists:projects,id',
            'customer_name'    => 'nullable|string',
            'project_name'     => 'nullable|string',
            'items'            => 'nullable|array',
            'manufacturers'    => 'nullable|array',
            'margins'          => 'nullable|array',
            'subtotal'         => 'required|numeric',
            'tax'              => 'required|numeric',
            'total'            => 'required|numeric',
            'discount'         => 'nullable|numeric',
        ]);

        $user = Auth::user();

        // helpers to ensure consistent DB storage formatting
        $toUnitPrice = fn($v) => number_format((float)$v, 4, '.', '');
        $toQty       = fn($v) => number_format((float)$v, 2, '.', '');
        $toLine      = fn($v) => number_format((float)$v, 4, '.', '');

        DB::beginTransaction();

        try {
            // Get project details
            $project = Project::with('customer')->findOrFail($request->input('project_id'));
            
            // generate quote number
            $quoteNumber = $this->generateQuoteNumber();

            $expiryDays = setting('quote_expiry_days', 30);
            $expiresAt = $request->filled('expires_at')
                ? Carbon::parse($request->input('expires_at'))
                : Carbon::now()->addDays($expiryDays);

            // Create quote master
            $quote = Quote::create([
                'user_id'      => $user->id,
                'project_id'   => $request->input('project_id'),
                'quote_number' => $quoteNumber,
                'customer_name'=> $request->input('customer_name') ?? $project->customer->name ?? '',
                'project_name' => $request->input('project_name') ?? $project->name ?? '',
                'status'       => 'Draft',
                'subtotal'     => $request->input('subtotal', 0),
                'tax'          => $request->input('tax', 0),
                'discount'     => $request->input('discount', 0),
                'total'        => $request->input('total', 0),
                'pdf_path'     => null,
                'is_kitchen'   => true,
                'is_vanity'    => false,
                'expires_at'   => $expiresAt,
            ]);

            // ---- 1) Process Quote Items ----
            $items = $request->input('items', []);
            foreach ($items as $item) {
                if (empty($item['name'])) continue;
                
                $isTaxable = $item['is_taxable'] ?? false;
                $lineTotal = $toLine($item['line_total'] ?? 0);
                $taxRate = setting('tax_rate', 0.08);
                $taxCost = $isTaxable ? ($lineTotal * $taxRate) : 0;
                
                $quote->items()->create([
                    'name'       => $item['name'],
                    'type'       => 'KITCHEN_TOP',
                    'scope_material' => $item['scope_material'] ?? null,
                    'unit_price' => $toUnitPrice($item['unit_price'] ?? 0),
                    'qty'        => $toQty($item['qty'] ?? 0),
                    'line_total' => $lineTotal,
                    'tax_cost'   => $taxCost,
                    'is_taxable' => $isTaxable,
                ]);
            }

            // ---- 2) Process Manufacturers ----
            $manufacturers = $request->input('manufacturers', []);
            foreach ($manufacturers as $manufacturer) {
                if (empty($manufacturer['name'])) continue;
                
                $isTaxable = $manufacturer['is_taxable'] ?? false;
                $lineTotal = $toLine($manufacturer['line_total'] ?? 0);
                $taxRate = setting('tax_rate', 0.08);
                $taxCost = $isTaxable ? ($lineTotal * $taxRate) : 0;
                
                $quote->items()->create([
                    'name'       => $manufacturer['name'],
                    'type'       => 'KITCHEN_MANUFACTURER',
                    'unit_price' => $toUnitPrice($manufacturer['unit_price'] ?? 0),
                    'qty'        => $toQty($manufacturer['qty'] ?? 0),
                    'line_total' => $lineTotal,
                    'tax_cost'   => $taxCost,
                    'is_taxable' => $isTaxable,
                ]);
            }

            // ---- 3) Process Margins (save as items and metadata) ----
            $margins = $request->input('margins', []);
            $savedMargins = [];
            
            foreach ($margins as $margin) {
                if (empty($margin['description'])) continue;
                
                $isTaxable = $margin['is_taxable'] ?? false;
                $lineTotal = $toLine($margin['result'] ?? 0);
                $taxRate = setting('tax_rate', 0.08);
                $taxCost = $isTaxable ? ($lineTotal * $taxRate) : 0;
                
                // Save as quote item without prefix (type column identifies it)
                $quote->items()->create([
                    'name'       => $margin['description'],
                    'type'       => 'KITCHEN_MARGIN_MARKUP',
                    'unit_price' => $toUnitPrice($margin['multiplier'] ?? 0),
                    'qty'        => $toQty(1),
                    'line_total' => $lineTotal,
                    'tax_cost'   => $taxCost,
                    'is_taxable' => $isTaxable,
                ]);
                
                // Save to metadata
                $savedMargins[] = [
                    'description' => $margin['description'],
                    'multiplier' => $margin['multiplier'] ?? 0,
                    'result' => $margin['result'] ?? 0,
                ];
            }

            // Save margins to meta if column exists
            if (!empty($savedMargins)) {
                try {
                    if (\Schema::hasColumn('quotes', 'meta')) {
                        $quote->meta = json_encode(['margins' => $savedMargins]);
                        $quote->save();
                    }
                } catch (\Throwable $e) {
                    Log::warning('Failed to persist margins into quote.meta: ' . $e->getMessage());
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Quote store error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to save quote: ' . $e->getMessage(),
            ], 500);
        }

        // ---- 9) Generate PDF & save file record ----
        try {
            $project = Project::find($quote->project_id);
            $clientId = $project->user_id ?? 'unknown';

            $safeQuoteNumber = preg_replace('/[^A-Za-z0-9\-_]/', '_', $quote->quote_number);
            $relativePath = "clients/{$clientId}/projects/{$quote->project_id}/quotes";
            $filename = "quote-{$safeQuoteNumber}.pdf";
            $storagePath = "{$relativePath}/{$filename}"; // storage/app/clients/...

            // view data for pdf (adjust view name/vars as needed)
            $items = $quote->items()->orderBy('id')->get();

            // embed company logo as base64 if exists (optional)
            $companyLogo = null;
            $logoPath = public_path('images/logo.jpeg'); // change to your logo path
            if (file_exists($logoPath)) {
                $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                $data = file_get_contents($logoPath);
                $companyLogo = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }

            $viewData = [
                'quote' => $quote,
                'items' => $items,
                'companyName' => setting('company_name', config('app.name')),
                'companyAddress' => setting('company_address', '317 West Boylston St'),
                'companyCity' => setting('company_city', 'West Boylston'),
                'companyState' => setting('company_state', 'MA'),
                'companyZipcode' => setting('company_zipcode', '01583'),
                'companyPhone' => setting('company_phone', '774-261-4445'),
                'companyEmail' => setting('company_email', ''),
                'companyWebsite' => setting('company_website', ''),
                'companyLogo' => $companyLogo,
                'taxRate' => setting('tax_rate', 0.08),
                'quoteTerms' => setting('quote_terms', 'Payment due within 30 days.'),
                'quoteFooter' => setting('quote_footer', 'Thank you for your business!'),
            ];

            // generate pdf with dynamic settings
            $pdfPageSize = setting('pdf_page_size', 'letter');
            $pdfOrientation = setting('pdf_orientation', 'portrait');
            $pdf = Pdf::loadView('admin.quote.pdf', $viewData)->setPaper($pdfPageSize, $pdfOrientation);
            $bytes = $pdf->output();

            // ensure dir exists
            Storage::disk('local')->put($storagePath, $bytes);

            // create file record - adjust fields to your files table
            $file = $quote->files()->create([
                'name' => $filename,
                'path' => $storagePath,
                'mime' => 'application/pdf',
                'size' => Storage::disk('local')->size($storagePath),
                'category' => 'quote_pdf',
                'created_by' => $user->id,
            ]);

            // attach path to quote
            $quote->update(['pdf_path' => $storagePath]);
        } catch (\Throwable $e) {
            Log::error('PDF generation error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            // don't treat PDF failure as fatal; return success with warning
            return response()->json([
                'success' => true,
                'message' => 'Quote saved but PDF generation failed: ' . $e->getMessage(),
                'quote_id' => $quote->id,
                'quote_number' => $quote->quote_number,
            ], 201);
        }

        // All good
        return response()->json([
            'success'       => true,
            'message'       => 'Quote saved and PDF generated successfully',
            'quote_id'      => $quote->id,
            'quote_number'  => $quote->quote_number,
            'pdf_path'      => $storagePath ?? null,
            'subtotal'      => (float)$quote->subtotal,
            'tax'           => (float)$quote->tax,
            'discount'      => (float)$quote->discount,
            'total'         => (float)$quote->total,
        ], 201);
    }

    /**
     * Show details (quote page). You can use this to show download button.
     */
    public function show(Quote $quote)
    {
        $quote->load('items', 'project.client', 'creator');
        return view('admin.quote.show', compact('quote'));
    }

    /**
     * Secure download: only admins, quote creator, or project client owner (or client user email) can access.
     */
    public function download(Quote $quote)
    {
        $user = Auth::user();

        if ($user && ($user->is_admin ?? false)) {
            return $this->streamPrivateFile($quote);
        }

        if ($user && $quote->user_id && $user->id === (int)$quote->user_id) {
            return $this->streamPrivateFile($quote);
        }

        $project = $quote->project()->with('customer')->first();
        if ($project && $project->client) {
            $clientOwnerId = $project->client->created_by ?? null;
            if ($clientOwnerId && $user && $user->id === (int)$clientOwnerId) {
                return $this->streamPrivateFile($quote);
            }
            $clientEmail = $project->client->email ?? null;
            if ($clientEmail && $user && $user->email === $clientEmail) {
                return $this->streamPrivateFile($quote);
            }
        }

        abort(403, 'You are not authorized to access this PDF.');
    }

    /**
     * Helper to stream private file from local disk.
     */
    protected function streamPrivateFile(Quote $quote): StreamedResponse
    {
        if (! $quote->pdf_path || ! Storage::disk('local')->exists($quote->pdf_path)) {
            abort(404, 'PDF not found');
        }

        // Optional audit log
        Log::info('PDF downloaded', ['user_id' => Auth::id(), 'quote_id' => $quote->id]);

        return Storage::disk('local')->download($quote->pdf_path, $quote->quote_number . '.pdf');
    }

    /**
     * Generate sequential quote number: QT-YYYY-XXX (naive).
     */
    protected function generateQuoteNumber(): string
    {
        $year = date('Y');
        $prefix = setting('quote_prefix', 'QT');
        $formattedPrefix = "{$prefix}-{$year}-";
        $count = DB::table('quotes')->where('quote_number', 'like', $formattedPrefix . '%')->count();
        $seq = $count + 1;
        return $formattedPrefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    // Send quote to customer (mark Sent and email)
    public function send(Request $request, Quote $quote)
    {
        // authorize if needed: $this->authorize('send', $quote);

        if ($quote->status === 'Sent') {
            return response()->json(['status' => 'error', 'message' => 'Quote already sent.'], 422);
        }

        $quote->status = 'Sent';
        $quote->sent_at = now(); // optional timestamp column
        $quote->save();

        // send mail (wrap in try/catch)
        try {
            $customer = optional($quote->project)->customer;
            if ($customer && $customer->email) {
                Mail::to($customer->email)->send(new QuoteSentMail($quote));
            }
        } catch (\Throwable $e) {
            Log::error('Quote send mail failed: '.$e->getMessage());
            // still return success but indicate email failed
            return response()->json([
                'status' => 'success',
                'message' => 'Quote marked as Sent but email failed to send.',
                'status_label' => $quote->status,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Quote sent to customer.',
            'status_label' => $quote->status,
        ]);
    }

    // Approve quote (mark Approved and email)
    public function approve(Request $request, Quote $quote)
    {
        // authorize if needed
        if ($quote->status === 'Approved') {
            return response()->json(['status' => 'error', 'message' => 'Quote already approved.'], 422);
        }

        $quote->status = 'Approved';
        $quote->approved_at = now(); // optional timestamp
        $quote->save();

        try {
            $customer = optional($quote->project)->customer;
            if ($customer && $customer->email) {
                Mail::to($customer->email)->send(new QuoteStatusChangedMail($quote, 'Approved'));
            }
        } catch (\Throwable $e) {
            \Log::error('Quote approve mail failed: '.$e->getMessage());
            return response()->json([
                'status' => 'success',
                'message' => 'Quote approved but email failed to send.',
                'status_label' => $quote->status,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Quote approved and customer notified.',
            'status_label' => $quote->status,
        ]);
    }

    // Reject quote (mark Rejected and email)
    public function reject(Request $request, Quote $quote)
    {
        // authorize if needed
        if ($quote->status === 'Rejected') {
            return response()->json(['status' => 'error', 'message' => 'Quote already rejected.'], 422);
        }

        $quote->status = 'Rejected';
        $quote->rejected_at = now(); // optional
        $quote->save();

        try {
            $customer = optional($quote->project)->customer;
            if ($customer && $customer->email) {
                Mail::to($customer->email)->send(new QuoteStatusChangedMail($quote, 'Rejected'));
            }
        } catch (\Throwable $e) {
            \Log::error('Quote reject mail failed: '.$e->getMessage());
            return response()->json([
                'status' => 'success',
                'message' => 'Quote rejected but email failed to send.',
                'status_label' => $quote->status,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Quote rejected and customer notified.',
            'status_label' => $quote->status,
        ]);
    }

    /**
     * Delete a quote and all associated items
     */
    public function destroy(Quote $quote)
    {
        try {
            // Delete associated PDF file if exists
            if ($quote->pdf_path && Storage::exists($quote->pdf_path)) {
                Storage::delete($quote->pdf_path);
            }

            // Delete all quote items (cascade should handle this, but explicit is safer)
            $quote->items()->delete();

            // Delete the quote
            $quote->delete();

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Quote deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Quote deletion failed: ' . $e->getMessage(), [
                'quote_id' => $quote->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'success' => false,
                'message' => 'Failed to delete quote: ' . $e->getMessage(),
            ], 500);
        }
    }
}
