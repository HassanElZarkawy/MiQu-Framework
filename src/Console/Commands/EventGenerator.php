<?php

namespace Miqu\Console\Commands;

use Ahc\Cli\Input\Command;
use Ahc\Cli\IO\Interactor;

class EventGenerator extends Command
{
    use CommandHelpers;

    /**
     * @var string|null
     */
    private $name = null;

    public function __construct()
    {
        parent::__construct('make:event', 'Creates an event that can be dispatched anywhere in your app.');
    }

    public function interact(Interactor $io)
    {
        if ( ! $this->name )
            $this->name = $io->prompt('Enter a name for the event');
    }

    public function execute()
    {
        $contents = $this->stubContents( 'Event' );
        $replacements = [
            '{{class}}' => $this->name
        ];
        $contents = $this->applyVariables( $replacements, $contents );
        $this->writeToFile( getcwd() . 'Events/' . $this->name . '.php', $contents );
        $this->io()->green("Event $this->name has been created successfully");
    }
}