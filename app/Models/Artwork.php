<?php

namespace App\Models;

use Core\Database\ActiveRecord\BelongsTo;
use Core\Database\ActiveRecord\Model;

/**
 * @property int $id
 * @property string $title 
 * @property float $price
 * @property date $creation_date
 * @property string $description
 * @property string $image_url
 * @property int $is_ai_verified
 * @property int $artist_id
 * @property int $category_id
*/

class Artwork extends Model
{
   protected static $table = 'artworks';
   protected static array $columns = [
     'id',
     'title',
     'price',
     'creation_date',
     'description',
     'image_url',
     'is_ai_veried',
     'artist_id',
     'category_id',
   ];

   public function refArtist(): BelongsTo
   {
      return $this->belongsTo(Artist::class, 'artist_id');
   }

   public function redCategory(): BelongsTo
   {
      return $this->belongsTo(Category::class, 'category_id');
   }
}  