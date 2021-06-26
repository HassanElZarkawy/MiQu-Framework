<?php

namespace Miqu\Console\Commands\Seeds;

use Ahc\Cli\Input\Command;
use Miqu\Console\Commands\CommandHelpers;
use Exception;
use Faker\Factory;
use Faker\Generator;
use ReflectionException;

class SeedRunner extends Command
{
    use CommandHelpers;

    /** @var Generator $faker */
    private $faker;

    public function __construct()
    {
        parent::__construct('run:seeds', 'Runs all the seeder classes (if found)');
        $this->faker = Factory::create( \Miqu\Helpers\env('localization.default_language') );
    }

    /**
     * @throws ReflectionException
     */
    public function execute()
    {
        $seeders = $this->get_seeders();
        $seeders = collect( $seeders )->sortBy(function($item) {
            return $item->order;
        });

        foreach ( $seeders as $instance ) {
            $instance->faker = $this->faker;
            $start = microtime(true);
            for ($i = 0; $i < $instance->count; $i++) {
                try {
                    $data = $instance->data();
                    $instance->model::create($data);
                } catch (Exception $exception) {
                    $this->io()->error("{$exception->getMessage()} - {$exception->getFile()} : {$exception->getLine()}");
                }
            }

            $total = $start - microtime(true);
            $this->io()->green( get_class( $instance ) . "took total of: $total seconds" );
        }
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    private function get_seeders(): array
    {
        return $this->get_classes_from_folder( BASE_DIRECTORY . 'Seeds' );
    }
}
