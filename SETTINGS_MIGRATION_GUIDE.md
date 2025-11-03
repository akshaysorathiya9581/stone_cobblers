# Settings Module - Migration Guide

## Converting Hardcoded Values to Settings

This guide helps you migrate existing hardcoded configuration values to the new Settings module.

## Step-by-Step Migration Process

### Step 1: Identify Hardcoded Values

Look for these patterns in your codebase:
- Hardcoded tax rates
- Company information strings
- Configuration constants
- Magic numbers
- Email templates with hardcoded values

### Step 2: Replace with Settings

#### Example 1: Tax Rate

**Before:**
```php
// In QuoteController.php
const TAX_RATE = 0.08;

public function calculate($subtotal)
{
    $tax = $subtotal * self::TAX_RATE;
    return $subtotal + $tax;
}
```

**After:**
```php
// In QuoteController.php
public function calculate($subtotal)
{
    $taxRate = setting('tax_rate', 0.08);
    $tax = $subtotal * $taxRate;
    return $subtotal + $tax;
}
```

#### Example 2: Company Information

**Before:**
```blade
<!-- In email template -->
<div class="header">
    <h1>Stone Cobblers Inc.</h1>
    <p>123 Stone Street, New York, NY 10001</p>
    <p>Phone: (555) 123-4567</p>
</div>
```

**After:**
```blade
<!-- In email template -->
<div class="header">
    <h1>{{ setting('company_name') }}</h1>
    <p>{{ setting('company_address') }}, {{ setting('company_city') }}, {{ setting('company_state') }} {{ setting('company_zipcode') }}</p>
    <p>Phone: {{ setting('company_phone') }}</p>
</div>
```

#### Example 3: JavaScript Constants

**Before:**
```javascript
// In create.blade.php
<script>
const TAX_RATE = 0.08;
const CURRENCY_SYMBOL = '$';
</script>
```

**After:**
```blade
<script>
const TAX_RATE = {{ setting('tax_rate', 0.08) }};
const CURRENCY_SYMBOL = '{{ setting('currency_symbol', '$') }}';
</script>
```

#### Example 4: Config Values in .env

**Before:**
```php
// Using .env
$appName = env('APP_NAME', 'Stone Cobblers');
$taxRate = env('TAX_RATE', 0.08);
```

**After:**
```php
// Using settings (now editable via UI)
$appName = setting('app_name', 'Stone Cobblers');
$taxRate = setting('tax_rate', 0.08);
```

**Note:** Keep security-sensitive values (API keys, passwords) in .env, but move user-configurable values to settings.

### Step 3: Update Existing Code Files

#### Files to Review:

1. **Controllers**
   - `app/Http/Controllers/QuoteController.php` - Tax calculations
   - `app/Http/Controllers/CustomerController.php` - Email settings
   - All PDF generation logic

2. **Views**
   - All PDF templates in `resources/views/pdf/`
   - Email templates
   - Footer/header partials with company info

3. **JavaScript Files**
   - `resources/views/admin/quote/create.blade.php` - Tax rates
   - Any files with hardcoded currency or formatting

### Step 4: Update PDF Generation

**Before:**
```php
public function generatePDF($quote)
{
    $pdf = PDF::loadView('pdf.quote', compact('quote'))
        ->setPaper('letter', 'portrait');
    
    return $pdf->download('quote.pdf');
}
```

**After:**
```php
public function generatePDF($quote)
{
    $pageSize = setting('pdf_page_size', 'letter');
    $orientation = setting('pdf_orientation', 'portrait');
    
    $pdf = PDF::loadView('pdf.quote', compact('quote'))
        ->setPaper($pageSize, $orientation);
    
    return $pdf->download('quote.pdf');
}
```

### Step 5: Update Email Configuration

**Before:**
```php
Mail::to($customer->email)
    ->from('noreply@stonecobblers.com', 'Stone Cobblers')
    ->send(new QuoteEmail($quote));
```

**After:**
```php
$fromEmail = setting('email_from_address', 'noreply@stonecobblers.com');
$fromName = setting('email_from_name', 'Stone Cobblers');

Mail::to($customer->email)
    ->from($fromEmail, $fromName)
    ->send(new QuoteEmail($quote));
```

## Common Migration Patterns

### Pattern 1: Constant to Setting
```php
// Before
define('QUOTE_EXPIRY_DAYS', 30);

// After
$expiryDays = setting('quote_expiry_days', 30);
```

