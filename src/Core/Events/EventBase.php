<?php

namespace Miqu\Core\Events;

use Exception;
use ReflectionException;
use function class_exists;

/**
 * An event abstract class used to manage event in more OOP way.
 * 
 * @since 1.0
 */
abstract class EventBase
{
    /** @var array Holds the fully qualified name of the listeners for this event */
    protected $listeners = [];

    /**
     * Dispatches to all listeners when this event is triggered
     *
     * @return void
     * @throws ReflectionException
     * @throws Exception
     */
    public function dispatch() : void
    {
        foreach ( $this->listeners as $listener)
        {
            if ( ! class_exists( $listener ) )
                throw new Exception( 'Class ' . $listener . ' does not exists' );

            $instance = app()->make( $listener );

            if ( ! method_exists( $instance, 'handle' ) )
                throw new Exception( 'Every listener class should have a public function handle()' );

            $instance->handle( $this );
        }
    }

    /**
     * A base function that is used to register all the events listeners.
     * 
     * @return void
     */
    public function boot()
    {
        
    }

    /**
     * Gets the count of the listeners for this event
     * 
     * @return int
     */
    public function getListenersCount() : int
    {
        if ( is_array( $this->listeners ) )
            return count( $this->listeners );
        return 0;
    }
}