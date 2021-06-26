<?php

namespace Miqu\Console\Commands;

use Ahc\Cli\Input\Command;
use Ahc\Cli\IO\Interactor;

class ListenerGenerator extends Command
{
    use CommandHelpers;

    /**
     * @var string|null
     */
    private $name = null;

    public function __construct()
    {
        parent::__construct('make:listener', 'Creates a lister that can be used with an event');
    }

    public function interact(Interactor $io)
    {
        if ( ! $this->name )
            $this->name = $io->prompt('Enter a name for the listener');
    }

    public function execute()
    {
        $contents = $this->stubContents( 'Listener' );
        $replacements = [
            '{{class}}' => $this->name
        ];
        $contents = $this->applyVariables( $replacements, $contents );
        $this->writeToFile( getcwd() . 'Events/Listeners/' . $this->name . '.php', $contents );
    }
}