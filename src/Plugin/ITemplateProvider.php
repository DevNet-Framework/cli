<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\CLI\Plugin;

interface ITemplateProvider
{
    /**
     * Get the template name
     */
    public function getName(): string;

    /**
     * Get the template description
     */
    public function getDescription(): string;

    /**
     * Get the template source path
     */
    public function getPath(): string;
}
