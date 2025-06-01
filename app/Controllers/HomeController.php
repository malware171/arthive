<?php

namespace App\Controllers;

use App\Models\Artwork;
use Core\Http\Request;
use Core\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index(Request $request): void
    {
        $artworks = Artwork::all();
        $paginator = Artwork::paginate(
            page: $request->getParam('page', 1),
            per_page: 10,
        );

        $title = 'Todas as obras';
        $this->render('home/index', compact('title', 'artworks'));
    }
}
