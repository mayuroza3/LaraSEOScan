<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoScan extends Model
{
    use HasFactory;

    protected $fillable = ['url', 'status'];

    public function pages()
    {
        return $this->hasMany(SeoPage::class);
    }
}
