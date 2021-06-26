<?php

namespace Miqu\Console\Commands\Minify;

use Ahc\Cli\Input\Command;
use Miqu\Console\Commands\CommandHelpers;
use Exception;
use MatthiasMullie\Minify\JS;
use Tightenco\Collect\Support\Collection;

class JsMinifier extends Command
{
    use CommandHelpers;

    public function __construct()
    {
        parent::__construct('minify:js', 'Minifies all the javascript files in the public/ directory into a single file called bundle.min.js that will be places in public/js directory');
    }

    public function execute()
    {
        $path = (string)string(BASE_DIRECTORY)->append('public')->append(DIRECTORY_SEPARATOR)
            ->append('*');
        $minifier = new JS();
        $this->getJsFiles($path)->each(function($file) use ($minifier) {
            $minifier->add($file);
        });

        $outputPath = (string)string(BASE_DIRECTORY)->append('public')->append(DIRECTORY_SEPARATOR)
            ->append('js')->append(DIRECTORY_SEPARATOR)->append('bundle.min.js');

        try {
            $contents = $minifier->execute();
            $this->writeToFile($outputPath, $contents);
            $this->io()->green('Javascript bundle has been created. Please check ' . $outputPath);
        } catch ( Exception $exception ) {
            $this->io()->error("{$exception->getMessage()} - {$exception->getFile()} : {$exception->getLine()}");
        }
    }

    private function getJsFiles(string $path): Collection
    {
        return collect(glob($path))->map(function($item) {
            if ( is_dir( $item ) )
                return $this->getJsFiles($item . DIRECTORY_SEPARATOR . '*');
            if ( $this->isJsFile($item) )
                return $item;
            return null;
        })->reject(function($item) {
            return $item === null;
        })->flatten();
    }

    private function isJsFile(string $item): bool
    {
        return pathinfo($item, PATHINFO_EXTENSION) === 'js';
    }
}