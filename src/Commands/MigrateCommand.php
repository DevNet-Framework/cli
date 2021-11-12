<?php

/**
 * @author      Mohammed Moussaoui
 * @copyright   Copyright (c) Mohammed Moussaoui. All rights reserved.
 * @license     MIT License. For full license information see LICENSE file in the project root.
 * @link        https://github.com/DevNet-Framework
 */

namespace DevNet\Cli\Commands;

use DevNet\Entity\EntityContext;
use DevNet\Entity\Migration\Migrator;
use DevNet\System\Command\CommandEventArgs;
use DevNet\System\Command\CommandLine;
use DevNet\System\Command\CommandOption;
use DevNet\System\Command\ICommandHandler;
use DevNet\System\Runtime\LauncherProperties;
use DevNet\System\IO\ConsoleColor;
use DevNet\System\IO\Console;

class MigrateCommand extends CommandLine implements ICommandHandler
{
    public function __construct()
    {
        $this->setName('migrate');
        $this->setDescription('Migrate database schema and data.');
        $this->addOption(new CommandOption('--help', '-h'));
        $this->addOption(new CommandOption('--target'));
        $this->addHandler($this);
    }

    public function execute(object $sender, CommandEventArgs $args): void
    {
        if ($args->Residual) {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeline("The specified argument or option is not valid, try '--help' option for usage information.");
            Console::resetColor();
            exit;
        }

        $path = 'Migrations';
        $workspace = getcwd();
        $loader    = LauncherProperties::getLoader();
        $help      = $args->get('--help');
        $target    = $args->get('--target');
        $directory = $args->get('--directory');

        if ($help) {
            $this->showHelp();
        }

        $directory = $args->get('--directory');
        if ($directory) {
            if ($directory->Value) {
                $path = ucwords($directory->Value, '/');
            }
        }

        $projectFile = simplexml_load_file($workspace . "/project.phproj");

        if (!$projectFile) {
            throw new \Exception("Can not find project file: {$workspace}/project.phproj");
        }

        $namespace  = $projectFile->properties->namespace;
        $entrypoint = $projectFile->properties->entrypoint;
        $packages   = $projectFile->dependencies->package ?? [];

        if ($namespace && $entrypoint) {
            $namespace  = (string)$namespace;
            $entrypoint = (string)$entrypoint;
            $loader->map($namespace, "/");
        }

        foreach ($packages as $package) {
            $include = (string)$package->attributes()->include;
            if (file_exists($workspace . '/' . $include)) {
                require $workspace . '/' . $include;
            }
        }

        $entity = null;
        $main = $namespace . "\\" . $entrypoint;

        if (!class_exists($main)) {
            throw new \Exception("Can not find class: {$main}");
        }

        $main::main([]);
        $provider = LauncherProperties::getProvider();
        $entity = $provider->getService(EntityContext::class);

        Console::writeline("Build started...");

        $namespace = $namespace . '\\' . str_replace('/', '\\', $path);
        if ($entity) {
            $migrator = new Migrator($entity->Database, $namespace, $workspace . '/' . $path);
            if ($target) {
                $migrator->migrate($target->Value);
            } else {
                $migrator->migrate();
            }
        } else {
            Console::foregroundColor(ConsoleColor::Red);
            Console::writeline("EntityContext not found.");
            Console::resetColor();
            exit;
        }

        Console::foregroundColor(ConsoleColor::Green);
        Console::writeline("Done.");
        Console::resetColor();
    }

    public function showHelp()
    {
        Console::writeline('Usage: devnet migrate [options]');
        Console::writeline();
        Console::writeline('Options:');
        Console::writeline('  --help     Displays help for this command.');
        Console::writeline('  --target   Displays help for this command.');
        Console::writeline();
        exit;
    }
}
