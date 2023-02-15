<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Category;

class Book extends Model
{
    use HasFactory;
    use Sluggable;
    protected $fillable = [
        'book_code',
        'title',
        'cover',
        'status',
        'slug'
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function categories(): BelongsToMany{
        return $this->belongsToMany(Category::class, 'book_category', 'book_id', 'category_id');
    }
}
