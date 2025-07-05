<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'seo_page_id',
        'href',
        'status_code',
        'is_internal'
    ];

    public function page()
    {
        return $this->belongsTo(SeoPage::class, 'seo_page_id');
    }
}
