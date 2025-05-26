<?php

namespace App\Models;

use Core\Database\ActiveRecord\Model;

/**
 * @property int $id
 * @property string $phone
 * @property int $user_id
*/

class Artwork extends Model {
   protected static $table = 'artworks';
   protected static array $columns = [

   ];
}  