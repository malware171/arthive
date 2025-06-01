<?php

namespace App\Controllers;

use App\Models\Artwork;
use Core\Http\Request;
use Core\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index(Request $request, int $page = 1): void
    {
        $paginator = Artwork::paginate(
            page: $page,
            per_page: 10,
        );
        $artworks = $paginator->registers();

        $title = 'Todas as obras';

        $this->render('home/index', compact('title', 'paginator', 'artworks'));
    }
}