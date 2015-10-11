<?php

use Piano\Config\Ini;

class IniTest extends PHPUnit_Framework_TestCase
{
    public $ini;

    public function setUp()
    {
        $this->ini = new Ini('tests/configTest.ini');
    }

    /**
     * @test
     * @expectedException RuntimeException
     * @expectedExceptionMessageRegExp /Path cannot be null.|Config file not found./
     * @dataProvider pathDataProvider
     */
    public function instanceShouldThrowRuntimeExceptionWhenConfigPathIsNotValid($path)
    {
        $config = new Ini($path);
    }

    public function pathDataProvider()
    {
        return [
            [''],
            [null],
            ['wrong/path/to/config.ini'],
        ];
    }

    /**
     * @test
     */
    public function itShouldReturnAnArray()
    {
        $config = $this->ini->get();

        $this->assertInternalType('array', $config, 'It should be an array');
        $this->assertArrayHasKey('development', $config);
        $this->assertArrayHasKey('production', $config);
        $this->assertArrayHasKey('defaultLocale', $config);
    }

    /**
     * @test
     */
    public function itShouldReturnAnArrayByItsKey()
    {
        $config = $this->ini->get('development');
        $this->assertArrayNotHasKey('production', $config);
        $this->assertArrayNotHasKey('defaultLocale', $config);
    }

    /**
     * @test
     */
    public function itShouldReturnNullWhenAnArrayKeyDoesNotExist()
    {
        $config = $this->ini->get('wrong_key');
        $this->assertNull($config);
    }
}
