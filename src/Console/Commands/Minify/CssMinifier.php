<?php

namespace Miqu\Console\Commands\Minify;

use Ahc\Cli\Input\Command;
use Miqu\Console\Commands\CommandHelpers;
use Exception;
use MatthiasMullie\Minify\CSS;
use Tightenco\Collect\Support\Collection;

class CssMinifier extends Command
{
    use CommandHelpers;

    public function __construct()
    {
        parent::__construct('minify:css', 'Minifies all the css files in the public/ directory into a single file called bundle.min.css that will be places in public/css directory');
    }

    public function execute()
    {
        $path = (string)string(BASE_DIRECTORY)->append('public')->append(DIRECTORY_SEPARATOR)
            ->append('*');
        $minifier = new CSS();
        $this->getCssFiles($path)->each(function($file) use ($minifier) {
            $minifier->add($file);
        });

        $outputPath = (string)string(BASE_DIRECTORY)->append('public')->append(DIRECTORY_SEPARATOR)
            ->append('css')->append(DIRECTORY_SEPARATOR)->append('bundle.min.css');

        try {
            $contents = $minifier->execute();
            $this->writeToFile($outputPath, $contents);
            $this->io()->green('CSS bundle has been created. Please check ' . $outputPath);
        } catch ( Exception $exception ) {
            $this->io()->error("{$exception->getMessage()} - {$exception->getFile()} : {$exception->getLine()}");
        }
    }

    private function getCssFiles(string $path): Collection
    {
        return collect(glob($path))->map(function($item) {
            if ( is_dir( $item ) )
                return $this->getCssFiles($item . DIRECTORY_SEPARATOR . '*');
            if ( $this->isCssFile($item) )
                return $item;
            return null;
        })->reject(function($item) {
            return $item === null;
        })->flatten();
    }

    private function isCssFile(string $item): bool
    {
        return pathinfo($item, PATHINFO_EXTENSION) === 'css';
    }
}