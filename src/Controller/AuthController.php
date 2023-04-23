<?php

    namespace App\Controller;

    use App\View\View;
    use App\Authenticator\Authenticator;
    use App\Entity\User;
    use App\DB\Connection;
    use App\Session\Flash;

    class AuthController 
    {
        public function login()
        { 
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                
                $user = new User(Connection::getInstance());
                $authenticator = new Authenticator($user);

                if(!$authenticator->login($_POST)){
                    Flash::add("warning","Usuário ou senha inválidos");
                    return header('Location: ' .HOME. '/auth/login');
                }
                Flash::add("success","Usuário Logado com Sucesso.");
                return header('Location: ' .HOME. '/blog');
            }
            $view = new View('auth/index.phtml');
            return $view->render();
        }

        public function logout()
        {
            $auth = (new Authenticator())->logout();
            Flash::add("success","Usário deslogado com suecesso!");
            return header('Location: ' .HOME);
        }
    }
