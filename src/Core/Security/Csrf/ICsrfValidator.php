<?php

namespace Miqu\Core\Security\Csrf;

interface ICsrfValidator
{
    function validate() : bool;
}