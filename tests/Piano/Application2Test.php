<?php

use Piano\Application2 as Application;
use Piano\Di;

/**
 * @group php7
 */
class Application2Test extends PHPUnit_Framework_TestCase
{
    public function testItMustSetTheDIContainerOnConstructor()
    {
        $class = new Application(new Di());

        $this->assertTrue(
            method_exists($class, 'getDi'),
            'Method "getDi()" must exist'
        );

        $container = $class->getDi();

        $this->assertInstanceOf('\Piano\Di', $container);
        $this->assertInstanceOf('\Pimple\Container', $container);
    }
}
