<?php

namespace App\Controllers;

use Core\Http\Controllers\Controller;
use Core\http\Request;
use Lib\Authentication\Auth;

class ClientController extends Controller
{
    public function index(Request $request): void
    {
        $title = 'Página para clientes';
        $this->render('client.index', compact('title'), 'application');
    }
}
