<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Utils\SearchableTrait;

class Post extends Model
{
    use HasFactory;
    use SearchableTrait;

    public static $searchableColumns = [
        'title',
        'image_url',
        'content',
        'is_active',
        'created_at',
        'updated_at',
    ];

    public static $sortableColumns = [
        'id',
        'title',
        'created_at',
        'updated_at',
    ];
    public static $maxPerPage = 100;

    protected $fillable = [
        'title',
        'image_url',
        'content',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
