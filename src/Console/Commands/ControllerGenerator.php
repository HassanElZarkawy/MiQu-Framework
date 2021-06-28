<?php

namespace Miqu\Console\Commands;

use Ahc\Cli\Input\Command;
use Ahc\Cli\IO\Interactor;

class ControllerGenerator extends Command
{
    use CommandHelpers;

    /**
     * @var string|null
     */
    private $name = null;

    public function __construct()
    {
        parent::__construct('make:controller', 'Generates a new controller in Controllers/ folder');
    }

    public function interact(Interactor $io)
    {
        if ( ! $this->name )
            $this->name = $io->prompt('Enter Controller Name');
    }

    public function execute()
    {
        $contents = $this->stubContents( 'Controller' );

        $this->name = (string)string($this->name)->replace('controller', '')->replace('Controller', '')
            ->append('Controller');

        $replacements = [
            '{{class}}' => $this->name
        ];

        $contents = $this->applyVariables( $replacements, $contents );

        $this->writeToFile( getcwd() . "/Controllers/$this->name.php", $contents );
        $this->io()->green("Controller $this->name has been created successfully");
    }
}