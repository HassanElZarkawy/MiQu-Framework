<?php


namespace Miqu\Console\Commands;


use ReflectionException;

trait CommandHelpers
{
    /** @var string[] */
    public $messages = [];

    /**
     * @param string $stub
     * @return string
     */
    public function stubContents(string $stub) : string
    {
        $stubFolder = join( DIRECTORY_SEPARATOR, [ __DIR__, '..', 'Stubs', '' ] );
        $stubs_file = "{$stubFolder}{$stub}.stub";

        $handle = fopen( $stubs_file, 'r' );

        $content = fread( $handle, filesize( $stubs_file ) );

        fclose( $handle );

        return $content;
    }

    public function applyVariables( array $variables, string $content ) : string
    {
        foreach ( $variables as $key => $value )
            $content = str_replace( $key, $value, $content );

        return $content;
    }

    /**
     * @param string $path
     * @param string $contents
     * @return bool
     */
    public function writeToFile(string $path, string $contents): bool
    {
        if ( ! is_dir( dirname( $path ) ) )
            mkdir( dirname( $path ), 0777, true );

        $write_handle = fopen( $path , 'w' );

        $results = fwrite( $write_handle, $contents );

        fclose( $write_handle );

        return ( bool ) $results;
    }

    /**
     * @throws ReflectionException
     */
    public function get_classes_from_folder(string $path): array
    {
        $files = glob( $path . DIRECTORY_SEPARATOR . '*.php' );
        global $container;
        $classes = [];
        foreach ( $files as $file )
        {
            if ( is_file( $file ) )
            {
                $namespace = str_replace( '.php', '', trim( str_replace( getcwd(), '', $file ), '\\' ) );
                $full_name = trim( str_replace( '/', '\\', $namespace ), '\\' );
                if ( ! class_exists( $full_name ) )
                    continue;

                $classes[] = $container->Resolve( $full_name );
            }
            else if ( is_dir( $file ) )
            {
                $instances = $this->get_classes_from_folder( $file );
                foreach ( $instances as $instance )
                    $classes[] = $instance;
            }
        }

        return $classes;
    }
}