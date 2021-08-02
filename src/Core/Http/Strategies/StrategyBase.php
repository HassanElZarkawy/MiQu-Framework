<?php

namespace Miqu\Core\Http\Strategies;

use League\Route\Strategy\ApplicationStrategy;
use Psr\Container\ContainerInterface;

class StrategyBase extends ApplicationStrategy
{
    public function getContainer(): ?ContainerInterface
    {
        return app()->container;
    }
}