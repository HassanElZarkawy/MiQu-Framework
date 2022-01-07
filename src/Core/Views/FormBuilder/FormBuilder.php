<?php

namespace Miqu\Core\Views\FormBuilder;

use eftec\bladeone\BladeOne;
use Exception;
use Illuminate\Database\Eloquent\Model;

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
     * @var string
     */
    private $enctype = 'multipart/form-data';

    /**
     * @throws Exception
     */
    public static function fromModel($abstract): self
    {
        if (is_string($abstract))
            $instance = app()->make($abstract);
        else if ($abstract instanceof Model)
            $instance = $abstract;
        else
            throw new Exception('Only a fully qualified name of a model or the model itself are only allowed.');

        $builder = new self;
        $is_editing = $abstract instanceof Model;
        if (method_exists($instance, 'formDefinitions'))
        {
            collect($instance->formDefinitions())->each(function($field) use($builder, $instance, $is_editing) {
                try {
                    $configuration = $field->getConfiguration();
                    if ($is_editing) {
                        $configuration['value'] = static::getPropertyValue($configuration['name'], $instance);
                    }
                    $builder->add(
                        self::getField($configuration['type'], $configuration['name'])
                            ->setConfiguration($configuration)
                    );
                } catch (Exception $e) {
                    // dd($e);
                }
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

    public function enctype(string $enctype): FormBuilder
    {
        $this->enctype = $enctype;
        return $this;
    }

    public function add(IField $field): self
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
        $inputs = collect($this->fields)->map(function($field) use ($engine) {
            return $field->render($engine);
        })->join('');
        return $engine->run('form', [
            'inputs' => $inputs,
            'url' => $this->url,
            'save' => $this->saveText,
            'cancel' => $this->cancelText,
            'method' => $this->method,
            'enctype' => $this->enctype,
        ]);
    }

    private static function getField($type, $name): IField
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
            case 'select':
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

    private static function getPropertyValue(string $name, Model $instance)
    {
        if ($instance->getAttribute($name) !== null)
            return $instance->{$name};
        return null;
    }
}