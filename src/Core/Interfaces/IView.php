<?php

namespace Miqu\Core\Interfaces;

interface IView
{
    function view( string $view_name ) : void;

    function content() : string;

    function with( array $args ) : IView;
}