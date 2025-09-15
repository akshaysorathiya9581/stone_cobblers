<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    use HasFactory;

    protected $fillable = [
        'user_id',
        'quote_number',
        'customer_name',
        'project_name',
        'final_total',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'final_total' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
