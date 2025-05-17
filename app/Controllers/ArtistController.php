<?php

namespace App\Controllers;

use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\Authentication\Auth;

class ArtistController extends Controller
{
    public function index(Request $request)
    {
        $tltle = 'Página inicial de Artistas';
        $this->render('artist.index', compact('title'), 'application');
    }
}
