<?php

use Piano\Mvc\DataAccessAbstract;

class FakeDataAccess extends DataAccessAbstract
{
    protected $table = 'user';
    protected $pdo;

    public function __construct(\PDO $connection)
    {
        $this->pdo = $connection;
    }
}