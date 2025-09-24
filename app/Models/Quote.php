<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'quote_number',
        'subtotal',
        'tax',
        'discount',
        'total',
        'pdf_path',
        'is_kitchen',
        'is_vanity',
        'status',
        'expires_at',
        'final_total',
        'sent_at',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'subtotal'       => 'decimal:2',
        'tax'            => 'decimal:2',
        'discount'       => 'decimal:2',
        'total'          => 'decimal:2',
        'final_total'    => 'decimal:2',
        'is_kitchen'     => 'boolean',
        'is_vanity'      => 'boolean',
        'expires_at'     => 'date',
        'sent_at'    => 'datetime',
        'approved_at'=> 'datetime',
        'rejected_at'=> 'datetime',
    ];

    // -----------------------
    // Relationships
    // -----------------------

    /**
     * The project this quote belongs to.
     * Expects quotes.project_id FK -> projects.id
     */
    public function project()
    {
        return $this->belongsTo(\App\Models\Project::class, 'project_id');
    }

    /**
     * Creator / owner of the quote (user)
     */
    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * Quote items (one-to-many)
     */
    public function items()
    {
        return $this->hasMany(\App\Models\QuoteItem::class);
    }

    /**
     * Polymorphic files (attachments)
     */
    public function files()
    {
        return $this->morphMany(\App\Models\File::class, 'fileable');
    }

    // -----------------------
    // Helpers / Accessors
    // -----------------------

    public function isKitchen(): bool
    {
        return (bool) $this->is_kitchen;
    }

    public function isVanity(): bool
    {
        return (bool) $this->is_vanity;
    }
    
    /**
     * Convenience accessor for a PDF public link if you later use public disk.
     * For now this returns the stored pdf_path (private local disk) — do not expose directly.
     */
    public function getPdfUrlAttribute()
    {
        if (! $this->pdf_path) return null;
        // if you switch to public storage later, change 'local' -> 'public' and return Storage::url(...)
        return $this->pdf_path;
    }
}
