<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Cli\Commands;

use DevNet\System\Command\ICommand;

class CommandRegistry extends AbstractRegistry
{
    private static ?CommandRegistry $instance = null;

    public static function getSingleton(): static
    {
        if (!static::$instance) {
            static::$instance = new CommandRegistry(ICommand::class);
        }

        return static::$instance;
    }
}