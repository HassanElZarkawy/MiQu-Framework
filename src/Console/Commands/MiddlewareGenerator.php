<?php

namespace Miqu\Console\Commands;

use Ahc\Cli\Input\Command;
use Ahc\Cli\IO\Interactor;

class MiddlewareGenerator extends Command
{
    use CommandHelpers;

    /**
     * @var string|null
     */
    private $name = null;

    public function __construct()
    {
        parent::__construct('make:middleware', 'Creates a middleware class in Middlewares/ folder');
    }

    public function interact(Interactor $io)
    {
        if ( ! $this->name )
            $this->name = $io->prompt('Enter a name for the middleware');
    }

    public function execute()
    {
        $contents = $this->stubContents( 'Middleware' );
        $replacements = [
            '{{class}}' => $this->name
        ];
        $contents = $this->applyVariables( $replacements, $contents );
        $this->writeToFile( BASE_DIRECTORY . 'Middlewares/' . $this->name . '.php', $contents );
    }
}