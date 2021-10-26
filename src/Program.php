<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Cli;

use DevNet\Cli\Commands\AddCommand;
use DevNet\Cli\Commands\MigrateCommand;
use DevNet\Cli\Commands\NewCommand;
use DevNet\Cli\Commands\RunCommand;
use DevNet\System\Command\CommandEventArgs;
use DevNet\System\Command\CommandLine;
use DevNet\System\Command\CommandOption;
use DevNet\System\IO\ConsoleColor;
use DevNet\System\IO\Console;

class Program
{
    public static function main(array $args = [])
    {
        $rootCommand = new CommandLine();
        $rootCommand->addCommand(new AddCommand);
        $rootCommand->addCommand(new NewCommand);
        $rootCommand->addCommand(new RunCommand);
        $rootCommand->addCommand(new MigrateCommand);
        $rootCommand->addOption(new CommandOption('--help', '-h'));
        $rootCommand->addOption(new CommandOption('--version', '-v'));
        $rootCommand->Handler->add(new self, 'execute');
        $result = $rootCommand->invoke($args);

        if (!$result) {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeline("The specified command or argument was not found, try 'devnet --help' for more infromation.");
            Console::resetColor();
        }

        exit;
    }

    public function execute(object $sender, CommandEventArgs $args): void
    {
        $help = $args->get('--help');
        if ($help) {
            self::showHelp($sender);
            return;
        }

        $version = $args->get('--version');
        if ($version) {
            self::showVersion();
        }
    }

    public static function showHelp(object $sender): void
    {
        Console::writeline("DevNet command-line interface v1.0.0");
        Console::writeline("Usage: devnet [options]");
        Console::writeline();
        Console::writeline("Options:");
        Console::writeline("  --help      Show command line help.");
        Console::writeline("  --version   Show DevNet Cli version.");
        Console::writeline();
        Console::writeline("Usage: devnet [command] [arguments] [options]");
        Console::writeline();
        Console::writeline("commands:");
        $super = 0;
        $commands = $sender->Commands;

        foreach ($commands as $command) {
            $lenth = strlen($command->Name);
            if ($lenth > $super) {
                $super = $lenth;
            }
        }

        foreach ($commands as $command) {
            $lenth = strlen($command->Name);
            $steps = $super - $lenth + 3;
            $space = str_repeat(" ", $steps);
            Console::writeline("  {$command->Name}$space{$command->Description}");
        }

        Console::writeline();
        Console::writeline("Run 'devnet [command] --help' for more information on a command.");
    }

    public static function showVersion(): void
    {
        Console::writeline("DevNet command-line interface v1.0.0");
        Console::writeline("Copyright (c) DevNet");
    }
}
