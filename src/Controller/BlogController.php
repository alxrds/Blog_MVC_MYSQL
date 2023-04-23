<?php

    namespace App\Controller;

    use App\Authenticator\CheckUserLogged;
    use App\View\View;
    use App\Entity\Post;
    use App\Security\Validator\Sanitizer;
    use App\Security\Validator\Validator;
    use App\Entity\Category;
    use App\DB\Connection;
    use App\Session\Flash;

    class BlogController 
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
            $view = new View('admin/blog/index.phtml');
            $view->posts = (new Post(Connection::getInstance()))->findAll();
            return $view->render();
        }

        public function new()
        {
            try {
                if($_SERVER['REQUEST_METHOD'] == 'POST'){
                    $data = $_POST;
                    $data = Sanitizer::sanitizeData($data, Post::$filters);
                    if(!Validator::validateRequiredFields($data)){
                        Flash::add('warning','Todos os campos são obrigatórios!');
                        return header('Location: '.HOME.'/blog/new');
                    }

                    $post = new Post(Connection::getInstance());

                    $data['slug'] = Post::slugify($data['title']);

                    if(!$post->insert($data)){
                        Flash::add('error','Erro ao atualizar postagem!');
                        return header('Location: '.HOME.'/blog/new');
                    }
                    Flash::add('success', 'Postagem atualizada com sucesso');
                    return header('Location: '.HOME.'/blog');
                }
                $view = new View('admin/blog/new.phtml');
                $view->categories = (new Category(Connection::getInstance()))->findAll();
                return $view->render();
            } catch (\Exception $e) {
                if(APP_DEBUG){
                    Flash::add('error',$e->getMessage);
                    return header('Location: '.HOME.'/blog');
                }
                Flash::add('error','Erro interno');
                return header('Location: '.HOME.'/blog');
            }
        }

        public function edit($id=null)
        {
            try {
                if($_SERVER['REQUEST_METHOD'] == 'POST'){
                    $data = $_POST;
                    $data = Sanitizer::sanitizeData($data, Post::$filters);
                    $data['id'] = (int) $id;
                    if(!Validator::validateRequiredFields($data)){
                        Flash::add('warning','Todos os campos são obrigatórios!');
                        return header('Location: '.HOME.'/blog/edit/'.$id);
                    }
                    $post = new Post(Connection::getInstance());
                    if(!$post->update($data)){
                        Flash::add('error','Erro ao atualizar postagem!');
                        return header('Location: '.HOME.'/blog/edit/'.$id);
                    }
                    Flash::add('success', 'Postagem atualizada com sucesso');
                    return header('Location: '.HOME.'/blog');
                }
                $view = new View('admin/blog/edit.phtml');
                $view->post = (new Post(Connection::getInstance()))->find($id);
                $view->categories = (new Category(Connection::getInstance()))->findAll();

                return $view->render();
            } catch (\Exception $e) {
                if(APP_DEBUG){
                    Flash::add('error',$e->getMessage);
                    return header('Location: '.HOME.'/blog');
                }
                Flash::add('error','Erro interno');
                return header('Location: '.HOME.'/blog');
            }
        }

        public function remove($id)
        {
            try {
                $post = new Post(Connection::getInstance());
                if(!$post->delete($id)){
                    Flash::add('error','Erro ao remover postagem!');
                    return header('Location: '.HOME.'/blog');
                }
                Flash::add('success', 'Postagem deletada com sucesso');
                return header('Location: '.HOME.'/blog');
            } catch (\Exception $e) {
                if(APP_DEBUG){
                    Flash::add('error',$e->getMessage);
                    return header('Location: '.HOME.'/blog');
                }
                Flash::add('error','Erro interno');
                return header('Location: '.HOME.'/blog');
            }
        }
    }
