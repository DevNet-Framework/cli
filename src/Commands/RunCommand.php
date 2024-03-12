<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\CLI\Commands;

use DevNet\System\Command\CommandLine;
use DevNet\System\Command\Help\HelpBuilder;
use DevNet\System\Command\Parsing\Parser;
use DevNet\System\IO\ConsoleColor;
use DevNet\System\IO\Console;
use DevNet\System\Runtime\Launcher;

class RunCommand extends CommandLine
{
    public function __construct()
    {
        parent::__construct('run', 'Run a DevNet project');

        $this->addOption('--project', "Path to the project file to run, by default 'devnet.proj' in current directory", '-p');
    }

    public function invoke(array $args): void
    {
        $parser = new Parser();

        foreach ($this->Arguments as $argument) {
            $parser->addArgument($argument);
        }

        foreach ($this->Options as $option) {
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
        $projectRoot =  getcwd();
        $projectPath =  $projectRoot . "/devnet.proj";
        $mainClass   = "Application\Program";
        $project     = $parameters['--project'] ?? null;

        if ($project) {
            if ($project->Value) {
                $projectPath = $project->Value;
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
            Console::$ForegroundColor = ConsoleColor::Red;
            Console::writeLine("Couldn't find a project to run in {$projectRoot}, Ensure if it exists, or pass the correct project path using the option --project.");
            Console::resetColor();
            return;
        }

        $launcher = Launcher::initialize($projectPath);

        if (!$launcher) {
            Console::$ForegroundColor = ConsoleColor::Red;
            Console::writeLine("Invalid project file format!");
            Console::resetColor();
            return;
        }

        $result = $launcher->launch($arguments);

        switch ($result) {
            case 1:
                Console::$ForegroundColor = ConsoleColor::Red;
                Console::writeLine("Couldn't find the main class {$mainClass} in " . $projectRoot);
                Console::resetColor();
                break;
            case 2:
                Console::$ForegroundColor = ConsoleColor::Red;
                Console::writeLine("Couldn't find the main method to run in the class {$mainClass}");
                Console::resetColor();
                break;
        }
    }
}
