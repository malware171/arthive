<?php

namespace Database\Populate;

use App\Models\Category;

class CategoryPopulate
{
   public static function populate(): void
   {
      $categories = [
         'Desing de Personagem',
         'Arte de jogos',
         'Anatomia',
         'Fanart',
         'Automotivo'
      ];
      sort($categories);

      foreach($categories as $categoryName) {
         if(!Category::exists(['name' => $categoryName])) {
            $category = new Category([
               'name' => $categoryName
            ]);
            $category->save();
         }
      }
      
   }
}