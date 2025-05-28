<?php

namespace App\Controllers;

use App\Models\Category;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\FlashMessage;

class ArtworkController extends Controller
{
   public function new(Request $request): void
   {
      $params = $request->getParams();
      $categories = Category::all();

      $title = 'Nova Arte';
      $description = 'Descrição';
      $this->render('adminPage/createPost/new', compact('title', 'description', 'categories'));
   }

   public function newArtwork(Request $request): void
   {
      $params = $request->getParam('artwork');

      if(
         !is_array($params) ||
         !isset($params['title'], $params['description'], $_FILES['image'])
      ) {
         FlashMessage::danger('Todos os campos devem ser preechidos');
         $this->redirectTo(route('artist.new'));
         return;
      }
   }

}