### Pattern 2: Class Constant to Setting
```php
// Before
class Quote extends Model
{
    const TAX_RATE = 0.08;
    const PREFIX = 'QT';
}

// After
class Quote extends Model
{
    public function getTaxRate()
    {
        return setting('tax_rate', 0.08);
    }
    
    public function getPrefix()
    {
        return setting('quote_prefix', 'QT');
    }
}
```

### Pattern 3: Config File to Setting
```php
// Before
// config/quote.php
return [
    'tax_rate' => 0.08,
    'expiry_days' => 30,
];

// Usage
$taxRate = config('quote.tax_rate');

// After
$taxRate = setting('tax_rate', 0.08);
```

## What Should NOT Be Migrated

Keep these in `.env` or config files:
- ❌ Database credentials
- ❌ API keys and secrets
- ❌ Application keys
- ❌ Debug settings
- ❌ Cache/session drivers
- ❌ Third-party service credentials

Move these to Settings:
- ✅ Tax rates
- ✅ Company information
- ✅ Display preferences
- ✅ User-facing text
- ✅ Feature flags
- ✅ Business rules (expiry days, etc.)

## Testing After Migration

Create a test checklist:

```php
// Test Settings Access
✓ Can access settings via helper function
✓ Can access settings via model
✓ Default values work correctly
✓ Cache is working (check performance)

// Test in Application
✓ Tax calculations use setting
✓ PDF generation works
✓ Email sending works
✓ Company info displays correctly
✓ Quote generation uses settings

// Test Admin Interface
✓ Can view settings page
✓ Can update settings via UI
✓ Changes reflect immediately
✓ Validation works
```

## Search and Replace Guide

Use these regex patterns to find candidates for migration:

1. **Find hardcoded tax rates:**
   ```regex
   0\.0\d+
   ```

2. **Find hardcoded company names:**
   ```regex
   ['"]Stone Cobblers[^'"]*['"]
   ```

3. **Find hardcoded email addresses:**
   ```regex
   ['"][a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}['"]
   ```

4. **Find class constants that might be settings:**
   ```regex
   const [A-Z_]+ = ['"0-9];
   ```

## Rollback Plan

If you need to rollback:

1. Keep backup of original code
2. Settings table can be dropped:
   ```bash
   php artisan migrate:rollback --step=1
   ```
3. Remove helper function from composer.json
4. Restore original hardcoded values

## Performance Considerations

### Before (No Caching)
```php
// Every request queries database
$taxRate = DB::table('settings')->where('key', 'tax_rate')->value('value');
```

### After (With Caching)
```php
// First request queries DB, subsequent requests use cache
$taxRate = setting('tax_rate', 0.08);
```

**Cache Duration:** 24 hours (86400 seconds)
**Auto-invalidation:** On any setting update

## Migration Checklist

- [ ] Identify all hardcoded configuration values
- [ ] Create settings for each value (via seeder or UI)
- [ ] Update controllers to use `setting()` helper
- [ ] Update views to use `setting()` helper
- [ ] Update JavaScript to use settings
- [ ] Update PDF generation logic
- [ ] Update email logic
- [ ] Test all affected functionality
- [ ] Update documentation
- [ ] Train users on settings management
- [ ] Monitor performance after deployment

## Example Pull Request Description

```markdown
## Settings Module Migration

### Changes
- Added Settings module with database-driven configuration
- Migrated tax rate from hardcoded 0.08 to `setting('tax_rate')`
- Migrated company information to settings
- Updated PDF generation to use configurable page size
- Updated email sending to use configurable from address

### Testing
- ✓ All existing tests pass
- ✓ Tax calculations work correctly
- ✓ PDF generation works
- ✓ Settings UI functional
- ✓ Cache performance verified

### Migration Notes
- Default values maintained for backward compatibility
- Settings can now be changed via `/admin/settings`
- Cache automatically invalidates on updates

### Rollback Plan
If needed, run: `php artisan migrate:rollback --step=1`
```

## Support

If you encounter issues during migration:
1. Check the cache is working: `php artisan cache:clear`
2. Verify seeder ran: `php artisan db:seed --class=SettingSeeder`
3. Check helper is loaded: `composer dump-autoload`
4. Review logs for errors

---

**Happy Migrating!** 🚀

