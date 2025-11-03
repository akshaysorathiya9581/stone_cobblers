# Settings Module - Quick Usage Examples

## Common Use Cases

### 1. Using Settings in Quote Generation

```php
// In QuoteController.php
public function generateQuote(Request $request)
{
    // Get settings
    $taxRate = setting('tax_rate', 0.08);
    $quotePrefix = setting('quote_prefix', 'QT');
    $expiryDays = setting('quote_expiry_days', 30);
    $companyName = setting('company_name');
    
    // Create quote
    $quote = new Quote();
    $quote->number = $quotePrefix . '-' . time();
    $quote->company_name = $companyName;
    $quote->subtotal = $request->subtotal;
    $quote->tax = $request->subtotal * $taxRate;
    $quote->total = $quote->subtotal + $quote->tax;
    $quote->expires_at = now()->addDays($expiryDays);
    $quote->save();
    
    return response()->json(['success' => true, 'quote' => $quote]);
}
```

### 2. Displaying Company Info in PDF

```blade
<!-- In resources/views/pdf/quote.blade.php -->
<div class="company-header">
    <h1>{{ setting('company_name') }}</h1>
    <p>{{ setting('company_address') }}</p>
    <p>{{ setting('company_city') }}, {{ setting('company_state') }} {{ setting('company_zipcode') }}</p>
    <p>Phone: {{ setting('company_phone') }}</p>
    <p>Email: {{ setting('company_email') }}</p>
    <p>Web: {{ setting('company_website') }}</p>
</div>

<div class="quote-footer">
    {{ setting('quote_footer', 'Thank you for your business!') }}
</div>

<div class="terms">
    <h3>Terms & Conditions</h3>
    <p>{{ setting('quote_terms') }}</p>
</div>
```

### 3. Email Notifications

```php
// In NotificationService.php
public function sendQuoteEmail($quote, $recipient)
{
    if (!setting('enable_email_notifications', true)) {
        return; // Notifications disabled
    }
    
    $fromName = setting('email_from_name', 'Stone Cobblers');
    $fromAddress = setting('email_from_address', 'noreply@stonecobblers.com');
    
    Mail::to($recipient)
        ->from($fromAddress, $fromName)
        ->send(new QuoteEmail($quote));
}
```

### 4. Dynamic Tax Calculation

```javascript
// In create.blade.php
const TAX_RATE = {{ setting('tax_rate', 0.08) }};

function calculateTax(subtotal) {
    return subtotal * TAX_RATE;
}

function updateTotal() {
    const subtotal = calculateSubtotal();
    const tax = calculateTax(subtotal);
    const total = subtotal + tax;
    
    $('#subtotal').text(currency(subtotal));
    $('#tax').text(currency(tax));
    $('#total').text(currency(total));
}
```

### 5. Currency Formatting

```php
// Create a helper function in app/Helpers.php
function format_currency($amount)
{
    $symbol = setting('currency_symbol', '$');
    $code = setting('currency_code', 'USD');
    
    return $symbol . number_format($amount, 2);
}

// Usage
echo format_currency(1250.50); // Output: $1,250.50
```

### 6. Conditional Features

```php
// In your controller or middleware
public function showAdvancedFeatures()
{
    $advancedEnabled = setting('enable_advanced_features', false);
    
    if (!$advancedEnabled) {
        return redirect()->back()
            ->with('error', 'Advanced features are disabled');
    }
    
    return view('admin.advanced');
}
```

### 7. Date/Time Formatting

```php
// In a service or helper
public function formatDate($date)
{
    $format = setting('date_format', 'MM/DD/YYYY');
    $timezone = setting('timezone', 'America/New_York');
    
    // Convert format to Carbon format
    $carbonFormat = str_replace(
        ['MM', 'DD', 'YYYY'],
        ['m', 'd', 'Y'],
        $format
    );
    
    return Carbon::parse($date)
        ->timezone($timezone)
        ->format($carbonFormat);
}
```

### 8. PDF Configuration

