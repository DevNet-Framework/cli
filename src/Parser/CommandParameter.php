<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Cli\Parser;

class CommandParameter
{
    private string $Name;
    public ?string $Value;

    public function __construct(string $name, ?string $value)
    {
        $this->Name = $name;
        $this->Value = $value;
    }

    public function __get(string $name)
    {
        return $this->$name;
    }
}
