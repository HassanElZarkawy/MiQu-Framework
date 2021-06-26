<?php

namespace Miqu\Console\Commands\Tasks;

use Ahc\Cli\Input\Command;
use Ahc\Cli\IO\Interactor;
use Miqu\Console\Commands\CommandHelpers;

class TaskGenerator extends Command
{
    use CommandHelpers;

    /**
     * @var string|null
     */
    private $task = null;

    public function __construct()
    {
        parent::__construct('make:task', 'Creates a new Task to run on background');
        $this->option('-n --name', 'Generated task name (CamelCase)')
            ->usage('php <bold> $0 make:task -t SampleTask</end>');
    }

    public function interact(Interactor $io)
    {
        if ( ! $this->task )
            $this->task = $io->prompt('Enter Task Name (CamelCase)');
    }

    public function execute()
    {
        $contents = $this->stubContents('task');
        $replacements = [
            '{{class}}' => $this->task
        ];
        $contents = $this->applyVariables( $replacements, $contents );
        $file_path = getcwd() . "/Tasks/{$this->task}.php";
        $this->writeToFile( $file_path, $contents );

        $this->app()->io()->green("Task {$this->task} has been created successfully.");
    }
}