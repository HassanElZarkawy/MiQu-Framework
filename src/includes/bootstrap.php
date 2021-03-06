<?php /** @noinspection PhpUnhandledExceptionInspection */

use Miqu\Core\AppEnvironment;
use Miqu\Core\CapsuleManager;
use Miqu\Core\Localization\LocalizationManager;
use Miqu\Core\Views\View;
use eftec\bladeone\BladeOne;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Phpfastcache\CacheManager;
use Phpfastcache\Config\ConfigurationOption;
use Phpfastcache\Exceptions\PhpfastcacheInvalidConfigurationException;
use Whoops\Run;

/*
|--------------------------------------------------------------------------
| Define the base directory for the application
|--------------------------------------------------------------------------
*/
if ( ! defined( 'BASE_DIRECTORY' ) )
{
    define( 'BASE_DIRECTORY', getcwd() . DIRECTORY_SEPARATOR );
}

/*
|--------------------------------------------------------------------------
| Turn off public error reporting
|--------------------------------------------------------------------------
*/
ini_set('display_errors', 'Off');

/*
|--------------------------------------------------------------------------
| Start the session
|--------------------------------------------------------------------------
*/
if ( session_status() === PHP_SESSION_NONE )
    session_start();

/*
|--------------------------------------------------------------------------
| Load Environment variables
|--------------------------------------------------------------------------
*/
Miqu\Core\Environment::load( BASE_DIRECTORY . 'config.php' );

if ( \Miqu\Helpers\env( 'environment' ) === AppEnvironment::DEVELOPMENT )
{
    global $debugger;
    $debugger = new DebugBar\StandardDebugBar;
}

if ( \Miqu\Helpers\env('database.enabled') )
    CapsuleManager::boot();

/*
|--------------------------------------------------------------------------
| Create a global Dependency Injection container
|--------------------------------------------------------------------------
*/
global $container;
$container = new Miqu\Core\Container;

/*
|--------------------------------------------------------------------------
| Set an instance for the cache manager
|--------------------------------------------------------------------------
*/
if ( \Miqu\Helpers\env( 'cache.enabled' ) ) {
    try {
        CacheManager::setDefaultConfig(new ConfigurationOption([
            'path' => BASE_DIRECTORY . \Miqu\Helpers\env('cache.path'),
        ]));
    } catch (PhpfastcacheInvalidConfigurationException | ReflectionException $e) {
        // fail silently
    }
}

/*
|--------------------------------------------------------------------------
| Set an instance for the Localization manager
|--------------------------------------------------------------------------
*/
$container->RegisterSingleton( LocalizationManager::class, function() {
    return new Miqu\Core\Localization\LocalizationManager();
} );

/*
|--------------------------------------------------------------------------
| Register the default view handler
|--------------------------------------------------------------------------
*/
$container->Register( Miqu\Core\Interfaces\IView::class, function( $c ) {
    return $c->Resolve(View::class);
} );

/*
|--------------------------------------------------------------------------
| Register the default view engine
|--------------------------------------------------------------------------
*/
$container->Register( Miqu\Core\Interfaces\IViewEngine::class, function() {
    return new BladeOne(
        BASE_DIRECTORY . \Miqu\Helpers\env('blade.views_path'),
        BASE_DIRECTORY . \Miqu\Helpers\env('blade.bin_path'),
        \Miqu\Helpers\env('blade.mode')
    );
} );

/*
|--------------------------------------------------------------------------
| Auto handle errors
|--------------------------------------------------------------------------
*/
if ( \Miqu\Helpers\env('environment') === Miqu\Core\AppEnvironment::PRODUCTION )
{
    register_shutdown_function(
        function() {
            $error = error_get_last();

            if ( ! $error ) // for some reason this function is getting called sometimes without any errors. If so just ignore it.
                return;

            if ( isset( $error[ 'message' ] ) )
                logger()->error( $error[ 'message' ] );

            (new SapiEmitter)->emit(response()->view('errors.500'));
    } );
}
else
    (new Run)->pushHandler(new Whoops\Handler\PrettyPageHandler)->register();

/*
|--------------------------------------------------------------------------
| Create a singleton class for the App
|--------------------------------------------------------------------------
*/
$container->RegisterSingleton( Miqu\Core\App::class, function () {
    return new Miqu\Core\App();
} );


/*
|--------------------------------------------------------------------------
| Register Datatables Provider
|--------------------------------------------------------------------------
*/
$container->Register(\Miqu\Core\Views\DataTables\Contracts\IDataTableResults::class, \Miqu\Core\Views\DataTables\DataTableResults::class);
$container->Register( \Miqu\Core\Views\DataTables\Contracts\IDataTable::class, \Miqu\Core\Views\DataTables\DataTable::class);

/*
|--------------------------------------------------------------------------
| Load all the routes for HTTP requests
|--------------------------------------------------------------------------
*/
if ( isset( $_SERVER['HTTP_HOST'] ) && ! empty( $_SERVER[ 'HTTP_HOST' ] ) )
    autoload_directory( BASE_DIRECTORY . 'Routes/' );