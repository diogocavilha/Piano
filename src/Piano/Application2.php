<?php

declare(strict_types=1);

namespace Piano;

use \Piano\Di as Container;

/**
 * @author Diogo Alexsander Cavilha <diogocavilha@gmail.com>
 */
class Application2
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getDi() : Container
    {
        return $this->container;
    }
}
