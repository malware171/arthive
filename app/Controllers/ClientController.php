<?php

namespace App\Controllers;

use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\Authentication\Auth;

class ClientController extends Controller
{
    public function index(Request $request): void
    {
        $title = 'Página para clientes';
        $this->render('home/index', compact('title'));
    }
}
