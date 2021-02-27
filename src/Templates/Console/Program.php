<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Templates\Console;

use Artister\System\Command\Parser\CommandParser;
use Artister\System\StringBuilder;
use Artister\System\ConsoleColor;
use Artister\System\Console;

class Program
{
    public static function main(array $args = [])
    {
        $rootPath  = getcwd();
        $className = "Program";
        $basePath  = null;
        $namespace = "Application";

        $parser = new CommandParser();
        $parser->addOption('--main');
        $parser->addOption('--project');
        $arguments = $parser->parse($args);

        $nameOption = $arguments->getOption('--main');
        if ($nameOption)
        {
            $className = $nameOption->Value;
        }

        if (!$className)
        {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeline("class Name not found, maybe forget to enter class name using the option --main");
            Console::resetColor();
            exit;
        }

        $projectOption = $arguments->getOption('--project');
        if ($projectOption)
        {
            $basePath = $projectOption->Value;
        }

        $path = implode("/", [$rootPath, $basePath]);

        if (!is_dir($path))
        {
            mkdir($path, 0777, true);
        }

        $result = self::createClass($path, $namespace, $className);

        if ($result)
        {
            self::copyFile( __DIR__."/project.phproj", $path."/project.phproj");
        }

        if ($result)
        {
            Console::foregroundColor(ConsoleColor::Green);
            Console::writeline("The template 'Console Application' was created successfully.");
            Console::resetColor();
        }
    }

    public static function createClass($path, $namespace, $className) : bool
    {
        $namespace = ucwords($namespace, "\\");
        $className = ucfirst($className);

        $context = new StringBuilder();
        $context->appendLine("<?php");
        $context->appendLine();
        $context->appendLine("namespace {$namespace};");
        $context->appendLine();
        $context->appendLine("use Artister\System\Console;");
        $context->appendLine();
        $context->appendLine("class {$className}");
        $context->appendLine("{");
        $context->appendLine("    public static function main(array \$args = [])");
        $context->appendLine("    {");
        $context->appendLine("        Console::writeline(\"Hello World!\");");
        $context->appendLine("    }");
        $context->append("}");

        $myfile = fopen($path."/".$className.".php", "w");
        $size   = fwrite($myfile, $context->__toString());
        $status = fclose($myfile);

        if ($size && $status)
        {
            return true;
        }

        return false;
    }

    public static function copyFile($srcfile, $dstfile)
    {
        $dstdir =  dirname($dstfile);
        if (!is_dir($dstdir))
        {
            mkdir($dstdir, 0777, true);
        }

        if (!file_exists($dstfile))
        {
            copy($srcfile, $dstfile);
        }
    }
}
