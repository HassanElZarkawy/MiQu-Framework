<?php

namespace Miqu\Core;

use Exception;
use Illuminate\Database\Capsule\Manager;
use Tightenco\Collect\Support\Collection;

class CapsuleManager
{
    private static $booted = false;
    private static $capsule = null;

    /**
     * @throws Exception
     */
    public static function boot()
    {
        if ( self::$booted )
            return;

        self::configure();
        self::$capsule->setAsGlobal();
        self::$capsule->bootEloquent();
        self::$booted = true;
    }

    /**
     * @throws Exception
     */
    public static function configure()
    {
        self::$capsule = new Manager;
        $configuration = self::getCurrentConfiguration();
        self::$capsule->addConnection($configuration);
    }

    /**
     * @throws Exception
     */
    private static function getCurrentConfiguration() : array
    {
        $driver = strtolower( env('database.driver') );
        $config = null;
        if ( $driver === 'mysql' )
            $config = self::getMysqlConfiguration();
        else if ( $driver === 'sqlite' )
            $config = self::getSqliteConfiguration();

        if ( $config === null )
            throw new Exception("Driver $driver is not supported");

        return $config->merge([ 'driver' => $driver ])->all();
    }

    private static function getMysqlConfiguration() : Collection
    {
        return self::getConfiguration('database.configurations.mysql.', [
            'host' => 'host',
            'database' => 'name',
            'username' => 'user',
            'password' => 'password',
            'charset' => 'charset',
            'collation' => 'collation',
            'prefix' => 'prefix'
        ]);
    }

    private static function getSqliteConfiguration() : Collection
    {
        return self::getConfiguration('database.configurations.sqlite.', [
            'database' => 'database'
        ]);
    }

    private static function getConfiguration(string $path, array $mapping) : Collection
    {
        return collect($mapping)->map(function ($item) use($path) {
            return env( $path . $item );
        });
    }
}