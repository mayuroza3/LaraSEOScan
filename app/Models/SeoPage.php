<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'seo_scan_id',
        'url',
        'title',
        'description',
        'canonical',
        'robots',
        'headings'
    ];

    protected $casts = [
        'headings' => 'array',
    ];

    public function scan()
    {
        return $this->belongsTo(SeoScan::class, 'seo_scan_id');
    }

    public function links()
    {
        return $this->hasMany(SeoLink::class);
    }

    public function images()
    {
        return $this->hasMany(SeoImage::class);
    }
}
