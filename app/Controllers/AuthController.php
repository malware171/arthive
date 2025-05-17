<?php

namespace App\Controllers;

use App\Models\User;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Lib\FlashMessage;

class AuthController extends Controller {

   protected string $layout = 'login';

   public function formLogin(): void {
      $this->render('authentications/formLogin');
   }
   
   public function authenticate(Request $request): void {
      $params = $request->getParam('user');
      $user = User::findByEmail($params['email']);
      
      if($user && $user->authenticate($params['password'])) {

         FlashMessage::success('Login realizado com sucesso.');
         $this->redirectTo('/home');
      } else {
         FlashMessage::danger('Usuario e/ou senha não encontrado.');
         $this->redirectTo('/login');
      }
   }
   
}