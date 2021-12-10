<?php

namespace Miqu\Core\Views\FormBuilder;

use eftec\bladeone\BladeOne;
use Exception;
use ReflectionException;

class FormBuilder
{
    /**
     * @var Field[]
     */
    private $fields = [];

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $method = 'post';

    /**
     * @var string
     */
    private $saveText = 'Save';

    /**
     * @var string
     */
    private $cancelText = 'Cancel';

    /**
     * @throws ReflectionException
     */
    public static function fromModel(string $abstract): self
    {
        $instance = app()->make($abstract);
        $builder = new self;
        if (property_exists($instance, 'formBuilder'))
        {
            collect($instance->formBuilder)->each(function($type, $name) use($builder, $instance) {
                $configuration = !is_array($type) ? Field::getDefaultConfiguration($type, $name) : $type;
                $builder->add(
                    self::getField($configuration['type'], $name)
                        ->applyConfiguration($configuration)
                );
            });
        }
        return $builder;
    }

    public function setUrl(string $url): FormBuilder
    {
        $this->url = $url;
        return $this;
    }

    public function setFormMethod(string $method): FormBuilder
    {
        $this->method = $method;
        return $this;
    }

    public function setSaveText(string $text): FormBuilder
    {
        $this->saveText = $text;
        return $this;
    }

    public function setCancelText(string $text): FormBuilder
    {
        $this->cancelText = $text;
        return $this;
    }

    public function add(Field $field): self
    {
        $this->fields[] = $field;
        return $this;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function render(): string
    {
        $engine = new BladeOne(
            __DIR__ . DIRECTORY_SEPARATOR . 'fields',
            BASE_DIRECTORY . \Miqu\Helpers\env('blade.bin_path'),
            \Miqu\Helpers\env('blade.mode')
        );
        $inputs = collect($this->fields)->map(function(Field $field) use ($engine) {
            return $field->render($engine);
        })->join('');
        return $engine->run('form', [
            'inputs' => $inputs,
            'url' => $this->url,
            'save' => $this->saveText,
            'cancel' => $this->cancelText,
            'method' => $this->method,
        ]);
    }

    private static function getField($type, $name): Field
    {
        $fieldBuilder = Field::builder($name);
        switch($type) {
            case 'currency':
                return $fieldBuilder->currency();
            case 'date':
                return $fieldBuilder->date();
            case 'email':
                return $fieldBuilder->email();
            case 'number':
                return $fieldBuilder->number();
            case 'password':
                return $fieldBuilder->password();
            case 'options':
                return $fieldBuilder->select();
            case 'text':
                return $fieldBuilder->text();
            case 'textArea':
                return $fieldBuilder->textArea();
            case 'time':
                return $fieldBuilder->time();
            case 'url':
                return $fieldBuilder->url();
            case 'week':
                return $fieldBuilder->week();
            case 'relation':
                return $fieldBuilder->relation();
            case 'file':
                return $fieldBuilder->file();
        }
        return $fieldBuilder->text();
    }
}