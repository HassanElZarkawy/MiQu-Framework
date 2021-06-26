<?php

namespace Miqu\Core\Interfaces;

interface IContainer
{
    function Register( string $abstract, $factory );
    function RegisterSingleton( string $abstract, $factory );
    function Resolve( string $abstract );
}