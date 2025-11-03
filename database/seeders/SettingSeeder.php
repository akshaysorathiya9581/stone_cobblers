<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'app_name',
                'value' => 'Stone Cobblers',
                'type' => 'string',
                'category' => 'general',
                'description' => 'Application name',
                'is_public' => true,
            ],
            [
                'key' => 'timezone',
                'value' => 'America/New_York',
                'type' => 'string',
                'category' => 'general',
                'description' => 'Default timezone',
                'is_public' => false,
            ],
            [
                'key' => 'date_format',
                'value' => 'MM/DD/YYYY',
                'type' => 'string',
                'category' => 'general',
                'description' => 'Date format',
                'is_public' => false,
            ],
            [
                'key' => 'time_format',
                'value' => '12h',
                'type' => 'string',
                'category' => 'general',
                'description' => 'Time format (12h or 24h)',
                'is_public' => false,
            ],

            // Company Information
            [
                'key' => 'company_name',
                'value' => 'Stone Cobblers Inc.',
                'type' => 'string',
                'category' => 'company',
                'description' => 'Company name',
                'is_public' => true,
            ],
            [
                'key' => 'company_email',
                'value' => 'info@stonecobblers.com',
                'type' => 'string',
                'category' => 'company',
                'description' => 'Company email',
                'is_public' => true,
            ],
            [
                'key' => 'company_phone',
                'value' => '(555) 123-4567',
                'type' => 'string',
                'category' => 'company',
                'description' => 'Company phone number',
                'is_public' => true,
            ],
            [
                'key' => 'company_address',
                'value' => '123 Stone Street',
                'type' => 'string',
                'category' => 'company',
                'description' => 'Company address',
                'is_public' => true,
            ],
            [
                'key' => 'company_city',
                'value' => 'New York',
                'type' => 'string',
                'category' => 'company',
                'description' => 'Company city',
                'is_public' => true,
            ],
            [
                'key' => 'company_state',
                'value' => 'NY',
                'type' => 'string',
                'category' => 'company',
                'description' => 'Company state',
                'is_public' => true,
            ],
            [
                'key' => 'company_zipcode',
                'value' => '10001',
                'type' => 'string',
                'category' => 'company',
                'description' => 'Company zipcode',
                'is_public' => true,
            ],
            [
                'key' => 'company_website',
                'value' => 'https://stonecobblers.com',
                'type' => 'string',
                'category' => 'company',
                'description' => 'Company website',
                'is_public' => true,
            ],

            // Tax & Pricing
            [
                'key' => 'tax_rate',
                'value' => '0.08',
                'type' => 'decimal',
                'category' => 'tax',
                'description' => 'Default tax rate (e.g., 0.08 for 8%)',
                'is_public' => false,
            ],
            [
                'key' => 'tax_label',
                'value' => 'Sales Tax',
                'type' => 'string',
                'category' => 'tax',
                'description' => 'Tax label to display',
                'is_public' => true,
            ],
            [
                'key' => 'currency_symbol',
                'value' => '$',
                'type' => 'string',
                'category' => 'tax',
                'description' => 'Currency symbol',
                'is_public' => true,
            ],
            [
                'key' => 'currency_code',
                'value' => 'USD',
                'type' => 'string',
                'category' => 'tax',
                'description' => 'Currency code',
                'is_public' => true,
            ],

            // Quote Settings
            [
                'key' => 'quote_prefix',
                'value' => 'QT',
                'type' => 'string',
                'category' => 'quote',
                'description' => 'Quote number prefix',
                'is_public' => false,
            ],
            [
                'key' => 'quote_expiry_days',
                'value' => '30',
                'type' => 'integer',
                'category' => 'quote',
                'description' => 'Default quote expiry in days',
                'is_public' => false,
            ],
            [
                'key' => 'quote_terms',
                'value' => 'Payment due within 30 days. All quotes are subject to change.',
                'type' => 'string',
                'category' => 'quote',
                'description' => 'Default terms and conditions',
                'is_public' => false,
            ],
            [
                'key' => 'quote_footer',
                'value' => 'Thank you for your business!',
                'type' => 'string',
                'category' => 'quote',
                'description' => 'Quote footer text',
                'is_public' => false,
            ],

            // Email Settings
            [
                'key' => 'email_from_name',
                'value' => 'Stone Cobblers',
                'type' => 'string',
                'category' => 'email',
                'description' => 'Email from name',
                'is_public' => false,
            ],
            [
                'key' => 'email_from_address',
                'value' => 'noreply@stonecobblers.com',
                'type' => 'string',
                'category' => 'email',
                'description' => 'Email from address',
                'is_public' => false,
            ],
            [
                'key' => 'enable_email_notifications',
                'value' => '1',
                'type' => 'boolean',
                'category' => 'email',
                'description' => 'Enable email notifications',
                'is_public' => false,
            ],

            // PDF Settings
            [
                'key' => 'pdf_page_size',
                'value' => 'letter',
                'type' => 'string',
                'category' => 'pdf',
                'description' => 'PDF page size (letter, a4, etc.)',
                'is_public' => false,
            ],
            [
                'key' => 'pdf_orientation',
                'value' => 'portrait',
                'type' => 'string',
                'category' => 'pdf',
                'description' => 'PDF orientation (portrait or landscape)',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
