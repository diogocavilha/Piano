<?php

include 'tests/fakeSystem/FakeDataAccess.php';

class DataAccessAbstractTest extends PHPUnit_Extensions_Database_TestCase
{
    private $stack;
    private $conn;

    public function setUp()
    {
        $this->stack = new FakeDataAccess($this->getConnection()->getConnection());
    }

    public function getConnection()
    {
        if (!$this->conn) {
            $config = parse_ini_file('tests/bootstrap/db.ini');
            $pdo = new \Piano\Config\Pdo($config);
            $this->conn = $this->createDefaultDBConnection($pdo->get(), $config['dbName']);
        }

        return $this->conn;
    }

    public function getDataSet()
    {
        $user = include 'tests/bootstrap/dbDataTest/user.php';

        return $this->createArrayDataSet($user);
    }

    /**
     * @test
     * @expectedException RuntimeException
     * @expectedExceptionMessage Data bind must be a recursive array.
     */
    public function itShouldThrowRuntimeExceptionIfDataBindParamIsNotValid()
    {
        $this->assertTrue(method_exists($this->stack, 'insert'));

        $this->stack->insert([], []);
    }

    /**
     * @test
     */
    public function itShouldInsertARecordAndReturnItsId()
    {
        $this->assertTrue(method_exists($this->stack, 'insert'));

        $fields = [
            'name' => ':name',
            'email' => ':email',
        ];

        $dataBind = [
            [':name', 'Test', PDO::PARAM_STR],
            [':email', 'test@domain.com', PDO::PARAM_STR],
        ];

        $id = $this->stack->insert($fields, $dataBind);

        $this->assertInternalType('integer', $id, 'Insert should return the record id');
    }
}
