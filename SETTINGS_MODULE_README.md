# Settings Module Documentation

## Overview
The Settings Module for Stone Cobblers provides a comprehensive solution for managing application configuration through a database-driven approach with caching for optimal performance.

## Features
- ✅ Database-driven settings management
- ✅ Type casting (string, boolean, integer, decimal, json, file)
- ✅ Category-based organization
- ✅ Built-in caching for performance
- ✅ Beautiful tabbed admin interface
- ✅ AJAX-powered updates
- ✅ Module-based access control
- ✅ Helper functions for easy access
- ✅ Default settings seeder

## Installation Complete ✓

All components have been successfully installed:

1. **Database Migration** - `database/migrations/2025_11_03_062839_create_settings_table.php`
2. **Model** - `app/Models/Setting.php`
3. **Controller** - `app/Http/Controllers/SettingController.php`
4. **Helper Functions** - `app/Helpers/settings_helper.php`
5. **Seeder** - `database/seeders/SettingSeeder.php`
6. **Views** - `resources/views/admin/settings/index.blade.php`
7. **Routes** - Added to `routes/web.php`
8. **Sidebar Menu** - Updated in `app/Providers/AppServiceProvider.php`

## Database Structure

The `settings` table includes:
- `id` - Primary key
- `key` - Unique setting identifier
- `value` - Setting value (can be any type)
- `type` - Data type (string, boolean, integer, decimal, json, file)
- `category` - Organization category (general, company, tax, email, quote, pdf)
- `description` - Human-readable description
- `is_public` - Whether non-admins can see this setting
- `created_at`, `updated_at` - Timestamps

## Usage Examples

### Using the Helper Function

```php
// Get a setting value
$appName = setting('app_name');
$taxRate = setting('tax_rate', 0.08); // with default value

// Set a single setting
setting(['app_name' => 'My New App Name']);

// Set multiple settings
setting([
    'company_name' => 'Stone Cobblers Inc.',
    'company_email' => 'info@stonecobblers.com',
    'tax_rate' => 0.08
]);
```

### Using the Model Directly

```php
use App\Models\Setting;

// Get a setting
$value = Setting::get('key_name', 'default_value');

// Set a setting
Setting::set('key_name', 'value', 'string', 'category');

// Get settings by category
$companySettings = Setting::byCategory('company');

// Get all settings grouped by category
$allSettings = Setting::grouped();

// Create a new setting
Setting::create([
    'key' => 'custom_setting',
    'value' => 'custom value',
    'type' => 'string',
    'category' => 'general',
    'description' => 'My custom setting',
    'is_public' => false,
]);
```

### In Blade Templates

```blade
{{-- Get a setting --}}
<h1>{{ setting('app_name') }}</h1>

{{-- Company information --}}
<p>{{ setting('company_email') }}</p>
<p>{{ setting('company_phone') }}</p>

{{-- With default value --}}
<p>Tax Rate: {{ setting('tax_rate', 0.08) * 100 }}%</p>
```

### In Controllers

```php
public function generateQuote()
{
    $taxRate = setting('tax_rate', 0.08);
    $quotePrefix = setting('quote_prefix', 'QT');
    $expiryDays = setting('quote_expiry_days', 30);
    
    // Use settings in your logic
    $quote = new Quote();
    $quote->number = $quotePrefix . '-' . time();
    $quote->tax_rate = $taxRate;
    $quote->expires_at = now()->addDays($expiryDays);
    $quote->save();
}
```

## Default Settings Categories

### 1. General Settings
- `app_name` - Application name
- `timezone` - Default timezone
- `date_format` - Date format (MM/DD/YYYY)
- `time_format` - Time format (12h or 24h)

### 2. Company Information
- `company_name` - Company legal name
- `company_email` - Company email
- `company_phone` - Company phone number
- `company_address` - Street address
- `company_city` - City
- `company_state` - State/Province
- `company_zipcode` - ZIP/Postal code
- `company_website` - Company website URL

### 3. Tax & Pricing
- `tax_rate` - Default tax rate (0.08 = 8%)
- `tax_label` - Tax label for display (e.g., "Sales Tax")
- `currency_symbol` - Currency symbol ($, €, £, etc.)
- `currency_code` - Currency code (USD, EUR, GBP, etc.)

