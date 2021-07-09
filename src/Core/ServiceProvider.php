<?php

namespace Miqu\Core;

use Miqu\Core\Interfaces\IContainer;

abstract class ServiceProvider
{
    /**
     * @var IContainer
     */
    public $container;

    public abstract function register();

    public abstract function boot();
}