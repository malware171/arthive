<?php

namespace App\Controllers;

use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\FlashMessage;

class ArtworkController extends Controller
{
   public function new(Request $request): void
   {
      $title = 'Nova Arte';
      $description = 'Descrição';
      $this->render('adminPage/createPost/new', compact('title', 'description'));
   }

   public function updateArtwork(): void
   {
      $image = $_FILES['image'];
   }

   public function newArtwork(Request $request): void
   {
      $parms = $request->getParam('artwork');

      if(
         !is_array($parms) ||
         !isset($parms['title'], $params['description'], $_FILES['image'])
      ) {
         FlashMessage::danger('All inputs need to filled');
         $this->redirectTo(route('artist.new'));
         return;
      }
      //  CHAMA O METODO DENTRO DO MODEL
      // PARA SALVAR A POSTAGEM
   }

}