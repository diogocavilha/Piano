<?php

namespace app\DataAccess;

use PDO;

class UserDataAccess extends \Piano\Mvc\DataAccessAbstract
{
    protected $table = 'user';
    protected $model = 'app\dataAccess\models\User';

    public function __construct()
    {
        $this->pdo = new PDO("mysql:host=localhost;dbname=tests;", 'root', 'admin');
    }
}
