<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeoPage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'seo_scan_id',
        'url',
        'title',
        'description',
        'canonical',
        'robots',
        'headings',
        'headers',
        'ttfb_ms',
        'html_size_bytes',
        'redirect_chain',
        'redirect_count',
        'status_code',
        'word_count',
        'shingle_signature',
        'structured_data',
        'fetched_at',
        'keyword_density',
        'image_total_size',
        'image_unoptimized_count',
    ];

    protected $casts = [
        'headings' => 'array',
        'headers' => 'array',
        'redirect_chain' => 'array',
        'structured_data' => 'array',
        'keyword_density' => 'array',
        'fetched_at' => 'datetime',
    ];

    public function scan()
    {
        return $this->belongsTo(SeoScan::class, 'seo_scan_id');
    }
    public function seoscan()
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

    public function issues()
    {
        return $this->hasMany(SeoIssue::class, 'seo_page_id');
    }
}
