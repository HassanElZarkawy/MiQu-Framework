<?php

namespace Miqu\Core\Localization;

use Miqu\Core\AppEnvironment;
use Exception;
use Tightenco\Collect\Support\Collection;

class LocalizationManager
{
    /** @var string */
    private $language;

    /** @var string */
    private $compilePath;

    /** @var string */
    private $languagesPath;

    /** @var bool */
    private $initialized = false;

    /** @var array */
    private $translations = [];

    public function __construct()
    {
        $this->language = (string)\Miqu\Helpers\env('localization.default_language');
        $this->compilePath = BASE_DIRECTORY . DIRECTORY_SEPARATOR . \Miqu\Helpers\env('localization.compile_path');
        $this->languagesPath = BASE_DIRECTORY . DIRECTORY_SEPARATOR . \Miqu\Helpers\env('localization.languages_path');
    }

    /**
     * @throws Exception
     */
    public function init() : void
    {
        if ( $this->initialized )
            return;

        if ( $this->isLanguageCachedBefore() )
        {
            $this->translations = json_decode( file_get_contents( $this->getLanguageCacheFilePath() ), true );
            $this->initialized = true;
            return;
        }

        $files = $this->getTranslationFiles( $this->language );

//        if ( count( $files ) === 0 )
//            throw new Exception( "Language $this->language does not have any translation files." );

        $this->translations = $this->getTranslationsFromLanguageFiles( $files );

        if ( ! $this->cacheLanguageTranslations() )
            throw new Exception( "Couldn't write to file: {$this->getLanguageCacheFilePath()}" );

        $this->initialized = true;
    }

    /**
     * @param string $language
     * @throws Exception
     */
    public function setLanguage( string $language )
    {
        $this->language = $language;
        $this->initialized = false;
        $this->init();
    }

    public function translate( string $key ) : ?string
    {
        if ( array_key_exists( $key, $this->translations ) )
            return $this->translations[ $key ];

        return $key;
    }

    public function getLanguage() : string
    {
        return $this->language;
    }

    private function isLanguageCachedBefore() : bool
    {
        if ( \Miqu\Helpers\env( 'environment' ) === AppEnvironment::DEVELOPMENT || \Miqu\Helpers\env( 'environment' ) === AppEnvironment::TESTING )
            return false;

        if ( file_exists( $this->getLanguageCacheFilePath() ) )
            return true;
        return false;
    }

    private function getLanguageCacheFilePath() : string
    {
        $cache_file_name = md5($this->language) . '.lang';
        return $this->compilePath . "/$cache_file_name";
    }

    private function getTranslationFiles( string $language ) : array
    {
        $path = (string)string($this->languagesPath)->append(DIRECTORY_SEPARATOR)->append($language)->append(DIRECTORY_SEPARATOR)->append('*.php');
        return glob( $path );
    }

    private function getTranslationsFromLanguageFiles( array $files ) : array
    {
        $pairs = new Collection;

        foreach ( $files as $file )
        {
            $data = require $file;
            $pairs = $pairs->merge($data);
        }

        return $pairs->all();
    }

    private function cacheLanguageTranslations() : bool
    {
        if ( $this->isLanguageCachedBefore() )
            unlink( $this->getLanguageCacheFilePath() );

        $json = json_encode( $this->translations );
        $handler = fopen( $this->getLanguageCacheFilePath(), 'w' );
        $written = fwrite( $handler, $json );
        fclose( $handler );

        return $written !== false;
    }
}