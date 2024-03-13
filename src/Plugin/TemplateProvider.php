<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\CLI\Plugin;

class TemplateProvider implements ITemplateProvider
{
    protected string $name;
    protected string $description;
    protected string $path;

    public function __construct(string $sourcePath)
    {
        $this->path = $sourcePath;
        if (!is_file($sourcePath . "/composer.json")) {
            throw new \Exception("Cannot find 'composer.json' in the template path : " . $sourcePath);
        }

        $content = file_get_contents($sourcePath . "/composer.json");
        $package = json_decode($content);
        
        if (!isset($package->name)) {
            throw new \Exception("The template package name is mission in : " . $sourcePath . "/composer.json");
        }

        $segments = explode('/', $package->name);
        if (count($segments) != 2) {
            throw new \Exception("The template package name has an invalid format in : " . $sourcePath . "/composer.json");
        }

        if (!isset($package->description)) {
            throw new \Exception("The template package description is mission in : " > $sourcePath . "/composer.json");
        }

        $this->name = $segments[1];
        $this->description = $package->description;
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
    public function getPath(): string
    {
        return  $this->path;
    }
}
