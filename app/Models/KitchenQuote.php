<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KitchenQuote extends Model
{
    use HasFactory;

    // allow only these fields to be mass assigned
    protected $fillable = [
        'project',
        'type',
        'cost',
        'is_taxable',
    ];

    protected $casts = [
        'cost' => 'decimal:4',
        'is_taxable' => 'boolean',
    ];
}
