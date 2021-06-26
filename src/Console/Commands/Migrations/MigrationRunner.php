<?php

namespace Miqu\Console\Commands\Migrations;

use Ahc\Cli\Input\Command;
use Ahc\Cli\IO\Interactor;
use Miqu\Console\Commands\Seeds\SeedRunner;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use ReflectionException;

class MigrationRunner extends Command
{
    /**
     * @var string|null
     */
    private $direction = null;

    public function __construct()
    {
        parent::__construct('migrate', 'Runs the migrations and update the database state.');
        $this->option('-f --fresh', 'Clean the database first and then perform all the migrations.');
    }

    public function interact(Interactor $io)
    {
        if ( ! $this->direction )
        {
            $directions = [ '1' => 'up', '2' => 'down' ];
            $choice = $io->choice('Select Migration Direction', $directions, '1');
            $this->direction = $directions[ $choice ];
        }
    }

    public function execute($fresh, $seed)
    {
        $files = glob((string)string(BASE_DIRECTORY)->append('Migrations')->append(DIRECTORY_SEPARATOR)->append('*.php'));
        $migrations = collect($files)->sort()->map(function($file) {
            require_once $file;
            $name = collect( string( basename( $file ) )->replace('.php', '')->split('[0-9]') )->last();
            $class = (string)string( $name )->trimLeft('_')->upperCamelize();
            return new $class;
        });

        if ( $this->direction === 'up' && $fresh )
            Capsule::schema()->dropAllTables();

        $completed_migrations = $this->getCompletedMigrations();

        $last_batch = collect($completed_migrations)->max(function($item) {
            return $item->batch;
        });

        $next_batch = $last_batch + 1;
        $migrations->filter(function($item) use($completed_migrations) {
            return ! $this->migrationCompleted($item, $completed_migrations);
        })->each(function($item) {
            if ( method_exists( $item, $this->direction ) )
                call_user_func( [ $item, $this->direction ] );
        })->each(function($item) use($next_batch) {
            Capsule::table('migrations')->insert([
                'migration' => get_class($item),
                'batch' => $next_batch
            ]);
        });

        if ( $seed )
        {
            try {
                (new SeedRunner)->execute();
            } catch (ReflectionException $e) {
                $this->io()->error("{$e->getMessage()} in {$e->getFile()} : {$e->getLine()}");
            }
        }
    }

    /**
     * @return array
     */
    private function getCompletedMigrations(): array
    {
        if ( ! Capsule::schema()->hasTable('migrations') )
        {
            Capsule::schema()->create('migrations', function(Blueprint $table) {
                $table->id();
                $table->string('migration');
                $table->integer('batch')->index();
            });

            return [];
        }

        return Capsule::table('migrations')->get()->all();
    }

    private function migrationCompleted($migration, array $completed) : bool
    {
        $name = get_class($migration);
        return collect($completed)->map(function($item) {
                return $item->migration;
            })->first(function($item) use($name) {
                return $item === $name;
            }) !== null;
    }
}