```php
// In PDF generation service
public function generatePDF($quote)
{
    $pageSize = setting('pdf_page_size', 'letter');
    $orientation = setting('pdf_orientation', 'portrait');
    
    $pdf = PDF::loadView('pdf.quote', compact('quote'))
        ->setPaper($pageSize, $orientation);
    
    return $pdf->download('quote-' . $quote->number . '.pdf');
}
```

### 9. Batch Update Settings

```php
// In settings update controller or admin panel
public function updateCompanyInfo(Request $request)
{
    setting([
        'company_name' => $request->name,
        'company_email' => $request->email,
        'company_phone' => $request->phone,
        'company_address' => $request->address,
        'company_city' => $request->city,
        'company_state' => $request->state,
        'company_zipcode' => $request->zipcode,
    ]);
    
    return response()->json(['success' => true]);
}
```

### 10. Type-Safe Settings Access

```php
// For boolean settings
$emailEnabled = (bool) setting('enable_email_notifications', true);

// For integer settings
$expiryDays = (int) setting('quote_expiry_days', 30);

// For decimal settings
$taxRate = (float) setting('tax_rate', 0.08);

// For JSON settings (if you add JSON type settings)
$features = json_decode(setting('enabled_features', '[]'), true);
```

### 11. Dashboard Stats with Settings

```blade
<!-- In dashboard.blade.php -->
<div class="stats-card">
    <h3>Company Information</h3>
    <table>
        <tr>
            <td>Company:</td>
            <td>{{ setting('company_name') }}</td>
        </tr>
        <tr>
            <td>Tax Rate:</td>
            <td>{{ setting('tax_rate', 0.08) * 100 }}%</td>
        </tr>
        <tr>
            <td>Quote Expiry:</td>
            <td>{{ setting('quote_expiry_days', 30) }} days</td>
        </tr>
    </table>
</div>
```

### 12. Middleware for Feature Flags

```php
// Create FeatureFlagMiddleware.php
public function handle($request, Closure $next, $feature)
{
    $enabled = setting("feature_{$feature}_enabled", false);
    
    if (!$enabled) {
        abort(403, "Feature '{$feature}' is not enabled");
    }
    
    return $next($request);
}

// In routes/web.php
Route::middleware(['auth', 'feature:advanced_reporting'])
    ->get('/admin/reports/advanced', [ReportController::class, 'advanced']);
```

### 13. API Response with Settings

```php
// In API controller
public function getPublicSettings()
{
    $settings = Setting::where('is_public', true)
        ->get()
        ->pluck('value', 'key');
    
    return response()->json([
        'success' => true,
        'settings' => $settings,
    ]);
}
```

### 14. Settings Validation

```php
// In SettingController update method
public function updateTaxRate(Request $request)
{
    $request->validate([
        'tax_rate' => 'required|numeric|min:0|max:1',
    ]);
    
    Setting::set('tax_rate', $request->tax_rate, 'decimal', 'tax');
    
    return response()->json([
        'success' => true,
        'message' => 'Tax rate updated successfully',
    ]);
}
```

### 15. Cache Warming

```php
// In a scheduled task or initialization
public function warmSettingsCache()
{
    // Force reload all settings into cache
    Cache::forget('settings.all');
    
    $settings = Setting::all()->pluck('value', 'key')->toArray();
    
    Cache::put('settings.all', $settings, 86400);
    
    Log::info('Settings cache warmed successfully');
}
```

## Pro Tips

1. **Always provide defaults** when accessing settings to prevent null reference errors
2. **Use type casting** when you know the expected type
3. **Cache settings** in variables if used multiple times in the same request
4. **Use categories** to organize settings logically
5. **Document custom settings** in the seeder for team awareness

## Testing Settings

```php
// In tests/Feature/SettingTest.php
public function test_can_get_setting()
{
    Setting::set('test_key', 'test_value');
    
    $value = setting('test_key');
    
    $this->assertEquals('test_value', $value);
}

public function test_setting_with_default()
{
    $value = setting('non_existent_key', 'default');
    
    $this->assertEquals('default', $value);
}
```

---

**Remember:** Settings are cached for 24 hours. Changes will be reflected immediately due to automatic cache invalidation on updates.

