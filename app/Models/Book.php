<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['book_category_id', 'title', 'author', 'isbn', 'publisher', 'total_copies', 'available_copies', 'shelf_location'])]
class Book extends Model
{
    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'book_category_id');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(BookIssue::class);
    }
}
