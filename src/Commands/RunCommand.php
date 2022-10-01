<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Cli\Commands;

use DevNet\System\Command\CommandLine;
use DevNet\System\Command\Help\HelpBuilder;
use DevNet\System\Command\Parsing\Parser;
use DevNet\System\Runtime\LauncherProperties;
use DevNet\System\Runtime\MainMethodRunner;
use DevNet\System\IO\ConsoleColor;
use DevNet\System\IO\Console;

class RunCommand extends CommandLine
{
    public function __construct()
    {
        parent::__construct('run', 'Run a DevNet project');

        $this->addOption('--project', "Path to the project file to run, by default 'project.phproj' in current directory", '-p');
    }

    public function invoke(array $args): void
    {
        $parser = new Parser();

        foreach ($this->getarguments() as $argument) {
            $parser->addArgument($argument);
        }

        foreach ($this->getoptions() as $option) {
            $parser->addOption($option);
        }

        $result = $parser->parse($args);
        $parameters = $result->getOptions();

        $help = $parameters['--help'] ?? null;
        if ($help) {
            $help = new HelpBuilder($this);
            $help->writeDescription();
            $help->writeHeading('Usage:');
            $help->writeLine('  devnet run [options] <additional arguments>');
            $help->writeLine();
            $help->writeOptions();
            $help->writeHeading('Additional Arguments:');
            $help->writeLine('  Arguments that are passed to the executed application.');
            $help->writeLine();
            $help->build()->write();
            return;
        }

        $this->execute($parameters, $result->getUnparsedTokens());
    }

    public function execute(array $parameters, array $arguments): void
    {
        $workspace   =  getcwd();
        $projectPath =  getcwd() . "/project.phproj";
        $mainClass   = "Application\Program";
        $loader      = LauncherProperties::getLoader();
        $project     = $parameters['--project'] ?? null;

        if ($project) {
            if ($project->getValue()) {
                $projectPath = $project->getValue();
                foreach ($arguments as $key => $arg) {
                    if ($arg == $project->Name) {
                        unset($arguments[$key]);
                        unset($arguments[$key + 1]);
                        $arguments = array_values($arguments);
                        break;
                    }
                }
            }
        }

        if (!file_exists($projectPath)) {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeLine("Couldn't find a project to run in {$workspace}, Ensure if it exists, or pass the correct project path using the option --project.");
            Console::resetColor();
            return;
        }

        $workspace = dirname($projectPath);
        $loader->setWorkspace($workspace);

        $projectFile = simplexml_load_file($projectPath);

        if (!$projectFile) {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeLine("Project file type not supported!");
            Console::resetColor();
            return;
        }

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

        $mainClass = ucwords($mainClass, "\\");

        if (!class_exists($mainClass)) {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeLine("Couldn't find the class {$mainClass} in " . $workspace);
            Console::resetColor();
            exit;
        }

        if (!method_exists($mainClass, 'main')) {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeLine("Couldn't find the main method to run, Ensure it exists in the class {$mainClass}");
            Console::resetColor();
            exit;
        }

        $runner = new MainMethodRunner($mainClass, $arguments);
        $runner->run();
    }
}
