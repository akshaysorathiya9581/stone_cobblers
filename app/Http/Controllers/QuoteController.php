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
        $KITCHEN_TOP = KitchenQuote::where('type', 'KITCHEN_TOP')->pluck('cost', 'project')->toArray();
        $KITCHEN_MANUFACTURER = KitchenQuote::where('type', 'KITCHEN_MANUFACTURER')->pluck('cost', 'project')->toArray();
        $KITCHEN_MARGIN_MARKUP = KitchenQuote::where('type', 'KITCHEN_MARGIN_MARKUP')->pluck('cost', 'project')->toArray();
        $KITCHEN_DELIVERY = KitchenQuote::where('type', 'KITCHEN_DELIVERY')->pluck('cost', 'project')->toArray();
        $KITCHEN_BUFFER = KitchenQuote::where('type', 'KITCHEN_BUFFER')->pluck('cost', 'project')->toArray();

        // You will likely pass clients/projects list to select project
        $user = auth()->user();

        // dd($user);
        if ($user && $user->role === 'admin') {
            $projects  = Project::all();
        } else {
            $projects  = $user ? Project::where('user_id', $user->id)->get() : collect();
        }

        return view('admin.quote.create', compact('KITCHEN_TOP', 'KITCHEN_MANUFACTURER', 'KITCHEN_MARGIN_MARKUP', 'KITCHEN_DELIVERY', 'KITCHEN_BUFFER', 'projects'));
    }

   /**
     * Store a new quote (handles full multi-step form payload)
     */
    public function store(Request $request)
    {
        // Basic server-side validation (arrays / nested)
        $request->validate([
            'project_id'               => 'required|exists:projects,id',
            'kitchen.name'             => 'nullable|array',
            'kitchen.qty'              => 'nullable|array',
            'kitchen.unit_price'       => 'nullable|array',
            'manufacturer.name'        => 'nullable|array',
            'manufacturer.qty'         => 'nullable|array',
            'manufacturer.unit_price'  => 'nullable|array',
            'delivery'                 => 'nullable|array',
            'buffer'                   => 'nullable|array',
            'margin'                   => 'nullable|array',
            'final_total'              => 'nullable|numeric',
            'list_price'               => 'nullable|numeric',
            'expires_at'               => 'nullable|date',
            'tax'                      => 'nullable|numeric',
            'discount'                 => 'nullable|numeric',
            'price_buffer'             => 'nullable|numeric',
            'phone_call_buffer'        => 'nullable|numeric',
            'dba_surcharge'            => 'nullable|numeric',
            'hardware_qty'             => 'nullable|numeric',
            'is_kitchen'               => 'nullable|in:0,1,2',
            'is_vanity'                => 'nullable|in:0,1,2',
        ]);

        $user = Auth::user();

        // helpers to ensure consistent DB storage formatting
        $toUnitPrice = fn($v) => number_format((float)$v, 4, '.', '');
        $toQty       = fn($v) => number_format((float)$v, 2, '.', '');
        $toLine      = fn($v) => number_format((float)$v, 4, '.', '');

        DB::beginTransaction();

        try {
            // generate quote number (implement in your controller or adjust)
            $quoteNumber = $this->generateQuoteNumber();

            $expiresAt = $request->filled('expires_at')
                ? Carbon::parse($request->input('expires_at'))
                : Carbon::now()->addDays(30);

            $isKitchen = (bool)$request->input('is_kitchen', false);
            $isVanity  = (bool)$request->input('is_vanity', false);

            // Create quote master
            $quote = Quote::create([
                'user_id'      => $user->id,
                'project_id'   => $request->input('project_id'),
                'quote_number' => $quoteNumber,
                'customer_name'=> $request->input('customer_name'),
                'project_name' => $request->input('project_name'),
                'status'       => 'Draft',
                'subtotal'     => 0,
                'tax'          => 0,
                'discount'     => $request->input('discount', 0),
                'total'        => 0,
                'pdf_path'     => null,
                'is_kitchen'   => $isKitchen,
                'is_vanity'    => $isVanity,
                'expires_at'   => $expiresAt,
            ]);

            $computedSubtotal = 0.0;

            // ---- 1) Kitchen items ----
            $kNames = $request->input('kitchen.name', []);
            $kQtys  = $request->input('kitchen.qty', []);
            $kUnits = $request->input('kitchen.unit_price', []);
            $kCount = max(count($kNames), count($kQtys), count($kUnits));
            for ($i = 0; $i < $kCount; $i++) {
                $name = trim((string)($kNames[$i] ?? ''));
                if ($name === '') continue;
                $qty  = isset($kQtys[$i]) ? (float)$kQtys[$i] : 0.0;
                $unit = isset($kUnits[$i]) ? (float)$kUnits[$i] : 0.0;
                $line = $qty * $unit;
                $item = $quote->items()->create([
                    'name'       => $name,
                    'unit_price' => $toUnitPrice($unit),
                    'qty'        => $toQty($qty),
                    'line_total' => $toLine($line),
                ]);
                $computedSubtotal += (float)$item->line_total;
            }

            // ---- 2) Manufacturer items ----
            $mNames = $request->input('manufacturer.name', []);
            $mQtys  = $request->input('manufacturer.qty', []);
            $mUnits = $request->input('manufacturer.unit_price', []);
            $mCount = max(count($mNames), count($mQtys), count($mUnits));
            for ($i = 0; $i < $mCount; $i++) {
                $name = trim((string)($mNames[$i] ?? ''));
                if ($name === '') continue;
                $qty  = isset($mQtys[$i]) ? (float)$mQtys[$i] : 0.0;
                $unit = isset($mUnits[$i]) ? (float)$mUnits[$i] : 0.0;
                $line = $qty * $unit;
                $item = $quote->items()->create([
                    'name'       => $name,
                    'unit_price' => $toUnitPrice($unit),
                    'qty'        => $toQty($qty),
                    'line_total' => $toLine($line),
                ]);
                $computedSubtotal += (float)$item->line_total;
            }

            // ---- 3) Delivery items (nested) ----
            $delivery = $request->input('delivery', []);
            if (is_array($delivery)) {
                foreach ($delivery as $key => $vals) {
                    if (!is_array($vals)) continue;
                    $qty  = isset($vals['qty']) ? (float)$vals['qty'] : 0.0;
                    $unit = isset($vals['unit_price']) ? (float)$vals['unit_price'] : 0.0;
                    $line = $qty * $unit;
                    if ($qty == 0 && $line == 0) continue;
                    $label = $vals['name'] ?? strtoupper(str_replace('_', ' ', $key));
                    $item = $quote->items()->create([
                        'name'       => $label,
                        'unit_price' => $toUnitPrice($unit),
                        'qty'        => $toQty($qty),
                        'line_total' => $toLine($line),
                    ]);
                    $computedSubtotal += (float)$item->line_total;
                }
            }

            // ---- 4) Buffer rows ----
            $buffer = $request->input('buffer', []);
            if (is_array($buffer)) {
                foreach ($buffer as $slug => $vals) {
                    if (!is_array($vals)) continue;
                    $unit = isset($vals['unit']) ? (float)$vals['unit'] : 0.0;
                    $qty  = isset($vals['qty']) ? (float)$vals['qty'] : 0.0;
                    $line = isset($vals['line_total']) ? (float)$vals['line_total'] : null;

                    if ($line === null) {
                        if ($unit && $qty) $line = $unit * $qty;
                        else $line = $qty; // input-mode absolute
                    }

                    if ((float)$line == 0.0) continue;

                    $label = $vals['name'] ?? str_replace('_', ' ', strtoupper($slug));
                    $item = $quote->items()->create([
                        'name'       => $label,
                        'unit_price' => $toUnitPrice($unit ?: $line),
                        'qty'        => $toQty($qty ?: 1),
                        'line_total' => $toLine($line),
                    ]);
                    $computedSubtotal += (float)$item->line_total;
                }
            }

            // ---- 5) Margins: save to meta (do not auto-apply to totals) ----
            $margins = $request->input('margin', []);
            $savedMargins = [];
            if (is_array($margins) && count($margins)) {
                foreach ($margins as $slug => $vals) {
                    $mname = $vals['name'] ?? $slug;
                    $mval  = isset($vals['value']) ? (float)$vals['value'] : null;
                    $mres  = isset($vals['result']) ? (float)$vals['result'] : null;
                    $savedMargins[$slug] = [
                        'name' => $mname,
                        'multiplier' => $mval,
                        'percent' => $mres,
                    ];
                }

                try {
                    if (Schema::hasColumn('quotes', 'meta')) {
                        $existing = $quote->meta ?? [];
                        if (is_string($existing)) $existing = json_decode($existing, true) ?: [];
                        $existing['margins'] = $savedMargins;
                        $quote->meta = $existing;
                        $quote->save();
                    } else {
                        Log::info('Margins received but quotes.meta column not present. Margins:', $savedMargins);
                    }
                } catch (\Throwable $e) {
                    Log::warning('Failed to persist margins into quote.meta: ' . $e->getMessage());
                }
            }

            // ---- 6) Extras (price_buffer, phone_call_buffer, dba_surcharge) ----
            $extrasMap = [
                'price_buffer'      => 'Price Change Buffer',
                'phone_call_buffer' => 'Phone Call Buffer',
                'dba_surcharge'     => 'DBA Surcharge',
            ];
            foreach ($extrasMap as $field => $label) {
                if ($request->filled($field)) {
                    $val = (float)$request->input($field, 0);
                    if ($val != 0) {
                        $item = $quote->items()->create([
                            'name'       => $label,
                            'unit_price' => $toUnitPrice($val),
                            'qty'        => $toQty(1),
                            'line_total' => $toLine($val),
                        ]);
                        $computedSubtotal += (float)$item->line_total;
                    }
                }
            }

            // ---- 7) Hardware quantity (if provided) ----
            if ($request->filled('hardware_qty')) {
                $hardwareQty = (float)$request->input('hardware_qty', 0);
                if ($hardwareQty > 0) {
                    $item = $quote->items()->create([
                        'name'       => 'Hardware Quantity',
                        'unit_price' => $toUnitPrice(1),
                        'qty'        => $toQty($hardwareQty),
                        'line_total' => $toLine($hardwareQty),
                    ]);
                    $computedSubtotal += (float)$item->line_total;
                }
            }

            // ---- 8) Totals ----
            $subtotal = round($computedSubtotal, 2);
            $discount = (float)$request->input('discount', 0);
            $taxInput = $request->input('tax', null);
            $tax = ($taxInput !== null) ? round((float)$taxInput, 2) : 0.00;
            $total = round($subtotal + $tax - $discount, 2);

            $quote->update([
                'subtotal' => number_format($subtotal, 2, '.', ''),
                'tax'      => number_format($tax, 2, '.', ''),
                'discount' => number_format($discount, 2, '.', ''),
                'total'    => number_format($total, 2, '.', ''),
                'saved_to_db_at' => now(),
            ]);

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
                'companyName' => config('app.name'),
                'companyAddress' => "317 West Boylston St, West Boylston, MA 01583 774-261-4445",
                'companyLogo' => $companyLogo,
            ];

            // generate pdf
            $pdf = Pdf::loadView('admin.quote.pdf', $viewData)->setPaper('a4', 'portrait');
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
            'message'       => 'Quote saved and PDF generated',
            'quote_id'      => $quote->id,
            'quote_number'  => $quote->quote_number,
            'pdf_path'      => $storagePath,
            'subtotal'      => (float)$subtotal,
            'tax'           => (float)$tax,
            'discount'      => (float)$discount,
            'total'         => (float)$total,
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
        $prefix = "QT-{$year}-";
        $count = DB::table('quotes')->where('quote_number', 'like', $prefix . '%')->count();
        $seq = $count + 1;
        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
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
}
