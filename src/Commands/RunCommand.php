<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Cli\Commands;

use DevNet\System\Command\CommandEventArgs;
use DevNet\System\Command\CommandLine;
use DevNet\System\Command\CommandOption;
use DevNet\System\Command\ICommandHandler;
use DevNet\System\Loader\LauncherProperties;
use DevNet\System\IO\ConsoleColor;
use DevNet\System\IO\Console;
use DevNet\System\Loader\MainMethodRunner;

class RunCommand extends CommandLine implements ICommandHandler
{
    public function __construct()
    {
        $this->setName('run');
        $this->setDescription('Run DevNet Application.');
        $this->addOption(new CommandOption('--help', '-h'));
        $this->addOption(new CommandOption('--project', '-p'));
        $this->addHandler($this);
    }

    public function execute(object $sender, CommandEventArgs $args): void
    {
        $workspace =  getcwd();
        $mainClass = "Application\Program";
        $loader    = LauncherProperties::getLoader();
        $help      = $args->get('--help');

        if ($help) {
            $this->showHelp();
        }

        $inputs  = $args->Inputs;
        $project = $args->get('--project');

        if ($project) {
            if ($project->Value) {
                $workspace = $project->Value;
                $loader->setWorkspace($workspace);
                foreach ($inputs as $key => $arg) {
                    if ($arg == $project->Name) {
                        unset($inputs[$key]);
                        unset($inputs[$key + 1]);
                        $inputs = array_values($inputs);
                        break;
                    }
                }
            }
        }

        $projectFile = simplexml_load_file($workspace . "/project.phproj");

        if ($projectFile) {
            $namespace  = $projectFile->properties->namespace;
            $entrypoint = $projectFile->properties->entrypoint;
            $packages   = $projectFile->dependencies->package ?? [];

            if ($namespace && $entrypoint) {
                $namespace  = (string)$namespace;
                $entrypoint = (string)$entrypoint;
                $mainClass  = $namespace . "\\" . $entrypoint;
                $loader->map($namespace, "/");
            }

            foreach ($packages as $package) {
                $include = (string)$package->attributes()->include;
                if (file_exists($workspace . '/' . $include)) {
                    require $workspace . '/' . $include;
                }
            }
        }

        $mainClass = ucwords($mainClass, "\\");

        if (!class_exists($mainClass)) {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeline("Couldn't find the class {$mainClass} in " . $workspace);
            Console::resetColor();
            exit;
        }

        if (!method_exists($mainClass, 'main')) {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeline("Couldn't find the main method to run, Ensure it exists in the class {$mainClass}");
            Console::resetColor();
            exit;
        }

        $runner = new MainMethodRunner($mainClass, $inputs);
        $runner->run();
    }

    public function showHelp(): void
    {
        Console::writeline("Usage: devnet run [arguments] [options]");
        Console::writeline();
        Console::writeline("Options:");
        Console::writeline("  --help     Displays help for this command.");
        Console::writeline("  --project  Path to the project to run.");
        Console::writeline();
        exit;
    }
}
