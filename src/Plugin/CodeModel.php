<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\CLI\Plugin;

class CodeModel
{
    protected string $fileName;
    protected string $content;
    protected string $relativePath;

    public function __construct(string $fileName, string $content, string $relativePath = '')
    {
        $this->fileName = $fileName;
        $this->content = $content;
        $this->relativePath = $relativePath;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getRelativePath(): string
    {
        return $this->relativePath;
    }
}
