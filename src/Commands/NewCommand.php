<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Cli\Commands;

use DevNet\Cli\ICommand;
use DevNet\System\Event\EventArgs;
use DevNet\System\StringBuilder;
use DevNet\System\ConsoleColor;
use DevNet\System\Console;

class NewCommand implements ICommand
{
    public function execute(object $sender, EventArgs $event) : void
    {
        $namespace = "Application";
        $className = "Program";
        $basePath  = null;
        $arguments = $event->getAttribute('arguments');
        $template  = $arguments->getParameter('template');
        $help      = $arguments->getOption('--help');
        $project   = $arguments->getOption('--project');

        if ($help)
        {
            $this->showHelp();
        }

        if (!$template || !$template->Value)
        {
            Console::foreGroundColor(ConsoleColor::Red);
            Console::writeline("Template argument is missing!");
            Console::resetColor();
            exit;
        }

        $project = $arguments->getOption('--project');
        if ($project)
        {
            $basePath = $project->Value;
        }

        $destination  = implode("/", [getcwd(), $basePath]);
        $templateName = $template->Value ?? '';
        $templateName = strtolower($templateName);
        $result       = false;

        switch ($templateName)
        {
            case 'console':
                $result = self::createProject($namespace, $className, $destination);
                break;
            case 'web':
                $rootDir = dirname(__DIR__, 3);
                $result  = self::copyProject($rootDir."/web-project", $destination);
                break;
            case 'mvc':
                $rootDir = dirname(__DIR__, 3);
                $result  = self::copyProject($rootDir."/mvc-project", $destination);
                break;
        }

        if ($result)
        {
            Console::foregroundColor(ConsoleColor::Green);
            Console::writeline("The {$templateName} template was created successfully.");
            Console::resetColor();
        }
        else
        {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeline("Somthing whent wrong! faild to create {$templateName} template.");
            Console::resetColor();
        }

        exit;
    }

    public static function createProject(string $namespace, string $className, string $destination) : bool
    {
        $namespace = ucwords($namespace, "\\");
        $className = ucfirst($className);

        if (!is_dir($destination))
        {
            mkdir($destination, 0777, true);
        }

        $context = new StringBuilder();
        $context->appendLine("<?php");
        $context->appendLine();
        $context->appendLine("namespace {$namespace};");
        $context->appendLine();
        $context->appendLine("use DevNet\System\Console;");
        $context->appendLine();
        $context->appendLine("class {$className}");
        $context->appendLine("{");
        $context->appendLine("    public static function main(array \$args = [])");
        $context->appendLine("    {");
        $context->appendLine("        Console::writeline(\"Hello World!\");");
        $context->appendLine("    }");
        $context->append("}");

        $myfile = fopen($destination."/".$className.".php", "w");
        $size   = fwrite($myfile, $context->__toString());
        $status = fclose($myfile);

        if (!$size || !$status)
        {
            return false;
        }

        $context = new StringBuilder();
        $context->appendLine("<?xml version=\"1.0\"?>");
        $context->appendLine("<project>");
        $context->appendLine("  <properties>");
        $context->appendLine("    <namespace>{$namespace}</namespace>");
        $context->appendLine("    <entrypoint>{$className}</entrypoint>");
        $context->appendLine("  </properties>");
        $context->appendLine("</project>");

        $myfile = fopen($destination."/project.phproj", "w");
        $size   = fwrite($myfile, $context->__toString());
        $status = fclose($myfile);

        if (!$size || !$status)
        {
            return false;
        }

        return true;
    }

    public static function copyProject(string $src, string $dst) : bool
    {
        $dir = opendir($src);

        if (!$dir)
        {
            return false;
        }
        
        if (!is_dir($dst))
        {
            mkdir($dst, 0777, true);
        }
        
        try
        {
            while($file = readdir($dir))
            {
                if ($file !== '.' && $file !== '..' && $file !== 'composer.json')
                {  
                    if (is_dir($src . '/' . $file))
                    {
                        self::copyProject($src . '/' . $file, $dst . '/' . $file);  
                    }  
                    else
                    {  
                        copy($src . '/' . $file, $dst . '/' . $file);  
                    }  
                }  
            }
        }
        catch(\Throwable $th)
        {
            return false;
        }

        closedir($dir);

        return true;
    }

    public function showHelp()
    {
        Console::writeline("Usage: devnet new [template] [arguments] [options]");
        Console::writeline();
        Console::writeline("Options:");
        Console::writeline("  --help     Displays help for this command.");
        Console::writeline("  --project  Location to place the generated project.");
        Console::writeline();
        Console::writeline("templates:");
        Console::writeline("  console     Console Applicatinon project");
        Console::writeline("  web         Web Applicatinon project");
        Console::writeline("  mvc         MVC Web Applicatinon project");
        Console::writeline();
        exit;
    }
}
