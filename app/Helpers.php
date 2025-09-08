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