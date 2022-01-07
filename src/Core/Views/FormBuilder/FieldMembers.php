<?php

namespace Miqu\Core\Views\FormBuilder;

trait FieldMembers
{
    /**
     * @var string
     */
    private $property;

    /**
     * @var string|null
     */
    private $label = null;

    /**
     * @var bool
     */
    private $includeLabel = true;

    /**
     * @var int
     */
    private $width = 12;

    /**
     * @var string[]
     */
    private $classes = [];

    /**
     * @var string
     */
    private $default_value = '';

    /**
     * @var string
     */
    private $type = 'text';

    /**
     * @var bool
     */
    private $required = false;

    /**
     * @var string|null
     */
    private $helpText = null;

    /**
     * @var string|null
     */
    private $placeholder = null;
}