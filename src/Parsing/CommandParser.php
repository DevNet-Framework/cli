<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Cli\Parsing;

class CommandParser
{
    private array $Arguments = [];
    private array $Options   = [];

    public function addArgument(string $name)
    {
        $this->Arguments[] = $name;
    }

    public function addOption(string $name)
    {
        $this->Options[strtolower($name)] = $name;
    }

    public function parse(array $args): array
    {
        $inputs     = $args;
        $arguments  = $this->Arguments;
        $parameters = [];

        do {
            $token = array_shift($inputs);
            $normalToken = $token ? strtolower($token) : null;

            if (isset($this->Options[$normalToken])) {
                $nextToken = $inputs[0] ?? null;
                $normalNextToken = $nextToken ? strtolower($nextToken) : null;

                if (!isset($this->Options[$normalNextToken])) {
                    $parameters[$normalToken] = new CommandOption($token, $nextToken);
                    array_shift($inputs);
                } else {
                    $parameters[$normalToken] = new CommandOption($token, null);
                }
            } else {
                $argName = $arguments[0] ?? null;
                if ($argName) {
                    $parameters[strtolower($argName)] = new CommandArgument($argName, $token);
                    array_shift($arguments);
                }
            }
        } while ($inputs != []);

        return $parameters;
    }
}
