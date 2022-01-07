<?php

namespace Miqu\Core\Views\FormBuilder\Types;

use eftec\bladeone\BladeOne;
use Exception;
use Miqu\Core\Views\FormBuilder\FieldBuilder;
use Miqu\Core\Views\FormBuilder\FieldConfigurations;
use Miqu\Core\Views\FormBuilder\FieldMembers;
use Miqu\Core\Views\FormBuilder\IField;

class TextArea implements IField
{
    use FieldMembers, FieldConfigurations, FieldBuilder;

    /**
     * @var int
     */
    private $columns = 10;

    /**
     * @var int
     */
    private $rows = 5;

    /**
     * @param string $fieldName
     */
    public function __construct(string $fieldName)
    {
        $this->property = $fieldName;
        $this->type = static::getType();
    }

    /**
     * @param string $property
     * @return null
     */
    public function get(string $property)
    {
        if (property_exists($this, $property))
            return $this->{$property};
        return null;
    }

    public static function getType(): string
    {
        return 'textArea';
    }

    /**
     * @param int $cols
     * @return TextArea
     */
    public function columns(int $cols): TextArea
    {
        $this->columns = $cols;
        return $this;
    }

    /**
     * @param int $rows
     * @return TextArea
     */
    public function rows(int $rows): TextArea
    {
        $this->rows = $rows;
        return $this;
    }

    public function setConfiguration(array $configuration): IField
    {
        static::applyConfiguration($this, $configuration);
        if (isset($configuration['rows']))
            $this->rows($configuration['rows']);
        if (isset($configuration['columns']))
            $this->columns($configuration['columns']);
        return $this;
    }

    /**
     * @param BladeOne $blade
     * @return string
     * @throws Exception
     */
    public function render(BladeOne $blade): string
    {
        $config = $this->getConfiguration();
        $config['rows'] = $this->rows;
        $config['columns'] = $this->columns;
        return $blade->run('textArea', $config);
    }
}