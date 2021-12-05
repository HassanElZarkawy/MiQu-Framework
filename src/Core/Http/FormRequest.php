<?php

namespace Miqu\Core\Http;

abstract class FormRequest extends HttpRequest
{
    /**
     * @var string
     */
    protected $redirect;

    /**
     * @return array
     */
    public abstract function rules(): array;

    /**
     * @return array
     */
    public function valid(): array
    {
        return collect($this->rules())->map(function($condition, $key) {
            return $this->{$key};
        })->filter(function($value) {
            return $value !== null;
        })->all();
    }

    public function getRedirectUri(): string
    {
        return $this->redirect ?? '';
    }
}