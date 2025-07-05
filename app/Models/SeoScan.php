<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeoScan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['url', 'status','user_id'];

    public function pages()
    {
        return $this->hasMany(SeoPage::class);
    }

    public function scopeTodayByUser($query, $userId)
    {
        return $query->where('user_id', $userId)
            ->whereDate('created_at', now()->toDateString());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
