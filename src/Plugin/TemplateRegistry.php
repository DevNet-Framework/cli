<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\CLI\Plugin;

use DevNet\CLI\Commands\AbstractRegistry;

class TemplateRegistry extends AbstractRegistry
{
    private static ?TemplateRegistry $instance = null;

    public static function getSingleton(): static
    {
        if (!static::$instance) {
            static::$instance = new TemplateRegistry(ITemplateProvider::class);
        }

        return static::$instance;
    }
}
