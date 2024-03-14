<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\CLI\Plugin;

class CodeGeneratorRegistry extends AbstractRegistry
{
    private static ?CodeGeneratorRegistry $instance = null;

    public static function getSingleton(): static
    {
        if (!static::$instance) {
            static::$instance = new CodeGeneratorRegistry(ICodeGeneratorProvider::class);
        }

        return static::$instance;
    }
}
