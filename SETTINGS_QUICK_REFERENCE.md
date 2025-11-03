# Settings Module - Quick Reference Guide

## 🚀 Quick Start

### Accessing Settings

```php
// Get a setting
$value = setting('key_name', 'default_value');

// Set a setting
setting(['key_name' => 'new_value']);
```

### Common Settings

```php
// Tax & Currency
$taxRate = setting('tax_rate', 0.08);              // 0.08
$currencySymbol = setting('currency_symbol', '$'); // $
$currencyCode = setting('currency_code', 'USD');   // USD

// Company Info
$companyName = setting('company_name');            // Stone Cobblers Inc.
$companyEmail = setting('company_email');          // info@stonecobblers.com
$companyPhone = setting('company_phone');          // (555) 123-4567

// Quote Settings
$quotePrefix = setting('quote_prefix', 'QT');      // QT
$quoteExpiry = setting('quote_expiry_days', 30);   // 30
$quoteTerms = setting('quote_terms');              // Payment due...
```

## 📍 Where Settings Are Used

### 1. Quote Generation
**File:** `app/Http/Controllers/QuoteController.php`
```php
// Tax calculation
$taxRate = setting('tax_rate', 0.08);
$taxCost = $isTaxable ? ($lineTotal * $taxRate) : 0;

// Quote number
$prefix = setting('quote_prefix', 'QT');
$quoteNumber = "{$prefix}-{$year}-{$seq}";

// Expiry date
$expiryDays = setting('quote_expiry_days', 30);
$expiresAt = Carbon::now()->addDays($expiryDays);

// Company info for PDF
'companyName' => setting('company_name'),
'companyPhone' => setting('company_phone'),
// ... etc
```

### 2. Quote Creation Form
**File:** `resources/views/admin/quote/create.blade.php`
```javascript
// JavaScript constants
const TAX_RATE = {{ setting('tax_rate', 0.08) }};
const CURRENCY_SYMBOL = '{{ setting('currency_symbol', '$') }}';
const CURRENCY_CODE = '{{ setting('currency_code', 'USD') }}';
```

```blade
<!-- Blade display -->
<div class="summary-label">
    {{ setting('tax_label', 'Tax') }} ({{ setting('tax_rate', 0.08) * 100 }}%)
</div>
```

### 3. PDF Template
**File:** `resources/views/admin/quote/pdf.blade.php`
```blade
<!-- Company header -->
{{ $companyAddress }}, {{ $companyCity }}, {{ $companyState }}
Phone: {{ $companyPhone }}
Email: {{ $companyEmail }}

<!-- Tax display -->
<td>{{ $taxRate ? 'Tax (' . ($taxRate * 100) . '%)' : 'Tax' }}</td>

<!-- Expiry message -->
<div>THIS QUOTE IS VALID FOR {{ setting('quote_expiry_days', 30) }} DAYS</div>

<!-- Terms & footer -->
@if($quoteTerms)
    <strong>Terms:</strong> {!! nl2br(e($quoteTerms)) !!}
@endif
@if($quoteFooter)
    <em>{{ $quoteFooter }}</em>
@endif
```

### 4. Email Notifications
**File:** `app/Mail/QuoteSentMail.php`
```php
public function build()
{
    $companyName = setting('company_name', 'Stone Cobblers Inc.');
    $fromEmail = setting('email_from_address', 'noreply@stonecobblers.com');
    $fromName = setting('email_from_name', $companyName);
    
    return $this->from($fromEmail, $fromName)
                ->subject("Quote from {$companyName}")
                ->markdown('emails.quotes.sent');
}
```

### 5. Dashboard
**File:** `resources/views/admin/dashboard/index.blade.php`
```blade
<div>
    <strong>Company:</strong> {{ setting('company_name') }}
</div>
<div>
    <strong>Tax Rate:</strong> {{ setting('tax_rate', 0.08) * 100 }}%
</div>
<div>
    <strong>Currency:</strong> {{ setting('currency_symbol') }}
</div>
```

## 🛠️ Helper Functions

### format_currency()
```php
// Format amount with currency symbol
format_currency(1250.50);           // $1,250.50
format_currency(1250.50, false);    // 1,250.50 (no symbol)
```

### get_tax_rate()
```php
// Get current tax rate
$rate = get_tax_rate();  // 0.08
```

### calculate_tax()
```php
// Calculate tax for amount
$tax = calculate_tax(1000);  // 80.00 (at 8% rate)
```

### company_info()
```php
// Get all company info
$info = company_info();
// Returns: ['name' => '...', 'email' => '...', ...]

// Get specific field
$name = company_info('name');    // Stone Cobblers Inc.
$email = company_info('email');  // info@stonecobblers.com
$phone = company_info('phone');  // (555) 123-4567
```

