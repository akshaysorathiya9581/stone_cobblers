<?php
/** Budget ranges */
if (!function_exists('get_budget_ranges')) {
    function get_budget_ranges($id = '')
    {
        $data = [
            ['id' => 'Under $5,000', 'text' => 'Under $5,000'],
            ['id' => '$5,000 - $10,000', 'text' => '$5,000 - $10,000'],
            ['id' => '$10,000 - $20,000', 'text' => '$10,000 - $20,000'],
            ['id' => '$20,000 - $50,000', 'text' => '$20,000 - $50,000'],
            ['id' => 'Over $50,000', 'text' => 'Over $50,000'],
        ];

        if ($id !== '') {
            $temp = array_combine(array_column($data, 'id'), array_column($data, 'text'));
            return $temp[$id] ?? '';
        }

        return $data;
    }
}

/** Timeline options */
if (!function_exists('get_timeline_options')) {
    function get_timeline_options($id = '')
    {
        $data = [
            ['id' => '1-2 weeks', 'text' => '1-2 weeks'],
            ['id' => '3-4 weeks', 'text' => '3-4 weeks'],
            ['id' => '1-2 months', 'text' => '1-2 months'],
            ['id' => '2-3 months', 'text' => '2-3 months'],
            ['id' => '3+ months', 'text' => '3+ months'],
        ];

        if ($id !== '') {
            $temp = array_combine(array_column($data, 'id'), array_column($data, 'text'));
            return $temp[$id] ?? '';
        }

        return $data;
    }
}

/** Project status options */
if (!function_exists('get_project_status_list')) {
    function get_project_status_list($id = '')
    {
        $data = [
            ['id' => 'Planning', 'text' => 'Planning'],
            ['id' => 'In Progress', 'text' => 'In Progress'],
            ['id' => 'On Hold', 'text' => 'On Hold'],
            ['id' => 'Completed', 'text' => 'Completed'],
            ['id' => 'Cancelled', 'text' => 'Cancelled'],
        ];

        if ($id !== '') {
            $temp = array_combine(array_column($data, 'id'), array_column($data, 'text'));
            return $temp[$id] ?? '';
        }

        return $data;
    }
}

if (!function_exists('get_customer_status_list')) {
    function get_customer_status_list($id = '')
    {
        $data = [
            ['id' => 'Active', 'text' => 'Active'],
            ['id' => 'VIP', 'text' => 'VIP'],
            ['id' => 'Prospects', 'text' => 'Prospects'],
            ['id' => 'Inactive', 'text' => 'Inactive'],
        ];

        if ($id !== '') {
            $temp = array_combine(array_column($data, 'id'), array_column($data, 'text'));
            return $temp[$id] ?? '';
        }

        return $data;
    }
}

/** Progress options */
if (!function_exists('get_progress_list')) {
    function get_progress_list($id = '')
    {
        $data = [
            ['id' => '0%', 'text' => '0%'],
            ['id' => '25%', 'text' => '25%'],
            ['id' => '50%', 'text' => '50%'],
            ['id' => '75%', 'text' => '75%'],
            ['id' => '100%', 'text' => '100%'],
        ];

        if ($id !== '') {
            $temp = array_combine(array_column($data, 'id'), array_column($data, 'text'));
            return $temp[$id] ?? '';
        }

        return $data;
    }
}

if (!function_exists('get_kitchen_type_list')) {
    /**
     * Get kitchen type list or a single label by ID.
     *
     * @param string $id
     * @return array|string
     */
    function get_kitchen_type_list($id = '')
    {
        $data = [
            ['id' => 'KITCHEN_TOP', 'text' => 'Kitchen Top'],
            ['id' => 'KITCHEN_MANUFACTURER', 'text' => 'Cabinet Manufacturer'],
            ['id' => 'KITCHEN_MARGIN_MARKUP', 'text' => 'Margin / Markup'],
            ['id' => 'KITCHEN_DELIVERY', 'text' => 'Delivery Charges'],
            ['id' => 'KITCHEN_BUFFER', 'text' => 'BUFFER & Totals'],
        ];

        if ($id !== '') {
            $temp = array_combine(array_column($data, 'id'), array_column($data, 'text'));
            return $temp[$id] ?? '';
        }

        return $data;
    }
}

