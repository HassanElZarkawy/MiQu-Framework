<?php

namespace Miqu\Console\Commands\Migrations;

use Ahc\Cli\Input\Command;
use Ahc\Cli\IO\Interactor;
use Miqu\Console\Commands\CommandHelpers;

class MigrationGenerator extends Command
{
    use CommandHelpers;

    /**
     * @var string|null
     */
    private $name = null;

    public function __construct()
    {
        parent::__construct('make:migration', 'Creates a new Migration that will be used to updated the database state.');
        $this->option('-n --name', 'Generated migration name (CamelCase)');
    }

    public function interact(Interactor $io)
    {
        if ( ! $this->name )
            $this->name = $io->prompt('Enter migration name (CamelCase)');
    }

    public function execute()
    {
        $this->name = (string)string($this->name)->prepend('_')->prepend(time())->underscored();
        $class = collect( string( $this->name )->split('[0-9]') )->last();

        $contents = $this->stubContents( 'Migration' );
        $replacements = [
            '{{class}}' => (string)string($class)->upperCamelize()
        ];

        $contents = $this->applyVariables( $replacements, $contents );
        $file_path = getcwd() . "/Migrations/$this->name.php";
        $this->writeToFile( $file_path, $contents );
        $this->app()->io()->green("Migration $this->name has been created successfully.");
    }
}