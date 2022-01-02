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
            'url' => '/alive',
            'scriptsPath' => 'http://miqu.local/Views/components/',
            'namespace' => 'Miqu\Components\\',
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

        // $yoyo->registerComponents($classes->combine($views)->all());
        $classes->combine($views)->each(function($view, $class) {
            Yoyo::registerComponent($view, $class);
        });
    }

    private function registerRoutes()
    {
        Route::post('/alive', function () {
            $output = (new Yoyo())->update();
            $response = response()->withStatus(200);
            $response->getBody()->write($output);
            return $response;
        });
    }
}