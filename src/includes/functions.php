<?php

use DebugBar\JavascriptRenderer;
use Miqu\Core\App;
use Miqu\Core\AppEnvironment;
use Miqu\Core\Authentication;
use Miqu\Core\Events\EventBase;
use Miqu\Core\Http\HttpResponse;
use Miqu\Core\Interfaces\IView;
use Miqu\Core\Localization\LocalizationManager;
use Miqu\Core\LogManager;
use Gaufrette\Adapter\Local as LocalAdapter;
use Gaufrette\Filesystem;
use Laminas\Diactoros\ServerRequest;
use League\Route\Router;
use League\Uri\UriInfo;
use Miqu\Core\Mailer;
use Monolog\Logger;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;
use Stringy\Stringy;

/**
 * @return Authentication
 */
function auth(): Authentication
{
    return new Authentication;
}

/**
 * Creates a Stringy object and returns it on success.
 *
 * @param mixed $str Value to modify, after being cast to string
 * @param string|null $encoding The character encoding
 * @return Stringy A Stringy object
 * @throws InvalidArgumentException if an array or object without a
 *         __toString method is passed as the first argument
 */
function string($str, string $encoding = null): Stringy
{
    return new Stringy($str, $encoding);
}

/**
 * @param string $key
 * @return string
 */
function __(string $key): string
{
    if ( ! \Miqu\Helpers\env('localization.enabled') )
        return $key;

    global $container;
    try {
        /** @var LocalizationManager $manager */
        $manager = $container->Resolve(LocalizationManager::class);
        return $manager->translate($key);
    } catch (Exception $ex) {
        return $key;
    }
}

function _n(string $number, int $decimals = 0): string
{
    if ( ! \Miqu\Helpers\env('localization.enabled') )
        return $number;

    $locale = lang();
    $str = number_format($number, $decimals);
    if ($locale === 'ar') {
        $arabic_eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $arabic_western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace($arabic_western, $arabic_eastern, $str);
    }
    return $str;
}

function lang(): string
{
    if ( ! \Miqu\Helpers\env('localization.enabled'))
        return \Miqu\Helpers\env('localization.default_language');

    global $container;
    try {
        /** @var LocalizationManager $manager */
        $manager = $container->Resolve(LocalizationManager::class);
        return $manager->getLanguage();
    } catch (Exception $ex) {
        return \Miqu\Helpers\env('localization.default_language');
    }
}

function debug($variable): void
{
    if (\Miqu\Helpers\env('environment') !== AppEnvironment::DEVELOPMENT)
        return;

    global $debugger;
    $debugger['messages']->addMessage($variable);
}

function debugBarAssets(): string
{
    if (\Miqu\Helpers\env('environment') !== AppEnvironment::DEVELOPMENT)
        return '';

    global $debugger;
    /** @var JavascriptRenderer $renderer */
    $renderer = $debugger->getJavascriptRenderer();
    $assets = $renderer->getAssets();
    $html = '';
    foreach ($assets as $group) {
        if (is_array($group)) {
            foreach ($group as $file) {
                $file_url = str_replace(BASE_DIRECTORY, url(''), $file);
                if (string($file)->endsWith('js'))
                    $html .= "<script src='$file_url'></script>";
                else if (string($file)->endsWith('css'))
                    $html .= "<link href='$file_url' rel='stylesheet'>";
            }
        }
    }
    return $html;
}

function renderDebugBar(): string
{
    if (\Miqu\Helpers\env('environment') !== AppEnvironment::DEVELOPMENT)
        return '';

    global $debugger;
    $renderer = $debugger->getJavascriptRenderer();
    return $renderer->render();
}

/**
 * @throws ReflectionException
 */
function registerDebugBarCollector(string $class): void
{
    if (\Miqu\Helpers\env('environment') !== AppEnvironment::DEVELOPMENT)
        return;

    global $debugger;
    $debugger->addCollector(app()->make($class));
}

/**
 * Return the default router for the app
 * @param string $abstract a fully qualified name of a class eg(Miqu\Core\Http\Request::class)
 * @return mixed
 * @throws ReflectionException
 */
function make(string $abstract)
{
    return app()->make($abstract);
}

/**
 * Return a singleton class of Miqu\Core\App
 * @return App|null
 */
function app(): ?App
{
    try {
        global $container;
        return $container->Resolve(App::class);
    } catch (Exception $exception) {
        return null;
    }
}

/**
 * Return a singleton class of Miqu\Core\App
 * @return ServerRequest
 */
function request(): ServerRequest
{
    return app()->request();
}

/**
 * @return HttpResponse
 */
function response(): HttpResponse
{
    return new HttpResponse;
}

/**
 * Return the default router for the app
 * Return the default router for the app
 * @return Router
 */
