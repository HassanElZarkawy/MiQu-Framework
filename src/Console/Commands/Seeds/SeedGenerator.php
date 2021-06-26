<?php

namespace Miqu\Console\Commands\Seeds;

use Ahc\Cli\Input\Command;
use Ahc\Cli\IO\Interactor;
use Miqu\Console\Commands\CommandHelpers;

class SeedGenerator extends Command
{
    use CommandHelpers;

    /**
     * @var string|null
     */
    private $name = null;

    public function __construct()
    {
        parent::__construct('make:seeder', 'Creates a new seeder class in Seeds/ folder');
        $this->option('-m --model', 'Specifies the model class for the seeder', null, 'Models\\User');
    }

    public function interact(Interactor $io)
    {
        if ( ! $this->name )
            $this->name = $io->prompt('Seeder Name');
    }

    public function execute(string $model)
    {
        $stub_content = $this->stubContents( 'seeder' );
        $stub_content = $this->applyVariables([
            '{{class}}' => $this->name,
            '{{model}}' => $model
        ], $stub_content);

        $this->writeToFile(BASE_DIRECTORY . "Seeds/$this->name.php", $stub_content);

        $this->io()->green("Seeder class '$this->name' has been created successfully");
    }
}