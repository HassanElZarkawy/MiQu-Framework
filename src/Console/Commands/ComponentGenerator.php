<?php

namespace Miqu\Console\Commands;

use Ahc\Cli\Input\Command;
use Ahc\Cli\IO\Interactor;

class ComponentGenerator extends Command
{
    use CommandHelpers;

    /**
     * @var string|null
     */
    private $name = null;

    public function __construct()
    {
        parent::__construct('make:component', 'Generates a new component in Components/ folder');
    }

    public function interact(Interactor $io)
    {
        if ( ! $this->name )
            $this->name = $io->prompt('Enter Component Name');
    }

    public function execute()
    {
        $contents = $this->stubContents( 'Component' );

        $this->name = (string)string($this->name)
            ->replace('component', '')
            ->replace('Component', '');
        $view = (string)string($this->name)->dasherize();

        $replacements = [
            '{{class}}' => $this->name,
            '{{view}}' => $view,
        ];

        $contents = $this->applyVariables( $replacements, $contents );
        $this->writeToFile( getcwd() . "/Components/$this->name.php", $contents );
        $contents = $this->stubContents('ComponentView');
        $contents = $this->applyVariables($replacements, $contents);
        $this->writeToFile(getcwd() . "/Views/components/$view.blade.php", $contents);
        $this->io()->green("Component $this->name has been created successfully");
    }
}