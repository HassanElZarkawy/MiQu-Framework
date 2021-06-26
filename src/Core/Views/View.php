<?php

namespace Miqu\Core\Views;

use Miqu\Core\Interfaces\IView;
use Miqu\Core\Interfaces\IViewEngine;
use Carbon\Carbon;
use DateTime;
use Exception;
use ReflectionException;

class View implements IView
{
    protected $view;

    protected $blade;

    protected $arguments = [];

    /**
     * Initialize a new instance of the View class
     *
     * @param string The relative path for the view file in (dot) notation
     * @throws Exception
     */
    public function __construct()
    {
        $this->blade = app()->make( IViewEngine::class );

        $this->set_loader();

        $this->addDirectives();
    }

    /**
    * Gets the content generated by the Blade Engine.
    * @return string
    */
    public function content() : string
    {
        return $this->blade->run( $this->view, $this->arguments );
    }

    /**
    * Sets the arguments passed to the view in Blade Engine
    * @param array $args an assoc array with keys as variable name and value as the variable value.
    * @return View
    */
    public function with( array $args ) : IView
    {
        $this->arguments = $args;
        return $this;
    }

    public function view( string $view_name ) : void
    {
        $this->view = $view_name;
    }

    /**
     * Sets the Dependency Injection class to the Blade Engine.
     * @return void
     * @throws ReflectionException
     */
    private function set_loader() : void
    {
        $this->blade->setInjectResolver( function( $namespace ) {
            return app()->make( $namespace );
        } );
    }

    private function addDirectives()
    {
        $this->blade->directiveRT( 'human_date', function( $expression ){
            $date = null;
            if ( is_numeric( $expression ) )
                $date = date( 'Y-m-d H:i:s', $expression );
            else if ( is_string( $expression ) )
                $date = date( 'Y-m-d H:i:s', strtotime( $expression ) );
            else if ( $expression instanceof DateTime )
                $date = $expression->format( 'Y-m-d H:i:s' );

            if ( $date == null )
                $date = date( 'Y-m-d H:i:s', time() );

            echo Carbon::createFromFormat('Y-m-d H:i:s', $date)->diffForHumans();
        } );

        $this->blade->directiveRT( 'asset', function( string $expression ) {
            echo getBaseUrl() . 'public/' . $expression;
        } );

        $this->blade->directiveRT( 'storage', function( string $expression ) {
            echo getBaseUrl() . 'storage/' . $expression;
        } );

        $this->blade->directiveRT( 'url', function( string $path ) {
            echo string($path)->trimRight('/')->prepend(getBaseUrl());
        } );

        $this->blade->directiveRT('route', function( string $expression ) {
            try {
                router()->getNamedRoute($expression);
            } catch (ReflectionException $e) {
            }
        } );
    }
}