if (! function_exists('format_money_short')) {
    /**
     * Format a number as compact currency string (e.g. $2.4M, ₹1.2K).
     *
     * @param  float|int|null  $value
     * @param  string $currency  Currency symbol (default: '₹')
     * @param  int    $precision Decimal precision for abbreviated number (default: 1)
     * @return string
     */
    function format_money_short($value, $currency = '₹', $precision = 1)
    {
        if ($value === null) {
            return $currency . '0';
        }

        $neg = $value < 0;
        $value = abs((float) $value);

        if ($value < 1000) {
            // show whole number if integer, otherwise show with precision
            $dec = ($value == floor($value)) ? 0 : $precision;
            $formatted = number_format($value, $dec, '.', ',');
            return ($neg ? '-' : '') . $currency . $formatted;
        }

        $units = ['', 'K', 'M', 'B', 'T'];
        foreach ($units as $i => $suffix) {
            $unitValue = pow(1000, $i);
            $nextUnitValue = $unitValue * 1000;
            if ($value < $nextUnitValue) {
                $short = $value / $unitValue;
                $short = number_format($short, $precision, '.', ',');
                // remove trailing zeros like 2.0 -> 2
                $short = rtrim(rtrim($short, '0'), '.');
                return ($neg ? '-' : '') . $currency . $short . $suffix;
            }
        }

        // Fallback (very large numbers)
        $short = number_format($value, $precision, '.', ',');
        $short = rtrim(rtrim($short, '0'), '.');
        return ($neg ? '-' : '') . $currency . $short;
    }
}

function slugify_id($s)
{
    $s = preg_replace('/[^\p{L}\p{N}\-]+/u', '-', trim($s));
    $s = preg_replace('/-+/', '-', $s);
    return strtolower(trim($s, '-'));
}

function fmt2($v)
{
    return number_format((float) $v, 2, '.', '');
}

function slug_id($s)
{
    $s = preg_replace('/[^\p{L}\p{N}\-]+/u', '-', $s);
    $s = preg_replace('/-+/', '-', $s);

    return strtolower(trim($s, '-'));
}

function fmtAuto($v)
{
    $v = (float) $v;
    if (round($v, 2) != $v) {
        return number_format($v, 4, '.', '');
    }
    return number_format($v, 2, '.', '');
}

if (!function_exists('format_currency')) {
    /**
     * Format a number as currency using settings
     *
     * @param float|int $amount
     * @param bool $showSymbol
     * @return string
     */
    function format_currency($amount, $showSymbol = true)
    {
        $symbol = setting('currency_symbol', '$');
        $decimals = 2;
        
        $formatted = number_format((float)$amount, $decimals, '.', ',');
        
        return $showSymbol ? $symbol . $formatted : $formatted;
    }
}

if (!function_exists('get_tax_rate')) {
    /**
     * Get the tax rate from settings
     *
     * @return float
     */
    function get_tax_rate()
    {
        return (float) setting('tax_rate', 0.08);
    }
}

if (!function_exists('calculate_tax')) {
    /**
     * Calculate tax amount for a given amount
     *
     * @param float|int $amount
     * @return float
     */
    function calculate_tax($amount)
    {
        $taxRate = get_tax_rate();
        return (float)$amount * $taxRate;
    }
}

if (!function_exists('company_info')) {
    /**
     * Get company information from settings
     *
     * @param string|null $key
     * @return string|array
     */
    function company_info($key = null)
    {
        $companyData = [
            'name' => setting('company_name', 'Stone Cobblers Inc.'),
            'email' => setting('company_email', 'info@stonecobblers.com'),
            'phone' => setting('company_phone', '(555) 123-4567'),
            'address' => setting('company_address', '123 Stone Street'),
            'city' => setting('company_city', 'New York'),
            'state' => setting('company_state', 'NY'),
            'zipcode' => setting('company_zipcode', '10001'),
            'website' => setting('company_website', 'https://stonecobblers.com'),
        ];
        
        if ($key === null) {
            return $companyData;
        }
        
        return $companyData[$key] ?? '';
    }
}