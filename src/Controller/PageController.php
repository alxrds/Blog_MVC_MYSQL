<?php

    namespace App\Controller;

    use App\View\View;
    use App\Entity\Post;
    use App\DB\Connection;

    class PageController 
    {
        public function index()
        {   
           $connection = Connection::getInstance();
            $view = new View('site/index.phtml');
            $view->posts = (new Post($connection))->findAll();
            return $view->render();
        }
    }