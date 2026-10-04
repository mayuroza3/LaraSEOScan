<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeoScan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'url',
        'status',
        'error_message',
        'failed_at',
        'score',
        'user_id',
        'has_robots_txt',
        'has_sitemap_xml',
        'uuid',
        'type'
    ];

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
        if (isset($this->attributes['score']) && $this->attributes['score'] !== null) {
            return (int) $this->attributes['score'];
        }

        return $this->calculateScore();
    }

    public function calculateScore(): int
    {
        $homepage = $this->pages()->where('url', $this->url)->first()
                 ?? $this->pages()->first();

        if (!$homepage) {
            return 100;
        }

        $issues = $homepage->issues;

        $critical = $issues->where('severity', 'critical')->count();
        $errors = $issues->where('severity', 'error')->count();
        $warnings = $issues->where('severity', 'warning')->count();

        // 100 is base score. Deduct weights based on target page audit.
        $score = 100 - ($critical * 15) - ($errors * 8) - ($warnings * 1);
        return (int) max(0, min(100, $score));
    }

    public function calculateAndStoreScore(): int
    {
        $score = $this->calculateScore();
        $this->update(['score' => $score]);
        return $score;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
