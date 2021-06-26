<?php

namespace Miqu\Console;

use Ahc\Cli\Application;
use Miqu\Console\Commands\ControllerGenerator;
use Miqu\Console\Commands\MiddlewareGenerator;
use Miqu\Console\Commands\Migrations\MigrationGenerator;
use Miqu\Console\Commands\Migrations\MigrationRunner;
use Miqu\Console\Commands\Minify\CssMinifier;
use Miqu\Console\Commands\Minify\JsMinifier;
use Miqu\Console\Commands\ModelGenerator;
use Miqu\Console\Commands\NotificationGenerator;
use Miqu\Console\Commands\Seeds\SeedGenerator;
use Miqu\Console\Commands\Seeds\SeedRunner;
use Miqu\Console\Commands\Tasks\TaskGenerator;
use Miqu\Console\Commands\Tasks\TaskRunner;
use Miqu\Core\Interfaces\IContainer;
use ReflectionException;

class Commando extends Application
{
    /**
     * @var IContainer
     */
    private $container;

    public function __construct()
    {
        parent::__construct('Commando', '0.1.0');
        global $container;
        $this->container = $container;
    }

    /**
     * @throws ReflectionException
     */
    public function init()
    {
        $commands = [
            TaskGenerator::class,
            TaskRunner::class,
            MigrationRunner::class,
            MigrationGenerator::class,
            SeedGenerator::class,
            SeedRunner::class,
            ControllerGenerator::class,
            ModelGenerator::class,
            NotificationGenerator::class,
            MiddlewareGenerator::class,
            CssMinifier::class,
            JsMinifier::class
        ];
        foreach( $commands as $command )
        {
            $instance = $this->container->Resolve($command);
            $this->add($instance);
        }
    }

    /**
     * @throws ReflectionException
     */
    public function run()
    {
        $this->init();
        $this->handle($_SERVER['argv']);
    }
}