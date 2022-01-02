<?php

namespace Miqu\Core\Providers\Yoyo;

use Clickfwd\Yoyo\View;
use Clickfwd\Yoyo\Yoyo;
use Miqu\Core\Http\Route;
use Miqu\Core\ServiceProvider;

class YoyoServiceProvider extends ServiceProvider
{
    public function register()
    {

    }

    public function boot()
    {
        $this->registerRoutes();
        $yoyo = new Yoyo();
        $yoyo->configure([
            'url' => \Miqu\Helpers\env('alive.url') ?? '/alive',
            'scriptsPath' => \Miqu\Helpers\env('alive.scripts_path') ?? url('vendor/globalsoft/miqu/src/Core/Providers/Yoyo'),
            'namespace' => \Miqu\Helpers\env('alive.namespace') ?? 'Components\\',
        ]);
        $yoyo->registerViewProvider(function() {
            return new YoyoBladeProvider( new View( BASE_DIRECTORY . 'Views/components' ) );
        });

        $files = glob(BASE_DIRECTORY . 'Components' . DIRECTORY_SEPARATOR . '*.php');
        $classes = collect($files)->map(function($file) {
            return (string)string($file)->replace(BASE_DIRECTORY, '')->replace('.php', '');
        });
        $views = collect($files)->map(function($file) {
            return (string)string(basename($file))->replace('.php', '')->dasherize();
        });

        $classes->combine($views)->each(function($view, $class) {
            Yoyo::registerComponent($view, $class);
        });
    }

    private function registerRoutes()
    {
        $types = \Miqu\Helpers\env('alive.request_types') ?? [ 'GET', 'POST' ];
        $standard = ['get', 'post', 'delete', 'put', 'patch'];
        $url = \Miqu\Helpers\env('alive.url') ?? '/alive';
        $handleRequest = function() {
            $output = (new Yoyo())->update();
            $response = response()->withStatus(200);
            $response->getBody()->write($output);
            return $response;
        };
        foreach ($types as $type)
        {
            $method = strtolower($type);
            if (!in_array($method, $standard))
                return;
            Route::{$method}($url, $handleRequest);
        }
    }
}