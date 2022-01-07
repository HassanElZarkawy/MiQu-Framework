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

interface IFieldBuilder
{
    public function currency(): Currency;

    public function date(): Date;

    public function email(): Email;

    public function localDateTime(): LocalDateTime;

    public function number(): Number;

    public function select(): Select;

    public function text(): Text;

    public function textArea(): TextArea;

    public function time(): Time;

    public function url(): Url;

    public function week(): Week;

    public function password(): Password;

    public function relation(): Relation;

    public function file(): File;

    public static function getType(): string;
}