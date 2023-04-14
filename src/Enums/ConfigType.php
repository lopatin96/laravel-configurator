<?php

namespace Atin\LaravelConfigurator\Enums;

enum ConfigType: string
{
    case String = 'string';
    case Integer = 'integer';
    case Float = 'float';
    case Boolean = 'boolean';
    case ArrayOfStrings = 'array of strings';
    case ArrayOfIntegers = 'array of integers';
}
