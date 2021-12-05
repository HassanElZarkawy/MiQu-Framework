<?php

namespace Miqu\Console\Commands;

use Ahc\Cli\Input\Command;
use Ahc\Cli\IO\Interactor;

class RequestGenerator extends Command
{
    use CommandHelpers;

    /**
     * @var string|null
     */
    private $name = null;

    public function __construct()
    {
        parent::__construct('make:request', 'Generates a new request validator in Requests/ folder');
    }

    public function interact(Interactor $io)
    {
        if ( ! $this->name )
            $this->name = $io->prompt('Enter Controller Name');
    }

    public function execute()
    {
        $contents = $this->stubContents( 'Request' );

        $this->name = (string)string($this->name)->replace('request', '')->replace('Request', '')
            ->append('Request');

        $replacements = [
            '{{class}}' => $this->name
        ];

        $contents = $this->applyVariables( $replacements, $contents );

        $this->writeToFile( getcwd() . "/Requests/$this->name.php", $contents );
        $this->io()->green("Request $this->name has been created successfully");
    }
}