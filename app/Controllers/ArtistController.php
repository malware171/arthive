<?php

namespace App\Controllers;

use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\Authentication\Auth;
use Lib\FlashMessage;

class ArtistController extends Controller
{
    public function index(Request $request): void
    {
        $title = 'Portifólio';
        $subtitle = 'Visualize todos os seus projetos';
        $this->render('adminPage/index', compact('title', 'subtitle'));
    }
}
