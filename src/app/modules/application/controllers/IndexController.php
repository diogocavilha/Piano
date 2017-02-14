<?php

namespace ApplicationName\Application\Controllers;

class IndexController extends \Piano\Mvc\Controller
{
    public function indexAction()
    {
        $this->view->render(
            'index',
            [
                'namespace' => __NAMESPACE__,
                'user' => 'Diogo',
            ]
        );
    }
}