<?php

namespace App\Models;

use Core\Database\ActiveRecord\Model;

/**
 * @property int $id
 * @property string $name 
*/
class Category extends Model
{
   protected static $table = 'categories';
   protected static array $columns = [
      'name'
   ];
}