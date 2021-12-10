<?php

namespace Miqu\Core\Views\FormBuilder;

use Closure;
use eftec\bladeone\BladeOne;
use Exception;
use Miqu\Core\Views\FormBuilder\Types\Currency;
use Miqu\Core\Views\FormBuilder\Types\Date;
use Miqu\Core\Views\FormBuilder\Types\Email;
use Miqu\Core\Views\FormBuilder\Types\File;
use Miqu\Core\Views\FormBuilder\Types\LocalDateTime;
use Miqu\Core\Views\FormBuilder\Types\Number;
use Miqu\Core\Views\FormBuilder\Types\Password;
use Miqu\Core\Views\FormBuilder\Types\Relation;
use Miqu\Core\Views\FormBuilder\Types\Select;
use Miqu\Core\Views\FormBuilder\Types\Text;
use Miqu\Core\Views\FormBuilder\Types\TextArea;
use Miqu\Core\Views\FormBuilder\Types\Time;
use Miqu\Core\Views\FormBuilder\Types\Url;
use Miqu\Core\Views\FormBuilder\Types\Week;

class Field implements IFieldBuilder
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
     * @var Closure
     */
    private $transformer = null;

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

    /**
     * @param string $property
     */
    private function __construct(string $property)
    {
        $this->property = $property;
    }

    public static function builder(string $fieldName): IFieldBuilder
    {
        return new self($fieldName);
    }

    public function applyConfiguration(array $configuration): Field
    {
        if (isset($configuration['label']))
            $this->label($configuration['label']);
        if (isset($configuration['includeLabel']))
            $this->includeLabel((bool)$configuration['includeLabel']);
        if (isset($configuration['id']))
            $this->property = $configuration['id'];
        if (isset($configuration['width']))
            $this->width((int)$configuration['width']);
        if (isset($configuration['required']))
            $this->setRequired((bool)$configuration['required']);
        if (isset($configuration['helpText']))
            $this->setHelpText($configuration['helpText']);
        if (isset($configuration['value']))
            $this->defaultValue($configuration['value']);
        if (isset($configuration['classes']))
            collect($configuration['classes'])->each(function($class) {
                $this->addClass($class);
            });
        return $this;
    }

    /**
     * @param string $type
     * @param string $name
     * @return array
     */
    public static function getDefaultConfiguration(string $type, string $name): array
    {
        return [
            'type' => $type,
            'label' => (string)string($name)->titleize(),
            'id' => $name,
            'name' => $name,
            'width' => 12,
            'required' => false,
            'classes' => [],
            'value' => ''
        ];
    }

    /**
     * @param bool $includeLabel
     * @return Field
     */
    public function includeLabel(bool $includeLabel): self
    {
        $this->includeLabel = $includeLabel;
        return $this;
    }

    /**
     * @param int $width
     * @return Field
     */
    public function width(int $width): self
    {
        $this->width = ($width > 0 && $width <= 12) ? $width : 12;
        return $this;
    }

    /**
     * @param string $class
     * @return Field
     */
    public function addClass(string $class): self
    {
        $this->classes[] = $class;
        return $this;
    }

    /**
     * @param string $value
     * @return Field
     */
    public function defaultValue(string $value): self
    {
        $this->default_value = $value;
        return $this;
    }

    /**
     * @param Closure|null $transformer
     * @return Field
     */
    public function transformer(?Closure $transformer): self
    {
        $this->transformer = $transformer;
        return $this;
    }

    /**
     * @param string $type
     * @return Field
     */
    public function type(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param string|null $label
     * @return Field
     */
    public function label(?string $label): self
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @param bool $required
     * @return Field
     */
    public function setRequired(bool $required): self
    {
        $this->required = $required;
        return $this;
    }

    /**
     * @param string|null $helpText
     * @return Field
     */
    public function setHelpText(?string $helpText): self
    {
        $this->helpText = $helpText;
        return $this;
    }

    /**
     * @param string|null $placeholder
     * @return Field
     */
    public function setPlaceholder(?string $placeholder): self
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function render(BladeOne $blade): string
    {
        return $blade->run($this->type, $this->getConfiguration());
    }

    public function getConfiguration(): array
    {
        $label = $this->label ?? (string)string($this->property)->titleize();
        return [
            'width' => $this->width,
            'label' => $this->includeLabel ? $label : null,
            'id' => $this->property,
            'classes' => collect($this->classes)->join(''),
            'value' => $this->default_value,
            'required' => $this->required,
            'helpText' => $this->helpText,
            'placeholder' => $this->placeholder,
        ];
    }

    public function currency(): Currency
    {
        return new Currency($this->property);
    }

    public function date(): Date
    {
        return new Date($this->property);
    }

    public function email(): Email
    {
        return new Email($this->property);
    }

    public function localDateTime(): LocalDateTime
    {
        return new LocalDateTime($this->property);
    }

    public function number(): Number
    {
        return new Number($this->property);
    }

    public function select(): Select
    {
        return new Select($this->property);
    }

    public function text(): Text
    {
        return new Text($this->property);
    }

    public function textArea(): TextArea
    {
        return new TextArea($this->property);
    }

    public function time(): Time
    {
        return new Time($this->property);
    }

    public function url(): Url
    {
        return new Url($this->property);
    }

    public function week(): Week
    {
        return new Week($this->property);
    }

    public function password(): Password
    {
        return new Password($this->property);
    }

    public function relation(): Relation
    {
        return new Relation($this->property);
    }

    public function file(): File
    {
        return new File($this->property);
    }
}