<?php

namespace Miqu\Core\Views\FormBuilder;

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

trait FieldTransformers
{
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