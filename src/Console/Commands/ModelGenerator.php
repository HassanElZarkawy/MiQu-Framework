<?php

namespace Miqu\Console\Commands;

use Ahc\Cli\Input\Command;
use Ahc\Cli\IO\Interactor;

class ModelGenerator extends Command
{
    use CommandHelpers;

    /**
     * @var string|null
     */
    private $name = null;

    public function __construct()
    {
        parent::__construct('make:model', 'Creates a model representation of a database table');
    }

    public function interact(Interactor $io)
    {
        if ( ! $this->name )
            $this->name = $io->prompt('Enter the model name');
    }

    public function execute()
    {
        $contents = $this->stubContents('Model');

        $replacements = [
            '{{class}}' => $this->name,
            '{{class_lower}}' => strtolower( $this->name )
        ];

        $contents = $this->applyVariables($replacements, $contents);
        $this->writeToFile( BASE_DIRECTORY . 'Models' . DIRECTORY_SEPARATOR . ucfirst($this->name) . '.php', $contents );
        $this->io()->green("Model $this->name has been created successfully");
    }
}