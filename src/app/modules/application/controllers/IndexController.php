<?php

namespace app\modules\application\controllers;

use Piano\Mvc\Controller;
use PDO;

class IndexController extends Controller
{
    public function indexAction()
    {
        $model = new \app\dataAccess\UserDataAccess();

        $user = $model->getFirst();

        $this->view->render('index', ['user' => $user]);
    }
}