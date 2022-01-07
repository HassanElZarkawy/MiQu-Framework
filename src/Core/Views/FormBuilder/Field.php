<?php

namespace Miqu\Core\Views\FormBuilder;

use eftec\bladeone\BladeOne;
use Exception;

class Field implements IFieldBuilder
{
    use FieldMembers, FieldConfigurations, FieldTransformers, FieldBuilder;

    /**
     * @param string $property
     */
    public function __construct(string $property)
    {
        $this->property = $property;
    }

    public static function builder(string $fieldName): IFieldBuilder
    {
        return new self($fieldName);
    }

    /**
     * @throws Exception
     */
    public function render(BladeOne $blade): string
    {
        return $blade->run($this->type, $this->getConfiguration());
    }

    public static function getType(): string
    {
        return 'text';
    }
}