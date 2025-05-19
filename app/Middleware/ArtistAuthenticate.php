<?php

namespace App\Middleware;

use Core\Http\Middleware\Middleware;
use Core\Http\Request;
use Lib\Authentication\Auth;
use Lib\FlashMessage;

class ArtistAuthenticate implements Middleware
{
    public function handle(Request $request): void
    {
        if (!Auth::check()) {
            FlashMessage::danger('You must be logged in to access this page.');
            $this->redirectTo(route('users.login'));
        }

        $user = Auth::user();
        if ($user->isClient()) {
            FlashMessage::danger('You do not have permission to access the page as an artist');
            $this->redirectTo(route('client.index'));
        }
    }

    private function redirectTo(string $location): void
    {
        header('Location: ' . $location);
        exit;
    }
}
