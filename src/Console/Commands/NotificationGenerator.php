<?php

namespace Miqu\Console\Commands;

use Ahc\Cli\Input\Command;
use Ahc\Cli\IO\Interactor;

class NotificationGenerator extends Command
{
    use CommandHelpers;

    /**
     * @var string|null
     */
    private $name = null;

    public function __construct()
    {
        parent::__construct('make:notification', 'Creates a notification class that can be used to notify a model');
    }

    public function interact(Interactor $io)
    {
        if ( ! $this->name )
            $this->name = $io->prompt('Enter a name for the notification');
    }

    public function execute()
    {
        $this->name = (string)string($this->name)->upperCamelize();
        $contents = $this->stubContents( 'Notification' );
        $replacements = [
            '{{class}}' => $this->name
        ];

        $contents = $this->applyVariables( $replacements, $contents );
        $this->writeToFile( getcwd() . 'Notifications/' . $this->name . '.php', $contents );
        $this->app()->io()->green("Notification $this->name has been created successfully");
    }
}