function router(): Router
{
    return app()->router();
}

/**
 * @param array $rules
 * @param array $attributes
 * @return Validation
 */
function validate(array $rules, array $attributes = []): Validation
{
    $validator = new Validator;
    $data = array_merge($_POST, $_GET, $_FILES);
    return $validator->validate($data, $rules, $attributes);
}

/**
 * Return the default router for the app
 * @param string $channel
 * @return Logger|null
 */
function logger(string $channel = 'default'): ?Logger
{
    if ( ! \Miqu\Helpers\env('logging.enabled') )
        return null;

    return LogManager::get($channel);
}

/**
 * Return a ready to use instance of the Mailer class
 * @return Mailer
 * @throws ReflectionException
 */
function mailer(): Mailer
{
    return make(Mailer::class);
}

/**
 * Return the default router for the app
 * @param string $view_name The relative path for the view file in (dot) notation
 * @param array $data
 * @return IView
 * @throws ReflectionException
 */
function view(string $view_name, array $data): IView
{
    /** @var IView $view */
    $view = app()->make(IView::class);
    $view->with($data)->view($view_name);
    return $view;
}

/**
 * Returns an instance of Gaufrette\Filesystem based on the storage configuration in .env.php
 * @return Filesystem
 */
function storage(): Filesystem
{
    $rootPath = (string)string(BASE_DIRECTORY)->trimRight(DIRECTORY_SEPARATOR)->append(\Miqu\Helpers\env('storage.folder'));
    $adapter = new LocalAdapter(
        $rootPath,
        \Miqu\Helpers\env('storage.auto_create'),
        \Miqu\Helpers\env('storage.permissions')
    );
    return new Filesystem($adapter);
}

/**
 * Triggers an event and all of it's listeners.
 * @param EventBase $event instance of an event
 * @return void
 * @throws Exception
 */
function event(EventBase $event): void
{
    $event->boot();

    if ($event->getListenersCount() < 1)
        return;

    $event->dispatch();
}


/**
 * Prints out a hidden input field with a csrf token to be validated in the next request.
 * @return string
 * @throws Exception
 */
function csrf(): string
{
    if (session('csrf_token'))
        $token = session('csrf_token');
    else {
        $token = bin2hex(random_bytes(32));
        session('csrf_token', $token);
    }

    return "<input type='hidden' name='csrf_token' value='$token'>";
}

/**
 * Validates a csrf token sent with the request.
 * @param string $token The token sent with the request
 * @return bool
 */
function validate_csrf(string $token): bool
{
    $stored = session('csrf_token');

    if ($stored != $token)
        return false;

    unset($_SESSION['csrf_token']);

    return true;
}

function old(string $key)
{
    $serialized = session('old');

    if (!$serialized)
        return null;

    $inputs = unserialize($serialized);

    if (!isset($inputs[$key]))
        return null;

    return $inputs[$key];
}

/**
 * Gets the absolute Url of the public folder. Helpful to include an asset file
 * Usage: asset('css/stylesheet.css') will print http(s)://example.com/public/css/stylesheet.css
 * @param string $asset_path the relative path for the asset in question
 * @return string The full Url path for the asset
 */
function asset(string $asset_path): string
{
    return getBaseUrl() . 'public/' . $asset_path;
}

function url(string $route): string
{
    return getBaseUrl() . ltrim($route);
}

/**
 * @throws Exception
 */
function route(string $name): string
{
    $route = router()->getNamedRoute($name);
    if (!$route)
        throw new Exception("Route with the name of $name does not exist");

    return (string)string(getBaseUrl())->trimRight('/')->append($route->getPath());
}

/**
 * Fetches or sets a session value based on a key
 *
 * @return mixed a session value if no value has been provided, or boolean if a session has been set.
 */
function session()
{
    $args = func_get_args();

    $args_count = count($args);

    if ($args_count < 1) {
        return false;
    }

    $key = $args[0];

    if ($args_count == 1) {

        if (!isset($_SESSION[$key])) {
            return false;
        }

        return $_SESSION[$key];

    } else if ($args_count == 2) {

        $value = $args[1];

        $_SESSION[$key] = $value;

        return true;

    }
    return false;
}

/**
 * Gets the origin url for the current request
 * http://localhost/myproject/index.php?id=8 -> http://localhost/myproject
 * @return string|null
 */
function getBaseUrl(): ?string
{
    return UriInfo::getOrigin(app()->uri()) . '/';
}

function autoload_directory($path)
{
    $items = glob($path . DIRECTORY_SEPARATOR . '*');
    foreach ($items as $item) {
        if (is_file($item)) {
            $isPhp = pathinfo($item)['extension'] === 'php';
            if ($isPhp)
                require_once $item;
        } else {
            autoload_directory($item);
        }
    }
}