<?php

use Piano\Container;

class ContainerTest extends PHPUnit_Framework_TestCase
{
    private $class;

    public function setUp()
    {
        $this->class = new Container();
    }

    public function testContainerMustBeAPianoContainerObject()
    {
        $this->assertInstanceOf('\Piano\Container', $this->class);
    }

    public function testContainerMustExtendsPimpleContainer()
    {
        $this->assertInstanceOf('\Pimple\Container', $this->class);
    }
}
