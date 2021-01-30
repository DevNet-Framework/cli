<?php declare(strict_types = 1);
/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/artister
 */

namespace Artister\Cli;

use Artister\Cli\Commands\NewCommand;
use Artister\Cli\Commands\RunCommand;
use Artister\System\Command\CommandDispatcher;
use Artister\System\Command\CommandLine;
use Artister\System\ConsoleColor;
use Artister\System\Console;

class Program
{
    public static function main(array $args = [])
    {
        $dispatcher = new CommandDispatcher();

        $dispatcher->addCommand(function(CommandLine $command){
            $command->setName('new');
            $command->setDescription('Create a new project');
            $command->addParameter('template');
            $command->addOption('--help');
            $command->OnExecute(new NewCommand(), 'execute');
        });

        $dispatcher->addCommand(function(CommandLine $command){
            $command->setName('run');
            $command->setDescription('Run a DevNet applicaton');
            $command->addOption('--project');
            $command->addOption('--help');
            $command->OnExecute(new RunCommand(), 'execute');
        });

        self::processArgs($dispatcher, $args);

    }

    public static function processArgs(CommandDispatcher $dispatcher, array $args) : void
    {
        $argument = $args[0] ?? null;

        if ($argument == '--path')
        {
            self::showPath();
        }

        if ($argument == '--version')
        {
            self::showVersion();
        }

        if ($argument == '--help' || $argument == null)
        {
            self::showHelp($dispatcher);
        }

        $result = $dispatcher->invoke($args);

        if (!$result)
        {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeline("Command not found, try --help option for more informations");
            Console::resetColor();
            Console::writeline();
            exit;
        }
    }

    public static function showHelp(CommandDispatcher $dispatcher) : void
    {
        Console::writeline("DevNet command line interpreter v1.0.0");
        Console::writeline("Usage: devnet [options]");
        Console::writeline();
        Console::writeline("Options:");
        Console::writeline("  --help      Show command line help.");
        Console::writeline("  --version   Show DevNet Cli version.");
        Console::writeline("  --path      Show DevNet runtime path.");
        Console::writeline();
        Console::writeline("Usage: devnet [command] [arguments] [options]");
        Console::writeline();
        Console::writeline("commands:");
        $super = 0;
        $commands = $dispatcher->Commands;

        foreach ($commands as $command)
        {
            $lenth = strlen($command->getName());
            if ($lenth > $super) {
                $super = $lenth;
            }
        }

        foreach ($commands as $command)
        {
            $lenth = strlen($command->getName());
            $steps = $super - $lenth + 3;
            $space = str_repeat(" ", $steps);
            Console::writeline("  {$command->getName()}$space{$command->getDescription()}");
        }

        Console::writeline();
        Console::writeline("Run 'devnet [command] --help' for more information on a command.");
        exit;
    }

    public static function showVersion() : void
    {
        Console::writeline("DevNet command line interpreter v1.0.0");
        Console::writeline("Copyright (c) Artister");
        exit;
    }

    public static function showPath() : void
    {
        Console::writeline(dirname(__DIR__,2));
        exit;
    }
}
