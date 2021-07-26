<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Cli;

use DevNet\System\Event\EventArgs;

class CommandEventArgs extends EventArgs
{
    protected array $Parameters = [];
    protected array $Inputs = [];

    public function __construct(array $parameters = [], array $inputs = [])
    {
        $this->Parameters = $parameters;
        $this->Inputs = $inputs;
    }

    public function get(string $name)
    {
        return $this->Parameters[$name] ?? null;
    }
}