### 4. Quote Settings
- `quote_prefix` - Quote number prefix (e.g., "QT")
- `quote_expiry_days` - Default quote expiry in days
- `quote_terms` - Default terms and conditions
- `quote_footer` - Quote footer text

### 5. Email Settings
- `email_from_name` - Email sender name
- `email_from_address` - Email sender address
- `enable_email_notifications` - Enable/disable email notifications

### 6. PDF Settings
- `pdf_page_size` - Page size (letter, a4, legal)
- `pdf_orientation` - Page orientation (portrait, landscape)

## Admin Interface

Access the settings page at: `/admin/settings`

Features:
- **Tabbed Interface** - Organized by category for easy navigation
- **Search Functionality** - Quickly find settings
- **Live Updates** - Save settings with AJAX (no page reload)
- **Type-Aware Inputs** - Checkboxes for booleans, number inputs for decimals, etc.
- **Descriptions** - Helpful descriptions for each setting
- **Floating Save Button** - Always accessible save button

## API Endpoints

### Get All Settings
```
GET /admin/settings
```

### Update Settings
```
POST /admin/settings
Content-Type: application/json

{
  "settings": {
    "app_name": "New App Name",
    "tax_rate": "0.085"
  }
}
```

### Get Single Setting
```
GET /admin/settings/{key}
```

### Create New Setting
```
POST /admin/settings/create
Content-Type: application/json

{
  "key": "custom_setting",
  "value": "value",
  "type": "string",
  "category": "general",
  "description": "Description",
  "is_public": false
}
```

### Delete Setting
```
DELETE /admin/settings/{key}
```

## Performance & Caching

Settings are automatically cached for 24 hours (86400 seconds) to improve performance. The cache is cleared automatically when:
- A setting is created
- A setting is updated
- A setting is deleted

Manual cache clearing:
```php
use Illuminate\Support\Facades\Cache;

Cache::forget('settings.all');
```

## Access Control

The Settings module is protected by the `module:settings` middleware. To grant access to users:

1. Ensure the user has the 'settings' module in their allowed modules
2. Check the `users` table `modules` field (JSON array)
3. Admin users (with `modules` = null) automatically have access to all modules

Example:
```json
{
  "modules": ["dashboard", "customers", "quotes", "settings"]
}
```

## Adding Custom Settings

### Via Seeder
Add new settings to `database/seeders/SettingSeeder.php`:

```php
[
    'key' => 'my_custom_setting',
    'value' => 'default value',
    'type' => 'string',
    'category' => 'custom',
    'description' => 'Description of my setting',
    'is_public' => false,
],
```

### Via Code
```php
Setting::create([
    'key' => 'feature_enabled',
    'value' => true,
    'type' => 'boolean',
    'category' => 'features',
    'description' => 'Enable new feature',
    'is_public' => false,
]);
```

### Via Admin Interface
Use the "Create Setting" API endpoint or add the feature to the UI.

## Best Practices

1. **Use Descriptive Keys** - Use snake_case like `company_email` instead of `ce`
2. **Provide Defaults** - Always provide default values when getting settings
3. **Cache Appropriately** - Settings are cached; don't use for frequently changing data
4. **Type Casting** - Use the correct type for automatic casting
5. **Categories** - Organize settings into logical categories
6. **Documentation** - Add descriptions to help other developers

## Troubleshooting

### Settings Not Showing
1. Check module access for the user
2. Verify the seeder has run: `php artisan db:seed --class=SettingSeeder`
3. Clear cache: `php artisan cache:clear`

### Settings Not Updating
1. Check CSRF token in AJAX requests
2. Verify routes are registered: `php artisan route:list | grep settings`
3. Check browser console for JavaScript errors

### Performance Issues
1. Ensure caching is enabled
2. Check cache driver configuration in `.env`
3. Consider using Redis for better performance

## Future Enhancements

Potential features to add:
- [ ] File upload support for logo/images
- [ ] Settings import/export (JSON/CSV)
- [ ] Settings versioning/history
- [ ] Settings validation rules
- [ ] Settings groups/nested categories
- [ ] Frontend settings management (for public settings)
- [ ] Settings encryption for sensitive data
- [ ] Multi-language support

## Support

For issues or questions about the Settings module, contact the development team or refer to the Laravel documentation for core concepts.

---

**Version:** 1.0.0  
**Last Updated:** November 3, 2025  
**Author:** Stone Cobblers Development Team

