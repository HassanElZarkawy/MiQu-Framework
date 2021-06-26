<?php


namespace Miqu\Console\Commands\Tasks;


use Ahc\Cli\Input\Command;
use Miqu\Core\Interfaces\IContainer;
use Crontask\TaskList;
use Crontask\Tasks\Task;
use Exception;

class TaskRunner extends Command
{
    /**
     * @var IContainer
     */
    private $container;

    public function __construct()
    {
        parent::__construct('run:tasks', 'Runs all the tasks that are registered and are due.');

        global $container;
        $this->container = $container;
    }

    public function execute()
    {
        $io = $this->app()->io();
        $taskList = new TaskList;
        $total_tasks = 0;
        collect(env('tasks'))->each(function($expression, $abstract) use($taskList, $total_tasks) {
            try {
                /** @var Task $task */
                $task = $this->container->Resolve($abstract);
            } catch (Exception $exception) {
                return;
            }
            $total_tasks++;
            if( (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ) {
                $task->run();
                return;
            }

            $task->setExpression($expression);
            $taskList->addTask($task);
        });
        $io->green('Running total of ' .$total_tasks . ' Tasks', true );
        $taskList->run();
    }
}