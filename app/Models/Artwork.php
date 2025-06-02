<?php

namespace App\Models;

use Core\Database\ActiveRecord\BelongsTo;
use Core\Database\ActiveRecord\Model;
use Lib\Validations;

/**
 * @property int $id
 * @property string $title
 * @property string $creation_date
 * @property string $description
 * @property string $image_url
 * @property int $is_ai_verified
 * @property int $artist_id
 * @property int $category_id
 */

class Artwork extends Model
{
    protected static string $table = 'artworks';
    protected static array $columns = [
        'id',
        'title',
        'creation_date',
        'description',
        'image_url',
        'is_ai_verified',
        'artist_id',
        'category_id',
    ];

    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class, 'artist_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function validates(): void
    {
        Validations::notEmpty('title', $this);
        Validations::notEmpty('description', $this);
        Validations::notEmpty('category_id', $this);
    }
}
