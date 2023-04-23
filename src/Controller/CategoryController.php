<?php

    namespace App\Controller;

    use App\Authenticator\CheckUserLogged;
    use App\View\View;
    use App\Security\Validator\Sanitizer;
    use App\Security\Validator\Validator;
    use App\Security\PasswordHash;
    use App\Entity\Category;
    use App\DB\Connection;
    use App\Session\Flash;

    class CategoryController 
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
            $view = new View('admin/category/index.phtml');
            $view->categories = (new Category(Connection::getInstance()))->findAll();
            return $view->render();
        }

        public function new()
        {
            try {
                if($_SERVER['REQUEST_METHOD'] == 'POST'){
                    $data = $_POST;
                    $data = Sanitizer::sanitizeData($data, Category::$filters);
                    if(!Validator::validateRequiredFields($data)){
                        Flash::add('warning','Todos os campos são obrigatórios!');
                        return header('Location: '.HOME.'/category/new');
                    }
                    $category = new Category(Connection::getInstance());
                    if(!$category->insert($data)){
                        Flash::add('error','Erro ao atualizar categoria!');
                        return header('Location: '.HOME.'/category/new');
                    }
                    Flash::add('success', 'Categoria atualizada com sucesso');
                    return header('Location: '.HOME.'/category');
                }
                $view = new View('admin/category/new.phtml');
                $view->category = (new Category(Connection::getInstance()))->findAll();
                return $view->render();
            } catch (\Exception $e) {
                if(APP_DEBUG){
                    Flash::add('error',$e->getMessage);
                    return header('Location: '.HOME.'/category');
                }
                Flash::add('error','Erro interno');
                return header('Location: '.HOME.'/category');
            }
        }

        public function edit($id=null)
        {
            try {
                if($_SERVER['REQUEST_METHOD'] == 'POST'){
                    $data = $_POST;
                    $data = Sanitizer::sanitizeData($data, Category::$filters);
                    $data['id'] = (int) $id;
                    if(!Validator::validateRequiredFields($data)){
                        Flash::add('warning','Todos os campos são obrigatórios!');
                        return header('Location: '.HOME.'/category/edit/'.$id);
                    }
                    $category = new Category(Connection::getInstance());
                    if(!$category->update($data)){
                        Flash::add('error','Erro ao atualizar categoria!');
                        return header('Location: '.HOME.'/category/edit/'.$id);
                    }
                    Flash::add('success', 'Categoria atualizada com sucesso');
                    return header('Location: '.HOME.'/category');
                }
                $view = new View('admin/category/edit.phtml');
                $view->category = (new Category(Connection::getInstance()))->find($id);
                return $view->render();
            } catch (\Exception $e) {
                if(APP_DEBUG){
                    Flash::add('error',$e->getMessage);
                    return header('Location: '.HOME.'/category');
                }
                Flash::add('error','Erro interno');
                return header('Location: '.HOME.'/category');
            }
        }

        public function remove($id)
        {
            try {
                $category = new Category(Connection::getInstance());
                if(!$category->delete($id)){
                    Flash::add('error','Erro ao remover usuário!');
                    return header('Location: '.HOME.'/blog');
                }
                Flash::add('success', 'Usuário deletado com sucesso');
                return header('Location: '.HOME.'/category');
            } catch (\Exception $e) {
                if(APP_DEBUG){
                    Flash::add('error',$e->getMessage);
                    return header('Location: '.HOME.'/category');
                }
                Flash::add('error','Erro interno');
                return header('Location: '.HOME.'/category');
            }
        }
    }
