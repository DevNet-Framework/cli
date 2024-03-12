<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\CLI\Plugin;

interface ICodeGeneratorProvider
{
    /**
     * Get the generator name
     */
    public function getName(): string;

    /**
     * Get the generator description
     */
    public function getDescription(): string;

    /**
     * Get the code generator
     */
    public function getGenerator(): ICodeGenerator;
}
