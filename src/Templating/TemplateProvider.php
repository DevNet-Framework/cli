<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Cli\Templating;

class TemplateProvider implements ITemplateProvider
{
    protected string $name;
    protected string $description;
    protected string $sourcePath;

    public function __construct(string $name, string $description, string $sourcePath)
    {
        $this->name = $name;
        $this->description = $description;
        $this->sourcePath = $sourcePath;
    }

    /**
     * Get the template name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the template description
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the template source path
     */
    public function getSourcePath(): string
    {
        return  $this->sourcePath;
    }
}
