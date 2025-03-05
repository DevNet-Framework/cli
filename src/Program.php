<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\CLI;

use DevNet\CLI\Commands\AddCommand;
use DevNet\CLI\Commands\NewCommand;
use DevNet\CLI\Commands\RunCommand;
use DevNet\CLI\Plugin\CommandRegistry;
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
                Console::writeLine("DevNet CLI: " . static::getVersion());
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

    public static function getVersion(): string
    {
        $json = json_decode(file_get_contents(dirname(__DIR__, 4). "/vendor/composer/installed.json"));
        foreach ($json->packages as $package) {
            if ($package->name == "devnet/cli") {
                return $package->version;
            }
        }

        return "0.0.0";
    }
}
