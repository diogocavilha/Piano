<?php

use Piano\Application2 as Application;

/**
 * @group php7
 */
class Application2Test extends PHPUnit_Framework_TestCase
{
    public function testItMustSetTheDIContainerOnConstructor()
    {
        $mockDi = $this->getMockBuilder('\Pimple\Container')
            ->getMock();

        $class = new Application($mockDi);

        $this->assertTrue(
            method_exists($class, 'getDi'),
            'Method "getDi()" must exist'
        );

        $container = $class->getDi();

        $this->assertInstanceOf(
            '\Pimple\Container',
            $container
        );
    }
}
