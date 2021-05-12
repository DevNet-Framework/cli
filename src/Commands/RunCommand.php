<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Cli\Commands;

use DevNet\Cli\ICommand;
use DevNet\System\Boot\LauncherProperties;
use DevNet\System\Event\EventArgs;
use DevNet\System\ConsoleColor;
use DevNet\System\Console;

class RunCommand implements ICommand
{
    public function execute(object $sender, EventArgs $event) : void
    {
        $workspace =  getcwd();
        $mainClass = "Application\Program";
        $loader    = LauncherProperties::getLoader();
        $arguments = $event->getAttribute('arguments');
        $help      = $arguments->getOption('--help');
        
        if ($help)
        {
            $this->showHelp();
        }

        $args = $arguments->Values;
        $project = $arguments->getOption('--project');

        if ($project)
        {
            if ( $project->Value)
            {
                $workspace = $project->Value;
                $loader->setWorkspace($workspace);
                foreach ($args as $key => $arg)
                {
                    if ($arg == $project->Name)
                    {
                        unset($args[$key]);
                        unset($args[$key+1]);
                        $args = array_values($args);
                        break;
                    }
                }
            }
        }

        if (file_exists($workspace."/vendor/autoload.php"))
        {
            require $workspace."/vendor/autoload.php";
        }

        $projectFile = simplexml_load_file($workspace."/project.phproj");

        if ($projectFile)
        {
            $namespace = (string)$projectFile->properties->namespace;
            $entrypoint = (string)$projectFile->properties->entrypoint;
            if ($namespace != "" && $entrypoint != "")
            {
                $mainClass = $namespace."\\".$entrypoint;
                $loader->map($namespace, "/");
            }
        }

        $mainClass = ucwords($mainClass, "\\");

        if (!class_exists($mainClass))
        {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeline("Couldn't find the class {$mainClass} in ". $workspace);
            Console::resetColor();
            exit;
        }

        if (!method_exists($mainClass, 'main'))
        {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeline("Couldn't find the main method to run, Ensure it exists in the class {$mainClass}");
            Console::resetColor();
            exit;
        }

        $mainClass::main($args);
    }

    public function showHelp() : void
    {
        Console::writeline("Usage: devnet run [options]");
        Console::writeline();
        Console::writeline("Options:");
        Console::writeline("  --help     Displays help for this command.");
        Console::writeline("  --project  Path to the project to run.");
        Console::writeline();
        exit;
    }
}
