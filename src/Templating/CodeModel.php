<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Cli\Templating;

class CodeModel
{
    protected string $fileName;
    protected string $content;

    public function __construct(string $fileName, string $content)
    {
        $this->fileName = $fileName;
        $this->content = $content;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
