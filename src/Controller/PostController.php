<?php

    namespace App\Controller;

    use App\View\View;
    use App\Entity\Post;
    use App\DB\Connection;
    use App\Session\Flash;

    class PostController 
    {
        public function index($slug)
        {   
            try{
                $connection = Connection::getInstance();
                $view = new View('site/single.phtml');
                $view->post = current((new Post($connection))->where(['slug'=>$slug]));
                return $view->render();
            } catch (\Exception $e){
                Flash::add('warning', 'Postagem não encontrada!');
                header('Location:'.HOME);
            }

        }

    }
