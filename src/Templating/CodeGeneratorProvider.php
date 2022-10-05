<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Cli\Templating;

class CodeGeneratorProvider implements ICodeGeneratorProvider
{
    protected string $name;
    protected string $description;
    protected ICodeGenerator $generator;

    public function __construct(string $name, string $description, ICodeGenerator $generator)
    {
        $this->name = $name;
        $this->description = $description;
        $this->generator = $generator;
    }

    /**
     * Get the generator name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the generator description
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the code generator
     */
    public function getGenerator(): ICodeGenerator
    {
        return $this->generator;
    }
}
