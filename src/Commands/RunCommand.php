<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Cli\Commands;

use Artister\System\Boot\LauncherProperties;
use Artister\System\Command\ICommand;
use Artister\System\Event\EventArgs;
use Artister\System\ConsoleColor;
use Artister\System\Console;

class RunCommand implements ICommand
{

    public function execute(object $sender, EventArgs $event) : void
    {
        // default main class name need to be inhetrited from sittings, noted to be added later
        if (file_exists(getcwd()."/vendor/autoload.php"))
        {
            require getcwd()."/vendor/autoload.php";
        }

        $workspace =  getcwd();
        $mainClass = "Application\Program";
        $arguments = $event->getAttribute('arguments');
        $help      = $arguments->getOption('--help');
        
        if ($help)
        {
            $this->showHelp();
        }

        $args = $arguments->Values;
        $project = $arguments->getOption('--project');

        if ($project) {
            if ( $project->Value) {
                $workspace = $project->Value;
                $loader = LauncherProperties::getLoader();
                $loader->setWorkspace($workspace);
                foreach ($args as $key => $arg) {
                    if ($arg == $project->Name) {
                        unset($args[$key]);
                        unset($args[$key+1]);
                        $args = array_values($args);
                        break;
                    }
                }
            }
        }

        $mainClass = ucwords($mainClass, "\\");

        if (!class_exists($mainClass)) {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeline("Couldn't find the class {$mainClass} in ". $workspace);
            Console::resetColor();
            exit;
        }

        if (!method_exists($mainClass, 'main')) {
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
