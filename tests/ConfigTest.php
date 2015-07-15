<?php

class ConfigTest extends PHPUnit_Framework_Testcase
{
    public $config;

    public function setUp()
    {
        $this->config = new Piano\Config();
    }

    public function testApplicationFolderShouldBeTheSame()
    {
        $appFolder = 'application';
        $this->config->setApplicationFolder($appFolder);
        $this->assertEquals($appFolder, $this->config->get('application_folder'), 'Application folder is not the same');
    }

    public function testDefaultModuleShouldBeTheSame()
    {
        $defaultModule = 'application';
        $this->config->setDefaultModule($defaultModule);
        $this->assertEquals($defaultModule, $this->config->get('default_module'), 'Default module is not the same');
    }

    public function testLayoutPerModule()
    {
        $layoutPerModule = [
            'base' => [
                'application',
            ],
            'admin' => [
                'admin',
            ],
        ];

        $this->config->setLayoutPerModule($layoutPerModule);
        $this->assertInternalType('array', $this->config->get('layout_module'), 'It should be an array');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Param name is expected.
     */
    public function testSetDefaultModuleShouldThrowAnInvalidArgumentException()
    {
        $this->config->setDefaultModule();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Param name is expected.
     */
    public function testSetApplicationFolderShouldThrowAnInvalidArgumentException()
    {
        $this->config->setApplicationFolder();
    }

    /**
     * @expectedException Exception
     */
    public function testMethodGetArrayShouldThrowAnException()
    {
        $this->config->getArray('keyDoesNotExist');
    }

    public function testMethodGetArrayShouldReturnAnArray()
    {
        $this->assertInternalType('array', $this->config->getArray());
    }

    /**
     * @expectedException Exception
     */
    public function testMethodGetObjectShouldThrowAnException()
    {
        $this->config->getObject('keyDoesNotExist');
    }

    public function testMethodGetObjectShouldReturnAnObject()
    {
        $this->assertInternalType('object', $this->config->getObject());
    }

    public function testMethodGetObjectShouldWork()
    {
        $defaultModule = 'application';
        $this->config->setDefaultModule($defaultModule);
        $this->assertInternalType('object', $this->config->getObject('default_module'));
    }
}
