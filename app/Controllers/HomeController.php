<?php

namespace App\Controllers;

use App\Models\Artwork;
use Core\Http\Controllers\Controller;

class HomeController extends Controller
{
   public function index(): void
   {
      $artworks = Artwork::all();
   
      $title = 'Todas as obras';
      $this->render('home/index', compact('title', 'artworks'));
   }
} 