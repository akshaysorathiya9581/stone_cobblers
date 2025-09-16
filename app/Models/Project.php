<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'subtitle', 'description', 'user_id',
        'customer_notes', 'budget', 'timeline', 'status', 'progress', 'team'
    ];

    public function customer()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function quotes()
    {
        return $this->hasMany(\App\Models\Quote::class);
    }


    public function scopeActive($q)
    {
        return $q->whereNotIn('status', ['Completed', 'Cancelled']);
    }

    public function scopeCompletedThisMonth($q)
    {
        return $q->whereNotIn('status', ['Completed', 'Cancelled'])
                 ->whereMonth('updated_at', now()->month)
                 ->whereYear('updated_at', now()->year);
    }
}
