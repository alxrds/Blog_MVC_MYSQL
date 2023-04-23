<?php

    namespace App\Controller;

    use App\Authenticator\CheckUserLogged;
    use App\View\View;
    use App\Security\Validator\Sanitizer;
    use App\Security\Validator\Validator;
    use App\Security\PasswordHash;
    use App\Entity\User;
    use App\DB\Connection;
    use App\Session\Flash;

    class UserController 
    {
        use CheckUserLogged;

        public function __construct()
        {
            if(!$this->check()){
                return header('Location: '.HOME.'/auth/login');
            }
        }

        public function index()
        { 
            $view = new View('admin/user/index.phtml');
            $view->users = (new User(Connection::getInstance()))->findAll();
            return $view->render();
        }

        public function new()
        {
            try {
                if($_SERVER['REQUEST_METHOD'] == 'POST'){
                    $data = $_POST;
                    $data = Sanitizer::sanitizeData($data, User::$filters);
                    if(!Validator::validateRequiredFields($data)){
                        Flash::add('warning','Todos os campos são obrigatórios!');
                        return header('Location: '.HOME.'/user/new');
                    }
                    if(!Validator::validadePasswordMinStringLenght($data['password'])){
                        Flash::add('warning','A senha deve ter ao menos 6 caracteres');
                        return header('Location: '.HOME.'/user/new');
                    }
                    if(!Validator::validatePasswordConfirm($data['password'], $data['password_confirm'])){
                        Flash::add('warning','Os campos de senha não são iguais');
                        return header('Location: '.HOME.'/user/new');
                    }
                    $user = new User(Connection::getInstance());
                    $data['password'] = (new PasswordHash())->hash($data['password']);
                    unset($data['password_confirm']);
                    if(!$user->insert($data)){
                        Flash::add('error','Erro ao atualizar usuário!');
                        return header('Location: '.HOME.'/user/new');
                    }
                    Flash::add('success', 'Usuário atualizado com sucesso');
                    return header('Location: '.HOME.'/user');
                }
                $view = new View('admin/user/new.phtml');
                $view->users = (new User(Connection::getInstance()))->findAll();
                return $view->render();
            } catch (\Exception $e) {
                if(APP_DEBUG){
                    Flash::add('error',$e->getMessage);
                    return header('Location: '.HOME.'/user');
                }
                Flash::add('error','Erro interno');
                return header('Location: '.HOME.'/user');
            }
        }

        public function edit($id=null)
        {
            try {
                if($_SERVER['REQUEST_METHOD'] == 'POST'){
                    $data = $_POST;
                    $data = Sanitizer::sanitizeData($data, User::$filters);
                    $data['id'] = (int) $id;
                    if(!Validator::validateRequiredFields($data)){
                        Flash::add('warning','Todos os campos são obrigatórios!');
                        return header('Location: '.HOME.'/user/edit/'.$id);
                    }
                    $user = new User(Connection::getInstance());
                    if($data['password']){
                        if(!Validator::validadePasswordMinStringLenght($data['password'])){
                            Flash::add('warning','A senha deve ter ao menos 6 caracteres');
                            return header('Location: '.HOME.'/user/edit');
                        }
                        if(!Validator::validatePasswordConfirm($data['password'], $data['password_confirm'])){
                            Flash::add('warning','Os campos de senha não são iguais');
                            return header('Location: '.HOME.'/user/edit');
                        }
                        $data['password'] = PasswordHash::hash($data['password']);
                    }else{
                        unset($data['password']);
                    }
                    unset($data['password_confirm']);
                    if(!$user->update($data)){
                        Flash::add('error','Erro ao atualizar usuário!');
                        return header('Location: '.HOME.'/user/edit/'.$id);
                    }
                    Flash::add('success', 'Usuário atualizado com sucesso');
                    return header('Location: '.HOME.'/user');
                }
                $view = new View('admin/user/edit.phtml');
                $view->user = (new User(Connection::getInstance()))->find($id);
                return $view->render();
            } catch (\Exception $e) {
                if(APP_DEBUG){
                    Flash::add('error',$e->getMessage);
                    return header('Location: '.HOME.'/user');
                }
                Flash::add('error','Erro interno');
                return header('Location: '.HOME.'/user');
            }
        }

        public function remove($id)
        {
            try {
                $user = new User(Connection::getInstance());
                if(!$user->delete($id)){
                    Flash::add('error','Erro ao remover usuário!');
                    return header('Location: '.HOME.'/blog');
                }
                Flash::add('success', 'Usuário deletado com sucesso');
                return header('Location: '.HOME.'/user');
            } catch (\Exception $e) {
                if(APP_DEBUG){
                    Flash::add('error',$e->getMessage);
                    return header('Location: '.HOME.'/user');
                }
                Flash::add('error','Erro interno');
                return header('Location: '.HOME.'/user');
            }
        }
    }
