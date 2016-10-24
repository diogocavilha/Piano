<?php

use Piano\Config\Ini;

/**
 * @group php7
 */
class IniTest extends \PHPUnit_Framework_TestCase
{
    public $class;

    public function setUp()
    {
        $this->class = new Ini('tests/configTest.ini');
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Config file not found.
     * @dataProvider invalidConfigFilePathProvider
     */
    public function testItMustThrowAnInvalidArgumentExceptionWhenConfigFilePathIsNotValid($invalidPath)
    {
        $class = new Ini($invalidPath);
    }

    public function invalidConfigFilePathProvider()
    {
        return [
            [''],
            ['foo/bar/configTest.ini'],
        ];
    }

    /**
     * @expectedException TypeError
     */
    public function testItMustThrowAnExceptionWhenConfigFilePathIsNotValid()
    {
        new Ini();
    }

    public function testItMustReturnAllSectionsAsArray()
    {
        $this->assertTrue(
            method_exists($this->class, 'toArray'),
            'Method "toArray()" must exist'
        );

        $config = $this->class->toArray();

        $this->assertInternalType('array', $config, 'Config should be an array');
        $this->assertArraySubset(
            [
                'development' => [
                    'databaseAdapter' => 'mysql',
                    'databaseHost' => 'localhost',
                    'databaseDbname' => 'banco_development',
                    'databaseUsername' => 'root',
                    'databasePassword' => 'admin',
                ]
            ],
            $config,
            'Config array must have a subarray "development"'
        );

        $this->assertArraySubset(
            [
                'production' => [
                    'databaseAdapter' => 'mysql',
                    'databaseHost' => 'localhost',
                    'databaseDbname' => 'banco_production',
                    'databaseUsername' => 'root',
                    'databasePassword' => 'admin',
                ],
            ],
            $config,
            'Config array must have a subarray "production"'
        );
    }

    public function testItShouldReturnWholeTheConfigurationAsArray()
    {
        $config = $this->class->toArray();

        $this->assertInternalType('array', $config, 'Config should be an array');

        $this->assertArrayHasKey('defaultLocale', $config);
        $this->assertArrayHasKey('defaultTimezone', $config);
        $this->assertArrayHasKey('defaultModule', $config);
        $this->assertArrayHasKey('defaultDirectory', $config);
        $this->assertArrayHasKey('development', $config, 'Key "development" must exist');
        $this->assertArrayHasKey('production', $config, 'Key "production" must exist');
        $this->assertEquals('pt_BR', $config['defaultLocale']);
        $this->assertEquals('America/Sao_Paulo', $config['defaultTimezone']);
        $this->assertEquals('authentication', $config['defaultModule']);
        $this->assertEquals('Piano', $config['defaultDirectory']);

        $developmentSection = $config['development'];
        $this->assertEquals('mysql', $developmentSection['databaseAdapter'], 'Key "databaseAdapter" must return "mysql"');
        $this->assertEquals('localhost', $developmentSection['databaseHost'], 'Key "databaseHost" must return "localhost"');
        $this->assertEquals('banco_development', $developmentSection['databaseDbname'], 'Key "databaseDbname" must return "banco_development"');
        $this->assertEquals('root', $developmentSection['databaseUsername'], 'Key "databaseUsername" must return "root"');
        $this->assertEquals('admin', $developmentSection['databasePassword'], 'Key "databasePassword" must return "admin"');

        $productionSection = $config['production'];
        $this->assertEquals('mysql', $productionSection['databaseAdapter'], 'Key "databaseAdapter" must return "mysql"');
        $this->assertEquals('localhost', $productionSection['databaseHost'], 'Key "databaseHost" must return "localhost"');
        $this->assertEquals('banco_production', $productionSection['databaseDbname'], 'Key "databaseDbname" must return "banco_production"');
        $this->assertEquals('root', $productionSection['databaseUsername'], 'Key "databaseUsername" must return "root"');
        $this->assertEquals('admin', $productionSection['databasePassword'], 'Key "databasePassword" must return "admin"');
    }

    public function testItShouldReturnASubsetConfigByItsKey()
    {
        $config = $this->class->get('development');
        $this->assertInternalType('array', $config, 'It must return a subset array');
        $this->assertArrayHasKey('databaseAdapter', $config, 'Key "databaseAdapter" must exist');
        $this->assertArrayHasKey('databaseHost', $config, 'Key "databaseHost" must exist');
        $this->assertArrayHasKey('databaseDbname', $config, 'Key "databaseDbname" must exist');
        $this->assertArrayHasKey('databaseUsername', $config, 'Key "databaseUsername" must exist');
        $this->assertArrayHasKey('databasePassword', $config, 'Key "databasePassword" must exist');
        $this->assertEquals('mysql', $config['databaseAdapter'], 'Key "databaseAdapter" must return "mysql"');
        $this->assertEquals('localhost', $config['databaseHost'], 'Key "databaseHost" must return "localhost"');
        $this->assertEquals('banco_development', $config['databaseDbname'], 'Key "databaseDbname" must return "banco_development"');
        $this->assertEquals('root', $config['databaseUsername'], 'Key "databaseUsername" must return "root"');
        $this->assertEquals('admin', $config['databasePassword'], 'Key "databasePassword" must return "admin"');

        $config = $this->class->get('production');
        $this->assertInternalType('array', $config, 'It must return a subset array');
        $this->assertArrayHasKey('databaseAdapter', $config, 'Key "databaseAdapter" must exist');
        $this->assertArrayHasKey('databaseHost', $config, 'Key "databaseHost" must exist');
        $this->assertArrayHasKey('databaseDbname', $config, 'Key "databaseDbname" must exist');
        $this->assertArrayHasKey('databaseUsername', $config, 'Key "databaseUsername" must exist');
        $this->assertArrayHasKey('databasePassword', $config, 'Key "databasePassword" must exist');
        $this->assertEquals('mysql', $config['databaseAdapter'], 'Key "databaseAdapter" must return "mysql"');
        $this->assertEquals('localhost', $config['databaseHost'], 'Key "databaseHost" must return "localhost"');
        $this->assertEquals('banco_production', $config['databaseDbname'], 'Key "databaseDbname" must return "banco_production"');
        $this->assertEquals('root', $config['databaseUsername'], 'Key "databaseUsername" must return "root"');
        $this->assertEquals('admin', $config['databasePassword'], 'Key "databasePassword" must return "admin"');
    }

    public function testItShouldReturnNullWhenTheKeyDoesNotExist()
    {
        $config = $this->class->get('invalid_key');
        $this->assertNull($config, 'It must return null');
    }
}
