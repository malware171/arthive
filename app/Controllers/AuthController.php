<?php

namespace App\Controllers;

use App\Models\User;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\Authentication\Auth;
use Lib\FlashMessage;

class AuthController extends Controller
{
    protected string $layout = 'login';

    public function new(): void
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->isClient()) {
                $this->redirectTo(route('client.index'));
            } else {
                $this->redirectTo(route('artist.index'));
            }
        }

        $title = 'Login';
        $this->render('authentications/formLogin', compact('title'));
    }

    public function authenticate(Request $request): void
    {
        $params = $request->getParam('user');

        if (
            !is_array($params) ||
            !isset($params['email'], $params['password']) ||
            empty(trim($params['email'])) ||
            empty(trim($params['password']))
        ) {
            FlashMessage::danger('Email and password are required');
            $this->redirectTo(route('users.login'));
            return;
        }

        $user = User::findBy(['email' => $params['email']]);

        if ($user && $user->authenticate($params['password'])) {
            Auth::login($user);
            FlashMessage::success('Login successful');

            //verificadno tipo de usuário
            if ($user->isClient()) {
                $this->redirectTo(route('client.index'));
            } else {
                $this->redirectTo(route('artist.index'));
            }
        } else {
            FlashMessage::danger('Invalid username or password');
            $this->redirectTo(route('users.login'));
        }
    }

    public function checkLogin(Request $request): void
    {
        $user = Auth::user();

        if ($user) {
            if ($user->isArtist()) {
                $this->redirectTo(route('artist.index'));
            } else {
                $this->redirectTo(route('client.index'));
            }
        } else {
            $this->redirectTo(route('users.login'));
        }
    }

    public function destroy(): void
    {
        Auth::logout();
        FlashMessage::success('Logout successful');
        $this->redirectTo(route('users.login'));
    }
}
