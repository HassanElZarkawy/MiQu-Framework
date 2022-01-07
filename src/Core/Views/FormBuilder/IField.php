<?php

namespace Miqu\Core\Views\FormBuilder;

interface IField
{
    public function __construct(string $fieldName);

    public function get(string $property);

    public function setConfiguration(array $configuration): IField;
}