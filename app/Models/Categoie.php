<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoie extends Model
{
    use HasFactory;

    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    protected $guarded = [];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'cate_id');
    }
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
