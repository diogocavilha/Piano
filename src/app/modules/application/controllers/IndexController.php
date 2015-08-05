<?php

namespace app\modules\application\controllers;

use Piano\Mvc\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        $this->view->render('index');
    }
}