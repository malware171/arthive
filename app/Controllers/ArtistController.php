<?php

namespace App\Controllers;

use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\Authentication\Auth;

class ArtistController extends Controller
{
    public function index(Request $request): void
    {
        $title = 'Portifólio';
        $subtitle = 'Visualize todos os seus projetos';
        $this->render('adminPage/index', compact('title', 'subtitle'));
    }
    public function new(Request $request): void
    {
        $title = 'Nova Arte';
        $subtitle = 'Descrição';
        $this->render('adminPage/new', compact('title', 'subtitle'));
    }
}
