<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Cli;

use DevNet\Cli\Commands\AddCommand;
use DevNet\Cli\Commands\NewCommand;
use DevNet\Cli\Commands\RunCommand;
use DevNet\Cli\Commands\CommandRegistry;
use DevNet\System\Command\CommandEventArgs;
use DevNet\System\Command\CommandLine;
use DevNet\System\IO\ConsoleColor;
use DevNet\System\IO\Console;

class Program
{
    public static function main(array $args = [])
    {
        $rootCommand = new CommandLine('devnet', 'DevNet command-line interface');
        $rootCommand->addOption('--version', 'Show version information', '-v');
        $rootCommand->setHelp(function ($builder) {
            $builder->useDefaults();
            $builder->writeLine("Run 'devnet [command] --help' for more information on a command.");
        });

        $rootCommand->setHandler(function (object $sender, CommandEventArgs $args): void {
            $version = $args->get('--version');
            if ($version) {
                Console::writeLine("DevNet CLI: 1.0.0");
                return;
            }

            Console::$ForegroundColor = ConsoleColor::Red;
            Console::writeLine("The command 'devnet' cannot be executed alone, try '--help' option for usage information.");
            Console::resetColor();
        });

        $provider = CommandRegistry::getSingleton();

        foreach ($provider as $command) {
            $rootCommand->addCommand($command);
        }

        $rootCommand->addCommand(new AddCommand());
        $rootCommand->addCommand(new NewCommand());
        $rootCommand->addCommand(new RunCommand());

        $rootCommand->invoke($args);
    }
}
