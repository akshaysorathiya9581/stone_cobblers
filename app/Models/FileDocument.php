<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FileDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','project_id','name','path','mime','size','category','created_by'
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

     // convenience: is image?
    public function getIsImageAttribute()
    {
        return $this->mime && str_starts_with($this->mime, 'image/');
    }

    // if you want an image URL route (controller must provide route)
    public function getImageUrlAttribute()
    {
        if ($this->is_image) {
            return route('admin.files.image', ['file' => $this->id]);
        }
        return null;
    }

    // download url helper
    public function getDownloadUrlAttribute()
    {
        return route('admin.files.download', ['file' => $this->id]);
    }
    
    public function getDownloadResponse()
    {
        $disk = Storage::disk('private');
        if (! $disk->exists($this->path)) abort(404);
        return $disk->download($this->path, $this->name, [
            'Content-Type' => $this->mime ?? 'application/octet-stream'
        ]);
    }
}
