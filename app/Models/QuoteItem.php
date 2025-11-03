<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'name',
        'type',
        'scope_material',
        'unit_price',
        'qty',
        'line_total',
        'tax_cost',
        'is_taxable',
    ];

    protected $casts = [
        'unit_price' => 'decimal:4',
        'qty'        => 'decimal:2',
        'line_total' => 'decimal:4',
        'tax_cost'   => 'decimal:4',
        'is_taxable' => 'boolean',
    ];

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }
}
