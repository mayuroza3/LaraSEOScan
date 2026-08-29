<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeoScan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['url', 'status', 'user_id', 'has_robots_txt', 'has_sitemap_xml', 'uuid', 'type'];

    protected static function booted()
    {
        static::creating(function ($scan) {
            if (empty($scan->uuid)) {
                $scan->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function pages()
    {
        return $this->hasMany(SeoPage::class, 'seo_scan_id');
    }

    public function scopeTodayByUser($query, $userId)
    {
        return $query->where('user_id', $userId)
            ->whereDate('created_at', now()->toDateString());
    }

    public function getDomainAttribute()
    {
        return parse_url($this->url, PHP_URL_HOST) ?? $this->url;
    }

    public function getScoreAttribute()
    {
        $issues = \App\Models\SeoIssue::whereHas('page', function ($q) {
            $q->where('seo_scan_id', $this->id);
        })->get();

        $critical = $issues->where('severity', 'critical')->count();
        $errors = $issues->where('severity', 'error')->count();
        $warnings = $issues->where('severity', 'warning')->count();

        // 100 is base score. Deduct weights.
        $score = 100 - ($critical * 15) - ($errors * 8) - ($warnings * 1);
        return max(0, min(100, $score));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
