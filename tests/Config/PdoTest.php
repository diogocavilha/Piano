<?php

use Piano\Config\Pdo;

class PdoTest extends \PHPUnit_Framework_TestCase
{
    private $config;

    public function setUp()
    {
        $this->config = parse_ini_file('tests/bootstrap/db.ini');
    }

    /**
     * @test
     * @expectedException RuntimeException
     * @expectedExceptionMessageRegExp /Invalid data access. Array is expected.|Invalid data access. Key \w+ is expected./
     * @dataProvider configTestDataProvider
     */
    public function itShouldThrowRuntimeExceptionWhenConfigurationIsNotValid($config)
    {
        $pdo = new Pdo($config);
    }

    /**
     * @test
     */
    public function itShouldReturnAPdoInstance()
    {
        $pdo = new Pdo($this->config);

        $this->assertInstanceOf('\Pdo', $pdo->get());
    }

    public function configTestDataProvider()
    {
        $adapter = $this->config['dbAdapter'];
        $host    = $this->config['dbHost'];
        $dbname  = $this->config['dbName'];
        $user    = $this->config['dbUser'];
        $pass    = $this->config['dbPass'];

        return [
            [
                [
                    'dbHost' => $host,
                    'dbName' => $dbname,
                    'dbUser' => $user,
                    'dbPass' => $pass,
                ]
            ],
            [
                [
                    'dbAdapter' => $adapter,
                    'dbName'    => $dbname,
                    'dbUser'    => $user,
                    'dbPass'    => $pass,
                ]
            ],
            [
                [
                    'dbAdapter' => $adapter,
                    'dbHost'    => $host,
                    'dbUser'    => $user,
                    'dbPass'    => $pass,
                ]
            ],
            [
                [
                    'dbAdapter' => $adapter,
                    'dbHost'    => $host,
                    'dbName'    => $dbname,
                    'dbPass'    => $pass,
                ]
            ],
            [
                [
                    'dbAdapter' => $adapter,
                    'dbHost'    => $host,
                    'dbName'    => $dbname,
                    'dbUser'    => $user,
                ]
            ],
            [null],
            [''],
            ['teste'],
        ];
    }
}
