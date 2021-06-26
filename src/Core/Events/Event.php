<?php

namespace Miqu\Core\Events;

/**
 * An event class that binds a certain event to a function.
 * When this event triggers, it'll automatically runs the function
 * 
 * @since 1.0
 */
class Event 
{
    /** @var array Holds events and there callback */
    private static $events = [];

    /**
     * Binds an event name to a callback that'll be called when the event triggers
     *  
     * Sample use case.
     * 
     * Event::listen( 'user.registered', function( $user ) {
     *      // do stuff
     * });
     * 
     * @since 1.0
     * 
     * @param string $name Event name
     * @param callable $callback function that will run when the event triggers
     * 
     * @return void
     */
    public static function listen( string $name, callable $callback ) : void
    {
        self::$events[$name][] = $callback;
    }

    /**
     * Triggers an event by it's name.
     *
     * Sample use case.
     *
     * Event::trigger( 'user.registered', $user );
     *
     * @param string $name Event name
     * @param array|null $argument Arguments passed to the event. Default is null
     *
     * @return void
     * @since 1.0
     *
     */
    public static function trigger( string $name, array $argument = null ) : void 
    {
        if ( !isset( self::$events[ $name ] ) )
            return;

        foreach ( self::$events[ $name ] as $callback )
        {
            if( $argument && is_array( $argument ) ) 
                call_user_func_array( $callback, $argument );
            elseif ( $argument && !is_array( $argument ) )
                call_user_func( $callback, $argument );
            else
                call_user_func( $callback );
        }
    }
}