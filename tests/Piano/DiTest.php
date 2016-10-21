<?php

use Piano\Di;

class DiTest extends PHPUnit_Framework_TestCase
{
    private $class;

    public function setUp()
    {
        $this->class = new Di();
    }

    public function testContainerMustBeAPianoDiObject()
    {
        $this->assertInstanceOf('\Piano\Di', $this->class);
    }

    public function testContainerMustExtendsPimpleContainer()
    {
        $this->assertInstanceOf('\Pimple\Container', $this->class);
    }
}
