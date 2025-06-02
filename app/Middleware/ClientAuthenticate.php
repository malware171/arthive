<?php

namespace App\Middleware;

use Core\Http\Middleware\Middleware;
use Core\Http\Request;
use Lib\Authentication\Auth;
use Lib\FlashMessage;

class ClientAuthenticate implements Middleware
{
    public function handle(request $request): void
    {
        $user = Auth::user();

        if ($user === null) {
            FlashMessage::danger('You do not have permission to access this page.');
            $this->redirectTo(route('users.login'));
        }

        if ($user->isArtist()) {
            FlashMessage::danger('You do not have permission to access the page as an artist');
            $this->redirectTo(route('artist.admin.page'));
        }
    }

    private function redirectTo(string $location): void
    {
        header('Location: ' . $location);
        exit;
    }
}