## 🎨 Adding Settings to New Features

### In Controllers
```php
class YourController extends Controller
{
    public function yourMethod()
    {
        // Get settings
        $taxRate = setting('tax_rate', 0.08);
        $companyName = setting('company_name');
        
        // Use in logic
        $total = $subtotal * (1 + $taxRate);
        
        return view('your.view', [
            'companyName' => $companyName,
            'total' => $total,
        ]);
    }
}
```

### In Views (Blade)
```blade
{{-- Display settings --}}
<h1>{{ setting('company_name') }}</h1>
<p>Tax: {{ setting('tax_rate', 0.08) * 100 }}%</p>

{{-- Use in calculations --}}
@php
    $taxRate = setting('tax_rate', 0.08);
    $total = $subtotal * (1 + $taxRate);
@endphp
```

### In JavaScript
```blade
<script>
    // Pass settings to JavaScript
    const APP_SETTINGS = {
        taxRate: {{ setting('tax_rate', 0.08) }},
        currency: '{{ setting('currency_symbol', '$') }}',
        companyName: '{{ setting('company_name') }}',
    };
    
    // Use in calculations
    function calculateTax(amount) {
        return amount * APP_SETTINGS.taxRate;
    }
</script>
```

## 📊 All Available Settings

### General
- `app_name` - Application name
- `timezone` - Default timezone
- `date_format` - Date format
- `time_format` - Time format (12h/24h)

### Company
- `company_name` - Company name
- `company_email` - Company email
- `company_phone` - Company phone
- `company_address` - Street address
- `company_city` - City
- `company_state` - State/Province
- `company_zipcode` - ZIP/Postal code
- `company_website` - Company website

### Tax & Pricing
- `tax_rate` - Tax rate (0.08 = 8%)
- `tax_label` - Tax label ("Sales Tax")
- `currency_symbol` - Currency symbol ($)
- `currency_code` - Currency code (USD)

### Quote
- `quote_prefix` - Quote number prefix (QT)
- `quote_expiry_days` - Expiry days (30)
- `quote_terms` - Terms and conditions
- `quote_footer` - Quote footer text

### Email
- `email_from_name` - Email from name
- `email_from_address` - Email from address
- `enable_email_notifications` - Enable/disable emails

### PDF
- `pdf_page_size` - Page size (letter/a4)
- `pdf_orientation` - Orientation (portrait/landscape)

## 🔍 Troubleshooting

### Settings Not Updating?
```bash
# Clear cache
php artisan cache:clear

# Or in code
Cache::forget('settings.all');
```

### Check Current Settings
```bash
# In tinker
php artisan tinker
>>> setting('tax_rate')
=> 0.08
>>> setting()->all()
```

### Add New Setting
```php
// Via code
Setting::create([
    'key' => 'new_setting',
    'value' => 'value',
    'type' => 'string',
    'category' => 'general',
    'description' => 'Description',
]);

// Via helper
setting(['new_setting' => 'value']);
```

## 📝 Best Practices

### ✅ DO:
- Always provide default values
- Use type casting when needed
- Cache settings in variables if used multiple times
- Use descriptive setting keys
- Document custom settings

### ❌ DON'T:
- Store sensitive data (use .env instead)
- Store frequently changing data
- Use settings for session/request data
- Forget to clear cache after manual DB changes

## 🎯 Common Patterns

### Pattern 1: Tax Calculation
```php
$taxRate = setting('tax_rate', 0.08);
$taxAmount = $subtotal * $taxRate;
$total = $subtotal + $taxAmount;
```

### Pattern 2: Company Info Display
```php
$company = [
    'name' => setting('company_name'),
    'email' => setting('company_email'),
    'phone' => setting('company_phone'),
];

// Or use helper
$company = company_info();
```

### Pattern 3: Currency Formatting
```php
$amount = 1250.50;
$formatted = format_currency($amount);  // $1,250.50
echo $formatted;
```

### Pattern 4: Conditional Features
```php
if (setting('enable_email_notifications', true)) {
    // Send email
    Mail::to($user)->send(new NotificationMail());
}
```

## 📞 Support

For help with settings:
1. Check the documentation files
2. Review the implementation summary
3. Check the usage examples file
4. Inspect the code in QuoteController for real examples

---

**Quick Access:**
- Admin Settings Page: `/admin/settings`
- Settings Model: `app/Models/Setting.php`
- Helper Functions: `app/Helpers.php` & `app/Helpers/settings_helper.php`
- Controller: `app/Http/Controllers/SettingController.php`

